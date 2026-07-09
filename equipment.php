<?php
$title = "Sports Equipment";
require_once __DIR__ . '/includes/header_public.php';

$equipments = $pdo->query("SELECT * FROM equipments ORDER BY name ASC")->fetchAll();
?>

<div class="sb-hero" style="min-height: auto; padding: 3rem 2.5rem 1rem; background: linear-gradient(135deg, rgba(15,17,26,0.95), rgba(15,17,26,0.8));">
    <div class="sb-container">
        <a href="<?= BASE_URL ?>/index.php" class="sb-btn sb-btn-outline sb-btn-sm" style="border-radius: 999px; margin-bottom: 1.5rem; display: inline-flex; border-color: rgba(255,255,255,0.2);">
            &larr; Back to Homepage
        </a>
        <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem; text-align: left;">Sports Equipment</h1>
        <p style="color: var(--muted-foreground); font-size: 1.1rem; text-align: left;">Browse our premium sports equipment available to borrow during your sessions</p>
    </div>
</div>

<section class="sb-section" style="padding-top: 3rem;">
    <div class="sb-container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <?php foreach($equipments as $eq): ?>
            <div class="sb-eq-card sb-fade" style="text-align: center; padding: 2.5rem 1.5rem; background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); display: flex; flex-direction: column;">
                <?php if (!empty($eq['image_path'])): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($eq['image_path']) ?>" alt="<?= htmlspecialchars($eq['name']) ?>" style="width: 100%; height: 160px; object-fit: cover; border-radius: 8px; margin-bottom: 1.5rem;">
                <?php else: ?>
                    <div class="sb-eq-icon-wrap" style="margin: 0 auto 1.5rem; height: 160px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2); border-radius: 8px;">
                        <?= render_icon('box', '', 48) ?>
                    </div>
                <?php endif; ?>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;"><?= htmlspecialchars($eq['name']) ?></h3>
                <p style="color: var(--muted-foreground); font-size: 0.95rem; margin-bottom: 1.5rem;">Total Quantity: <?= htmlspecialchars($eq['total_quantity']) ?></p>
                
                <div style="margin-top: auto;">
                    <?php if ($eq['available_quantity'] > 0): ?>
                        <div style="display: inline-block; padding: 0.4rem 1rem; border-radius: 999px; background: rgba(16, 185, 129, 0.1); color: #10b981; font-weight: 600; font-size: 0.85rem;">
                            <?= htmlspecialchars($eq['available_quantity']) ?> Available
                        </div>
                    <?php else: ?>
                        <div style="display: inline-block; padding: 0.4rem 1rem; border-radius: 999px; background: rgba(239, 68, 68, 0.1); color: #ef4444; font-weight: 600; font-size: 0.85rem;">
                            Out of Stock
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if(empty($equipments)): ?>
                <p style="text-align:center; color: var(--muted-foreground); grid-column: 1/-1;">No equipment available.</p>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 4rem; padding: 2rem; background: rgba(16, 185, 129, 0.05); border-radius: var(--radius); border: 1px solid rgba(16, 185, 129, 0.2);">
            <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Ready to Play?</h3>
            <p style="color: var(--muted-foreground); margin-bottom: 1.5rem;">You can select and add equipment to your reservation when you book a facility.</p>
            <a href="<?= BASE_URL ?>/availability.php" class="sb-btn sb-btn-primary sb-btn-lg" style="border-radius: 999px;">Book a Facility</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
