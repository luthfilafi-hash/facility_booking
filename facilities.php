<?php
$title = "All Facilities";
require_once __DIR__ . '/includes/header_public.php';

$facilities = $pdo->query("SELECT * FROM facilities ORDER BY name ASC")->fetchAll();
?>

<div class="sb-hero" style="min-height: auto; padding: 3rem 2.5rem 1rem; background: linear-gradient(135deg, rgba(15,17,26,0.95), rgba(15,17,26,0.8));">
    <div class="sb-container">
        <a href="<?= BASE_URL ?>/index.php" class="sb-btn sb-btn-outline sb-btn-sm" style="border-radius: 999px; margin-bottom: 1.5rem; display: inline-flex; border-color: rgba(255,255,255,0.2);">
            &larr; Back to Homepage
        </a>
        <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem; text-align: left;">All Facilities</h1>
        <p style="color: var(--muted-foreground); font-size: 1.1rem; text-align: left;">Browse our complete list of premium sports facilities</p>
    </div>
</div>

<section class="sb-section">
    <div class="sb-container">
        <div class="sb-facilities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <?php foreach($facilities as $f): ?>
            <div class="sb-facility-card" style="border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.03); transition: all 0.2s;">
                <?php if (!empty($f['image_path'])): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($f['image_path']) ?>" alt="<?= htmlspecialchars($f['name']) ?>" style="width: 100%; height: 200px; object-fit: cover;">
                <?php else: ?>
                    <div class="sb-facility-thumb" style="height: 200px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0a1628, #1a3a4a);">
                        <?= render_icon('building', '', 50) ?>
                    </div>
                <?php endif; ?>
                <div class="sb-facility-body" style="padding: 1.5rem;">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem;"><?= htmlspecialchars($f['name']) ?></h3>
                    <div class="loc" style="display: flex; align-items: center; gap: 0.4rem; color: var(--muted-foreground); font-size: 0.85rem; margin-bottom: 1rem;">
                        <?= render_icon('map-pin', '', 14) ?> <?= htmlspecialchars($f['location']) ?>
                    </div>
                    <p style="font-size: 0.9rem; color: var(--muted-foreground); margin-bottom: 1.5rem;">Capacity: <?= htmlspecialchars($f['capacity'] ?? 'N/A') ?> people</p>
                    <a href="<?= BASE_URL ?>/availability.php" class="sb-btn sb-btn-outline" style="width: 100%; justify-content: center;">Check Availability</a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if(empty($facilities)): ?>
                <p style="text-align:center; color: var(--muted-foreground); grid-column: 1/-1;">No facilities available.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
