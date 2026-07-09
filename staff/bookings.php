<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $type = $_POST['type'];
    $status = $_POST['status'];
    $notes = $_POST['notes'] ?? '';

    if ($type === 'facility') {
        $stmt = $pdo->prepare("SELECT user_id, status FROM bookings WHERE id = ?");
        $stmt->execute([$id]);
        $booking = $stmt->fetch();
        
        if ($booking && $booking['status'] !== $status) {
            // If the status is changing to cancelled/rejected, we need to update equipment bookings too
            if (in_array($booking['status'], ['pending', 'approved']) && in_array($status, ['cancelled', 'rejected'])) {
                $eq_stmt = $pdo->prepare("SELECT id, equipment_id, quantity, status FROM equipment_bookings WHERE booking_id = ?");
                $eq_stmt->execute([$id]);
                $assoc_eq = $eq_stmt->fetchAll();
                
                $update_eq_status = $pdo->prepare("UPDATE equipment_bookings SET status=?, modified=NOW() WHERE id=?");
                $restore_eq_inv = $pdo->prepare("UPDATE equipments SET available_quantity = available_quantity + ? WHERE id = ?");
                
                foreach ($assoc_eq as $eq) {
                    if (in_array($eq['status'], ['pending', 'approved'])) {
                        $restore_eq_inv->execute([$eq['quantity'], $eq['equipment_id']]);
                        $update_eq_status->execute([$status, $eq['id']]);
                    }
                }
            } else if (in_array($booking['status'], ['cancelled', 'rejected']) && in_array($status, ['pending', 'approved'])) {
                $eq_stmt = $pdo->prepare("SELECT id, equipment_id, quantity, status FROM equipment_bookings WHERE booking_id = ?");
                $eq_stmt->execute([$id]);
                $assoc_eq = $eq_stmt->fetchAll();
                
                $update_eq_status = $pdo->prepare("UPDATE equipment_bookings SET status=?, modified=NOW() WHERE id=?");
                $deduct_eq_inv = $pdo->prepare("UPDATE equipments SET available_quantity = available_quantity - ? WHERE id = ?");
                
                foreach ($assoc_eq as $eq) {
                    if (in_array($eq['status'], ['cancelled', 'rejected'])) {
                        $deduct_eq_inv->execute([$eq['quantity'], $eq['equipment_id']]);
                        $update_eq_status->execute([$status, $eq['id']]);
                    }
                }
            }
        }
        
        $stmt = $pdo->prepare("UPDATE bookings SET status=?, notes=?, modified=NOW() WHERE id=?");
        $stmt->execute([$status, $notes, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE equipment_bookings SET status=?, modified=NOW() WHERE id=?");
        $stmt->execute([$status, $id]);
    }

    $auditStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address, created) VALUES (?, ?, ?, ?, NOW())");
    $auditStmt->execute([$_SESSION['user_id'], 'Update Booking', "Changed " . $type . " booking #" . $id . " status to " . $status . ".", $_SERVER['REMOTE_ADDR'] ?? '']);

    setFlash(ucfirst($type) . ' booking updated.');
    header('Location: bookings.php'); exit;
}

$action = $_GET['action'] ?? 'index';
$title = 'Manage Bookings';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

if ($action === 'index'):
    $bookings = $pdo->query("SELECT b.*, u.name as user_name, u.avatar as user_avatar, f.name as facility_name, t.start_time, t.end_time,
                               (SELECT GROUP_CONCAT(CONCAT(eb.quantity, 'x ', e.name) SEPARATOR ', ')
                                FROM equipment_bookings eb 
                                JOIN equipments e ON eb.equipment_id = e.id 
                                WHERE eb.booking_id = b.id) as equipment_details
                             FROM bookings b 
                             JOIN users u ON b.user_id = u.id 
                             JOIN facilities f ON b.facility_id = f.id 
                             LEFT JOIN availability t ON b.timeslot_id = t.id 
                             ORDER BY b.booking_date DESC, t.start_time DESC")->fetchAll();
?>
<div class="sb-page-header">
    <h2><?= render_icon('calendar', '', 20) ?> Manage Bookings</h2>
    <div style="margin-left: auto; display: flex; gap: 1rem; align-items: center;">
        <div class="sb-search-wrap" style="position:relative;">
            <input type="text" id="bookingSearch" placeholder="Search bookings..." class="sb-form-input" style="padding-left: 32px; width: 250px;">
            <span style="position:absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--muted-foreground);">
                <?= render_icon('search', '', 16) ?>
            </span>
        </div>
        <button onclick="openExportModal()" class="sb-btn sb-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Export to PDF
        </button>
    </div>
</div>
<div class="sb-tabs sb-fade">
    <button class="sb-tab-btn active" data-filter="all">All Bookings</button>
    <button class="sb-tab-btn" data-filter="pending">Pending</button>
    <button class="sb-tab-btn" data-filter="approved">Approved</button>
    <button class="sb-tab-btn" data-filter="rejected">Rejected</button>
</div>
<div class="sb-card sb-fade" id="fac-card">
    <div class="sb-table-wrap">
    <table class="sb-table">
        <thead><tr><th>Student</th><th>Facility</th><th>Equipment</th><th>Date</th><th>Timeslot</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($bookings as $b): ?>
        <tr class="sb-booking-row" data-status="<?= htmlspecialchars($b['status']) ?>">
            <td>
                <div class="sb-table-avatar">
                    <?php if(!empty($b['user_avatar'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($b['user_avatar']) ?>" alt="avatar" class="sb-mini-avatar" style="padding:0; object-fit:cover; border:none;">
                    <?php else: ?>
                        <div class="sb-mini-avatar"><?= strtoupper(substr($b['user_name'],0,1)) ?></div>
                    <?php endif; ?>
                    <strong style="white-space: nowrap;"><?= htmlspecialchars($b['user_name']) ?></strong>
                </div>
            </td>
            <td><?= htmlspecialchars($b['facility_name']) ?></td>
            <td>
                <?php if ($b['equipment_details']): ?>
                    <span style="color:var(--muted-foreground); font-size:0.9rem;"><?= htmlspecialchars($b['equipment_details']) ?></span>
                <?php else: ?>
                    <span style="color:var(--border);">—</span>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($b['booking_date']) ?></td>
            <td><?= htmlspecialchars($b['start_time'] ? substr($b['start_time'],0,5).' - '.substr($b['end_time'],0,5) : '—') ?></td>
            <td><span class="sb-badge sb-badge-<?= htmlspecialchars($b['status']) ?>"><?= htmlspecialchars($b['status']) ?></span></td>
            <td class="sb-actions">
                <a href="?action=edit&type=facility&id=<?= $b['id'] ?>" class="sb-btn sb-btn-sm sb-btn-outline" title="Review"><?= render_icon('edit', '', 14) ?> Review</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr class="sb-empty-state-premium" id="fac-empty" style="display: <?= empty($bookings) ? 'table-row' : 'none' ?>;">
            <td colspan="7">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-check"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="m9 16 2 2 4-4"/></svg>
                <p>No facility bookings found.</p>
            </td>
        </tr>
        </tbody>
    </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.sb-tab-btn');
    const rows = document.querySelectorAll('.sb-booking-row');
    const facEmpty = document.getElementById('fac-empty');
    const searchInput = document.getElementById('bookingSearch');

    function filterBookings() {
        const activeTab = document.querySelector('.sb-tab-btn.active');
        const status = activeTab ? activeTab.getAttribute('data-filter') : 'all';
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';

        let facCount = 0;

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const rowContent = row.textContent.toLowerCase();
            
            const isMatch = (status === 'all' || rowStatus === status) && (searchTerm === '' || rowContent.includes(searchTerm));
            
            if (isMatch) {
                row.style.display = 'table-row';
                facCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (facEmpty) facEmpty.style.display = facCount === 0 ? 'table-row' : 'none';
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            filterBookings();
        });
    });
    
    if (searchInput) {
        searchInput.addEventListener('input', filterBookings);
    }
    
    // Initial filter check
    filterBookings();
});
</script>

<!-- Premium Export Modal -->
<div id="exportModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(12px); align-items: center; justify-content: center; z-index: 1000; opacity: 0; transition: opacity 0.3s ease;">
    <div class="premium-modal-card" style="width: 420px; max-width: 90%; background: linear-gradient(145deg, #1e293b, #0f172a); border-radius: 20px; border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5), 0 0 40px rgba(56, 189, 248, 0.1); overflow: hidden; transform: translateY(20px); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
        
        <div style="padding: 24px 24px 16px 24px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="background: rgba(56, 189, 248, 0.1); color: #38bdf8; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <?= render_icon('calendar', '', 20) ?>
                </div>
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: #f8fafc;">Export Report</h3>
            </div>
            <button onclick="closeExportModal()" style="background: rgba(255,255,255,0.05); border: none; font-size: 1.2rem; cursor: pointer; color: #94a3b8; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">&times;</button>
        </div>
        
        <div style="padding: 24px;">
            <form action="<?= BASE_URL ?>/export_pdf.php" method="GET">
                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; color: #94a3b8; font-size: 0.9rem; font-weight: 500;">Select Month and Year</label>
                    <input type="month" name="month" required value="<?= date('Y-m') ?>" 
                           style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #f8fafc; font-size: 1rem; outline: none; transition: border-color 0.2s; color-scheme: dark;">
                </div>
                
                <div style="display: flex; gap: 12px;">
                    <button type="button" onclick="closeExportModal()" style="flex: 1; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #e2e8f0; font-weight: 500; cursor: pointer; transition: background 0.2s;">Cancel</button>
                    <button type="submit" style="flex: 2; padding: 12px; background: linear-gradient(to right, #0ea5e9, #3b82f6); border: none; border-radius: 12px; color: white; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3); transition: transform 0.1s, box-shadow 0.2s;">Download PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openExportModal() {
    const modal = document.getElementById('exportModal');
    const card = modal.querySelector('.premium-modal-card');
    modal.style.display = 'flex';
    void modal.offsetWidth; // Trigger reflow
    modal.style.opacity = '1';
    card.style.transform = 'translateY(0)';
}
function closeExportModal() {
    const modal = document.getElementById('exportModal');
    const card = modal.querySelector('.premium-modal-card');
    modal.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    setTimeout(() => { modal.style.display = 'none'; }, 300);
}
</script>

<?php elseif ($action === 'edit'): 
    $type = $_GET['type'] ?? 'facility';
    if ($type === 'facility') {
        $stmt = $pdo->prepare("SELECT b.*, u.name as user_name, f.name as facility_name FROM bookings b JOIN users u ON b.user_id=u.id JOIN facilities f ON b.facility_id=f.id WHERE b.id = ?");
    } else {
        $stmt = $pdo->prepare("SELECT b.*, u.name as user_name, e.name as facility_name FROM equipment_bookings b JOIN users u ON b.user_id=u.id JOIN equipments e ON b.equipment_id=e.id WHERE b.id = ?");
    }
    $stmt->execute([$_GET['id']]);
    $b = $stmt->fetch();
?>
<div class="sb-page-header">
    <h2><?= render_icon('edit', '', 20) ?> Review Booking #<?= $b['id'] ?></h2>
    <a href="bookings.php" class="sb-btn sb-btn-ghost">Back</a>
</div>
<div class="sb-form-card sb-fade">
    <div class="sb-form-header"><?= render_icon('calendar', '', 18) ?><h2>Booking Details</h2></div>
    <div class="sb-form-body">
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="type" value="<?= $type ?>">
            <input type="hidden" name="id" value="<?= $b['id'] ?>">
            <div class="sb-form-row">
                <div class="sb-form-group"><label>Student</label><input type="text" class="sb-form-input" disabled value="<?= htmlspecialchars($b['user_name']) ?>"></div>
                <div class="sb-form-group"><label><?= $type === 'facility' ? 'Facility' : 'Equipment' ?></label><input type="text" class="sb-form-input" disabled value="<?= htmlspecialchars($b['facility_name']) ?>"></div>
            </div>
            <div class="sb-form-row">
                <div class="sb-form-group"><label>Date</label><input type="text" class="sb-form-input" disabled value="<?= htmlspecialchars($b['booking_date']) ?>"></div>
                <div class="sb-form-group">
                    <label>Action</label>
                    <select name="status" class="sb-form-input" required>
                        <option value="pending" <?= $b['status']==='pending'?'selected':'' ?>>Pending</option>
                        <option value="approved" <?= $b['status']==='approved'?'selected':'' ?>>Approve</option>
                        <option value="rejected" <?= $b['status']==='rejected'?'selected':'' ?>>Reject</option>
                    </select>
                </div>
            </div>
            <?php if ($type === 'facility'): ?>
            <div class="sb-form-group">
                <label>Staff Notes</label>
                <textarea name="notes" class="sb-form-input" rows="3"><?= htmlspecialchars($b['notes'] ?? '') ?></textarea>
            </div>
            <?php endif; ?>
            <div class="sb-form-actions">
                <button type="submit" class="sb-btn sb-btn-primary"><?= render_icon('check', '', 15) ?> Save Decision</button>
            </div>
        </form>
    </div>
</div>
<?php endif; require_once __DIR__ . '/../includes/footer.php'; ?>
