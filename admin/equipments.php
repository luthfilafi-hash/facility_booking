<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM equipments WHERE id = ?");
    if ($stmt->execute([$_POST['id']])) setFlash('Equipment deleted.');
    else setFlash('Failed to delete equipment.', 'error');
    header('Location: equipments.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action']??'', ['add', 'edit'])) {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $facility_id = empty($_POST['facility_id']) ? null : $_POST['facility_id'];
    $quantity = $_POST['quantity'];
    $condition = $_POST['condition'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    if ($_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO equipments (name, facility_id, quantity, `condition`, status, description, created, modified) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$name, $facility_id, $quantity, $condition, $status, $description]);
        setFlash('Equipment added.');
    } else {
        $stmt = $pdo->prepare("UPDATE equipments SET name=?, facility_id=?, quantity=?, `condition`=?, status=?, description=?, modified=NOW() WHERE id=?");
        $stmt->execute([$name, $facility_id, $quantity, $condition, $status, $description, $id]);
        setFlash('Equipment updated.');
    }
    header('Location: equipments.php'); exit;
}

$action = $_GET['action'] ?? 'index';
$title = 'Equipment';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

if ($action === 'index'):
    $equipments = $pdo->query("SELECT e.*, f.name as facility_name FROM equipments e LEFT JOIN facilities f ON e.facility_id = f.id ORDER BY e.name")->fetchAll();
?>
<div class="sb-page-header">
    <h2><?= render_icon('box', '', 20) ?> Equipment</h2>
    <a href="?action=add" class="sb-btn sb-btn-primary"><?= render_icon('plus', '', 15) ?> Add Equipment</a>
</div>
<div class="sb-card sb-fade">
    <div class="sb-table-wrap">
    <table class="sb-table">
        <thead><tr><th>Name</th><th>Facility</th><th>Qty</th><th>Condition</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($equipments as $e): ?>
        <tr>
            <td><strong><?= htmlspecialchars($e['name']) ?></strong></td>
            <td><?= htmlspecialchars($e['facility_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($e['quantity'] ?? '0') ?></td>
            <td><?= htmlspecialchars($e['condition'] ?? '—') ?></td>
            <td><span class="sb-badge sb-badge-<?= htmlspecialchars($e['status'] ?? 'available') ?>"><?= htmlspecialchars($e['status'] ?? 'available') ?></span></td>
            <td class="sb-actions">
                <a href="?action=edit&id=<?= $e['id'] ?>" class="sb-btn sb-btn-sm sb-btn-outline" title="Edit"><?= render_icon('edit', '', 14) ?></a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete equipment?');">
                    <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $e['id'] ?>">
                    <button type="submit" class="sb-btn sb-btn-sm sb-btn-danger" title="Delete"><?= render_icon('trash', '', 14) ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach; if(empty($equipments)): ?><tr class="sb-table-empty"><td colspan="6">No equipment found.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php elseif ($action === 'add' || $action === 'edit'): 
    $e = null;
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM equipments WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $e = $stmt->fetch();
    }
    $facilities = $pdo->query("SELECT id, name FROM facilities")->fetchAll();
?>
<div class="sb-page-header">
    <h2><?= render_icon($action==='add'?'plus':'edit', '', 20) ?> <?= $action==='add'?'Add':'Edit' ?> Equipment</h2>
    <a href="equipments.php" class="sb-btn sb-btn-ghost">Back</a>
</div>
<div class="sb-form-card sb-fade">
    <div class="sb-form-header"><?= render_icon('box', '', 18) ?><h2>Equipment Details</h2></div>
    <div class="sb-form-body">
        <form method="POST">
            <input type="hidden" name="action" value="<?= $action ?>">
            <?php if($e): ?><input type="hidden" name="id" value="<?= $e['id'] ?>"><?php endif; ?>
            <div class="sb-form-row">
                <div class="sb-form-group"><label>Name</label><input type="text" name="name" class="sb-form-input" required value="<?= htmlspecialchars($e['name'] ?? '') ?>"></div>
                <div class="sb-form-group">
                    <label>Facility</label>
                    <select name="facility_id" class="sb-form-input">
                        <option value="">-- None --</option>
                        <?php foreach($facilities as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= ($e['facility_id']??'')==$f['id']?'selected':'' ?>><?= htmlspecialchars($f['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="sb-form-row">
                <div class="sb-form-group">
                    <label>Quantity</label>
                    <div class="sb-qty-selector">
                        <button type="button" class="sb-qty-btn" onclick="this.parentNode.querySelector('input').stepDown()">-</button>
                        <input type="number" name="quantity" class="sb-qty-input" min="0" value="<?= htmlspecialchars($e['quantity'] ?? '1') ?>">
                        <button type="button" class="sb-qty-btn" onclick="this.parentNode.querySelector('input').stepUp()">+</button>
                    </div>
                </div>
                <div class="sb-form-group">
                    <label>Condition</label>
                    <select name="condition" class="sb-form-input">
                        <option value="excellent" <?= ($e['condition']??'')==='excellent'?'selected':'' ?>>Excellent</option>
                        <option value="good" <?= ($e['condition']??'')==='good'?'selected':'' ?>>Good</option>
                        <option value="fair" <?= ($e['condition']??'')==='fair'?'selected':'' ?>>Fair</option>
                        <option value="poor" <?= ($e['condition']??'')==='poor'?'selected':'' ?>>Poor</option>
                    </select>
                </div>
            </div>
            <div class="sb-form-row">
                <div class="sb-form-group">
                    <label>Status</label>
                    <select name="status" class="sb-form-input">
                        <option value="available" <?= ($e['status']??'')==='available'?'selected':'' ?>>Available</option>
                        <option value="maintenance" <?= ($e['status']??'')==='maintenance'?'selected':'' ?>>Maintenance</option>
                        <option value="unavailable" <?= ($e['status']??'')==='unavailable'?'selected':'' ?>>Unavailable</option>
                    </select>
                </div>
                <div class="sb-form-group"><label>Description</label><textarea name="description" class="sb-form-input" rows="2"><?= htmlspecialchars($e['description'] ?? '') ?></textarea></div>
            </div>
            <div class="sb-form-actions">
                <button type="submit" class="sb-btn sb-btn-premium" id="saveEqBtn">
                    <span class="spinner"></span>
                    <span class="btn-text" style="display:inline-flex;align-items:center;gap:0.5rem;"><?= render_icon('check', '', 15) ?> Save Equipment</span>
                </button>
                <a href="equipments.php" class="sb-btn sb-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const btn = document.getElementById('saveEqBtn');
            if (btn && !btn.classList.contains('is-loading')) {
                e.preventDefault(); // Pause submission to show premium animation
                btn.classList.add('is-loading');
                setTimeout(() => {
                    form.submit();
                }, 800); // 800ms loading showcase
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
