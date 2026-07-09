<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('student');

$title = 'Facilities';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

$facilities = $pdo->query("SELECT * FROM facilities ORDER BY name ASC")->fetchAll();
?>
<div class="sb-page-header">
    <h2><?= render_icon('building', '', 20) ?> Explore Facilities</h2>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(300px, 1fr));gap:2rem;">
    <?php foreach ($facilities as $f): ?>
    <div class="sb-card sb-fade" style="overflow: hidden; display: flex; flex-direction: column; padding: 0;">
        <?php if (!empty($f['image_path'])): ?>
        <div style="width: 100%; height: 220px; background-image: url('<?= BASE_URL ?>/<?= htmlspecialchars($f['image_path']) ?>'); background-size: cover; background-position: center; border-bottom: 1px solid var(--border);"></div>
        <?php else: ?>
        <div style="width: 100%; height: 220px; background-color: var(--card-hover); display: flex; align-items: center; justify-content: center; color: var(--muted-foreground); border-bottom: 1px solid var(--border);">
            <?= render_icon('image', '', 48) ?>
        </div>
        <?php endif; ?>
        <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
            <div class="sb-card-header" style="flex-direction:column;align-items:flex-start;gap:0.5rem; padding: 0; border: none; margin-bottom: 1rem;">
                <div style="display:flex;justify-content:space-between;width:100%;align-items:center;">
                    <h3 style="margin:0;"><?= htmlspecialchars($f['name']) ?></h3>
                    <div style="text-align: right;">
                        <span class="sb-badge sb-badge-<?= htmlspecialchars($f['status']) ?>"><?= htmlspecialchars($f['status']) ?></span>
                        <?php if (in_array($f['status'], ['maintenance', 'unavailable']) && !empty($f['maintenance_end_time'])): ?>
                            <div class="maintenance-countdown" data-end="<?= date('c', strtotime($f['maintenance_end_time'])) ?>" style="font-size: 0.75rem; color: #f59e0b; margin-top: 0.35rem; font-weight: 600;">
                                Ends in...
                            </div>
                        <?php elseif (in_array($f['status'], ['maintenance', 'unavailable']) && !empty($f['maintenance_time'])): ?>
                            <div style="font-size: 0.75rem; color: #f59e0b; margin-top: 0.35rem; font-weight: 600;">
                                <?= date('d M, h:i A', strtotime($f['maintenance_time'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="color:var(--muted-foreground);font-size:0.875rem;display:flex;align-items:center;gap:4px;">
                    <?= render_icon('map-pin', '', 14) ?> <?= htmlspecialchars($f['location']) ?>
                </div>
            </div>
            <div class="sb-card-body" style="padding: 0; display: flex; flex-direction: column; flex-grow: 1;">
                <p style="color:var(--muted-foreground);margin-bottom:1.5rem;min-height:3rem; line-height: 1.6;">
                    <?= htmlspecialchars($f['description'] ?? 'No description available.') ?>
                </p>
                <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--border);padding-top:1.5rem;margin-top:auto;">
                    <div style="font-size:0.875rem;color:var(--muted-foreground);">
                        <strong>Capacity:</strong> <?= htmlspecialchars($f['capacity'] ?? 'N/A') ?>
                    </div>
                    <?php if ($f['status'] === 'available' || (in_array($f['status'], ['maintenance', 'unavailable']) && !empty($f['maintenance_time']))): ?>
                    <a href="book.php?facility_id=<?= $f['id'] ?>" class="sb-btn sb-btn-sm sb-btn-primary">Book Now</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; if(empty($facilities)): ?>
    <p>No facilities available at the moment.</p>
    <?php endif; ?>
</div>

<script>
function updateCountdowns() {
    document.querySelectorAll('.maintenance-countdown').forEach(el => {
        const end = new Date(el.getAttribute('data-end')).getTime();
        const now = new Date().getTime();
        const diff = end - now;
        
        if (diff > 0) {
            const h = Math.floor(diff / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);
            el.innerText = `Ends in ${h}h ${m}m ${s}s`;
        } else {
            el.innerText = 'Finishing up...';
        }
    });
}
setInterval(updateCountdowns, 1000);
updateCountdowns();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
