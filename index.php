<?php
$title = "Welcome";
require_once __DIR__ . '/includes/header_public.php';

// Fetch top 3 facilities for the featured section
$featured = $pdo->query("SELECT * FROM facilities LIMIT 3")->fetchAll();
$liveFacilities = $pdo->query("SELECT * FROM facilities LIMIT 4")->fetchAll();
$equipments = $pdo->query("SELECT * FROM equipments WHERE available_quantity > 0 LIMIT 4")->fetchAll();

$tickerItems = [
    ['icon' => 'basketball', 'text' => 'Main Basketball Court was just booked'],
    ['icon' => 'tennis', 'text' => 'Tennis Rackets are trending today'],
    ['icon' => 'activity', 'text' => 'Futsal Court is almost fully booked for tomorrow'],
    ['icon' => 'users', 'text' => '5 new students registered today'],
    ['icon' => 'award', 'text' => 'Ping Pong table is available right now!']
];

// Fetch real statistics
$totalFacilities = $pdo->query("SELECT COUNT(*) FROM facilities")->fetchColumn() ?: 0;
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?: 0;
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn() ?: 0;
?>

<!-- Hero Section -->
<section class="sb-hero">
    <div class="sb-hero-bg" style="background-image: url('<?= BASE_URL ?>/images/hero_bg.png'); opacity: 0.4;"></div>
    <div class="sb-hero-gradient"></div>
    <div class="sb-hero-content sb-fade" style="text-align: center;">
        <div class="sb-hero-eyebrow" style="margin: 0 auto 1.5rem; justify-content: center;">
            <?= render_icon('star', '', 14) ?> Official University Booking Portal
        </div>
        <h1 style="margin-left: auto; margin-right: auto; max-width: 900px;"><span class="sb-serif sb-text-primary">Elevate,</span> your <br> sports <span class="sb-serif sb-text-primary">experience.</span></h1>
        <p style="margin-left: auto; margin-right: auto; font-size: 1.15rem; color: #cbd5e1; font-weight: 400; line-height: 1.7; max-width: 650px;">
            Reserve world-class athletic facilities, track your daily reservations, and unlock your ultimate active lifestyle—all through one seamless, ultra-modern platform.
        </p>
        <div style="display:flex;gap:1rem;justify-content:center;margin-top:2.5rem;">
            <?php if (isLoggedIn()): ?>
                <?php
                    $u = getUser();
                    $dashUrl = BASE_URL . '/student/index.php';
                    if ($u['role'] === 'admin') $dashUrl = BASE_URL . '/admin/index.php';
                    if ($u['role'] === 'staff') $dashUrl = BASE_URL . '/staff/index.php';
                ?>
                <a href="<?= $dashUrl ?>" class="sb-btn sb-btn-primary sb-btn-lg" style="border-radius: 999px;">Go to Dashboard</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login.php" class="sb-btn sb-btn-primary sb-btn-lg" style="border-radius: 999px;">Sign In to Book</a>
                <a href="#facilities" class="sb-btn sb-btn-outline sb-btn-lg" style="border-radius: 999px; color: white; border-color: rgba(255,255,255,0.3);">Browse Facilities</a>
            <?php endif; ?>
        </div>
        </div>
    </div>
    
    <!-- Floating Stats Glass Card -->
    <div class="sb-hero-stats-glass sb-fade-3">
        <div class="sb-glass-stat">
            <span class="n sb-animate-num" data-target="<?= $totalFacilities ?>">0</span>
            <span class="l">Premium Facilities</span>
        </div>
        <div class="sb-glass-stat">
            <span class="n sb-animate-num" data-target="<?= $totalUsers ?>">0</span>
            <span class="l">Active Athletes</span>
        </div>
        <div class="sb-glass-stat">
            <span class="n sb-animate-num" data-target="<?= $totalBookings ?>">0</span>
            <span class="l">Successful Bookings</span>
        </div>
    </div>
</section>

<!-- Live Now Dashboard -->
<?php if(count($liveFacilities) > 0): ?>
<style>
@keyframes sb-pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
</style>
<section id="live-now" class="sb-section" style="padding-top: 5rem; padding-bottom: 2rem;">
    <div class="sb-container">
        <div class="sb-section-heading" style="text-align: center; margin-bottom: 3rem;">
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.5rem 1rem; border-radius: 999px; font-weight: 600; margin-bottom: 1rem;">
                <div style="width: 10px; height: 10px; background-color: #10b981; border-radius: 50%; animation: sb-pulse 2s infinite;"></div>
                Live Now
            </div>
            <h2 style="font-size: 2rem; font-weight: 800;">Available Right Now</h2>
            <p style="color: var(--muted-foreground);">Skip the wait. Book these premium facilities immediately.</p>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            <?php foreach($liveFacilities as $f): ?>
            <div class="sb-facility-card sb-fade" style="border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; display: flex; align-items: center; padding: 1rem; gap: 1rem; background: var(--card);">
                <?php if (!empty($f['image_path'])): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($f['image_path']) ?>" alt="<?= htmlspecialchars($f['name']) ?>" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 80px; height: 80px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0a1628, #1a3a4a); flex-shrink: 0;">
                        <?= render_icon('activity', 'text-white', 30) ?>
                    </div>
                <?php endif; ?>
                <div style="flex: 1;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 0.25rem 0;"><?= htmlspecialchars($f['name']) ?></h3>
                    <div style="color: var(--muted-foreground); font-size: 0.85rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.25rem;">
                        <?= render_icon('map-pin', '', 12) ?> <?= htmlspecialchars($f['location']) ?>
                    </div>
                    <a href="<?= BASE_URL ?>/student/book.php?facility_id=<?= $f['id'] ?>" class="sb-btn sb-btn-primary" style="padding: 0.4rem 1rem; font-size: 0.85rem; width: 100%; justify-content: center; background: #10b981; border-color: #10b981; color: #fff;">Book Instantly</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Facilities -->
<section id="facilities" class="sb-section sb-section-muted">
    <div class="sb-container">
        <div class="sb-section-heading" style="text-align: center;">
            <h2 style="margin-bottom: 0.5rem; font-size: 2rem; font-weight: 800;">Featured Facilities</h2>
            <p style="color: var(--muted-foreground); font-size: 1rem;">Our top-rated venues available for student booking</p>
        </div>
        <div class="sb-facilities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 3rem;">
            <?php foreach($featured as $f): ?>
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
        </div>
        <div style="text-align: center; margin-top: 4rem;">
            <a href="<?= BASE_URL ?>/facilities.php" class="sb-btn sb-btn-primary sb-btn-lg" style="border-radius: 999px;">View All Facilities</a>
        </div>
    </div>
</section>

<!-- Premium Equipment Showcase -->
<section id="equipment" class="sb-section" style="padding-top: 5rem;">
    <div class="sb-container">
        <div class="sb-section-heading" style="text-align: center; margin-bottom: 4rem;">
            <h2 style="margin-bottom: 0.5rem; font-size: 2rem; font-weight: 800;">Sports Equipment</h2>
            <p style="color: var(--foreground); font-size: 1.1rem; font-weight: bold;">We also provide equipment! You can borrow our sports equipment for your sessions.</p>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
            <?php foreach($equipments as $eq): ?>
            <div class="sb-eq-card sb-fade">
                <?php if (!empty($eq['image_path'])): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($eq['image_path']) ?>" alt="<?= htmlspecialchars($eq['name']) ?>" style="width: 100%; height: 140px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
                <?php else: ?>
                    <div class="sb-eq-icon-wrap" style="height: 140px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2); border-radius: 8px; margin-bottom: 1rem;">
                        <?= render_icon('box', '', 36) ?>
                    </div>
                <?php endif; ?>
                <h3><?= htmlspecialchars($eq['name']) ?></h3>
                <p style="margin-top: auto; padding-top: 1rem;">Qty Available: <?= htmlspecialchars($eq['available_quantity']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?= BASE_URL ?>/equipment.php" class="sb-btn sb-btn-outline" style="border-radius: 999px;">View All Equipment</a>
        </div>
    </div>
</section>

<!-- How It Works -->
<section id="how-it-works" class="sb-section" style="padding: 6rem 2.5rem;">
    <div class="sb-container">
        <h2 style="text-align:center;font-size:2.5rem;margin-bottom:4rem;font-weight: 800;">How It Works</h2>
        <div class="sb-timeline">
            <div class="sb-timeline-step sb-fade-1">
                <div class="sb-timeline-icon">
                    <?= render_icon('user', '', 32) ?>
                </div>
                <h3>1. Sign In</h3>
                <p style="color: var(--muted-foreground); font-size: 0.95rem; line-height: 1.6;">Log in securely using your official university credentials.</p>
            </div>
            <div class="sb-timeline-step sb-fade-2">
                <div class="sb-timeline-icon">
                    <?= render_icon('calendar', '', 32) ?>
                </div>
                <h3>2. Choose Time & Place</h3>
                <p style="color: var(--muted-foreground); font-size: 0.95rem; line-height: 1.6;">Browse real-time availability and select a premium timeslot.</p>
            </div>
            <div class="sb-timeline-step sb-fade-3">
                <div class="sb-timeline-icon">
                    <?= render_icon('check', '', 32) ?>
                </div>
                <h3>3. Get Approved & Play</h3>
                <p style="color: var(--muted-foreground); font-size: 0.95rem; line-height: 1.6;">Show up with your ID after staff approval and enjoy.</p>
            </div>
        </div>
    </div>
</section>

<!-- Live Activity Marquee -->
<div class="sb-marquee-wrapper">
    <div class="sb-marquee-content">
        <?php for($i=0; $i<4; $i++): // Repeat content to ensure smooth infinite scroll ?>
            <?php foreach($tickerItems as $item): ?>
            <div class="sb-ticker-item">
                <?= render_icon($item['icon'], '', 16) ?>
                <span class="sb-ticker-highlight" style="margin-left: 0.5rem;"><?= htmlspecialchars($item['text']) ?></span>
            </div>
            <?php endforeach; ?>
        <?php endfor; ?>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const nums = document.querySelectorAll('.sb-animate-num');
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-target'), 10);
                let current = 0;
                const duration = 2000;
                const stepTime = Math.abs(Math.floor(duration / Math.max(target, 1)));
                
                if (target > 0) {
                    const timer = setInterval(() => {
                        current += Math.ceil(target / 100);
                        if (current >= target) {
                            el.textContent = target;
                            clearInterval(timer);
                        } else {
                            el.textContent = current;
                        }
                    }, stepTime > 0 ? stepTime : 10);
                }
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    nums.forEach(num => observer.observe(num));
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
