<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('student');

// Fetch staff and admins
$stmt = $pdo->prepare("SELECT name, email, role, phone, bio, avatar FROM users WHERE role IN ('admin', 'staff') ORDER BY role, name");
$stmt->execute();
$contacts = $stmt->fetchAll();

$title = 'Contact Information';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';
?>

<style>
/* Premium Contact Cards Styles */
.contact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}
.contact-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 2rem 1.5rem;
    text-align: center;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    position: relative;
    overflow: hidden;
}
.contact-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 4px;
    background: linear-gradient(90deg, #10b981, #3b82f6);
}
.contact-card:hover {
    transform: translateY(-5px);
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
}
.contact-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    margin: 0 auto 1rem;
    border: 3px solid var(--card);
    box-shadow: 0 0 0 2px var(--primary);
    object-fit: cover;
    background: linear-gradient(135deg, #1e293b, #0f172a);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: bold;
    color: white;
    overflow: hidden;
}
.contact-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.contact-name {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: var(--foreground);
}
.contact-role {
    font-size: 0.75rem;
    color: #10b981;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 1px;
    margin-bottom: 1.5rem;
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: rgba(16, 185, 129, 0.1);
    border-radius: 999px;
}
.contact-info {
    font-size: 0.9rem;
    color: var(--muted-foreground);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}
.contact-info a {
    color: var(--muted-foreground);
    text-decoration: none;
    transition: color 0.2s;
}
.contact-info a:hover {
    color: var(--primary);
}
.contact-bio {
    font-size: 0.85rem;
    color: var(--muted-foreground);
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border);
    line-height: 1.5;
}
.contact-header-box {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.7), rgba(15, 23, 42, 0.9));
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--radius);
    padding: 2.5rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2.5rem;
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}
.contact-header-box::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; height: 4px;
    background: linear-gradient(90deg, #10b981, #3b82f6);
}
.ch-icon {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(59, 130, 246, 0.2));
    width: 72px;
    height: 72px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #10b981;
    flex-shrink: 0;
    border: 1px solid rgba(16, 185, 129, 0.3);
}
.ch-content h2 {
    margin: 0 0 0.5rem 0;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--foreground);
    letter-spacing: -0.5px;
}
.ch-content p {
    margin: 0;
    color: var(--muted-foreground);
    line-height: 1.6;
    font-size: 1rem;
    max-width: 800px;
}
@media (max-width: 768px) {
    .contact-header-box {
        flex-direction: column;
        text-align: center;
        padding: 2rem 1.5rem;
    }
}
</style>

<div class="contact-header-box sb-fade">
    <div class="ch-icon">
        <?= render_icon('headphones', '', 32) ?>
    </div>
    <div class="ch-content">
        <h2>Contact Information</h2>
        <p>Need help? Reach out to our administrative team and staff members for any assistance regarding facility bookings, technical issues, or general inquiries. We're here to help!</p>
    </div>
</div>

<div class="contact-grid">
    <?php foreach ($contacts as $index => $contact): ?>
        <div class="contact-card sb-fade" style="animation-delay: <?= $index * 0.1 ?>s;">
            <div class="contact-avatar">
                <?php if (!empty($contact['avatar'])): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($contact['avatar']) ?>" alt="Avatar">
                <?php else: ?>
                    <?= strtoupper(substr($contact['name'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            
            <div class="contact-name"><?= htmlspecialchars($contact['name']) ?></div>
            <div class="contact-role"><?= htmlspecialchars($contact['role']) ?></div>
            
            <div class="contact-info">
                <?= render_icon('mail', '', 14) ?>
                <a href="mailto:<?= htmlspecialchars($contact['email']) ?>">
                    <?= htmlspecialchars($contact['email']) ?>
                </a>
            </div>
            
            <div class="contact-info">
                <?= render_icon('phone', '', 14) ?>
                <?php if (!empty($contact['phone'])): ?>
                    <a href="tel:<?= htmlspecialchars($contact['phone']) ?>">
                        <?= htmlspecialchars($contact['phone']) ?>
                    </a>
                <?php else: ?>
                    <span style="opacity: 0.6; font-style: italic;">Not provided</span>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($contact['bio'])): ?>
            <div class="contact-bio">
                <?= nl2br(htmlspecialchars($contact['bio'])) ?>
            </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($contacts)): ?>
        <div style="grid-column: 1 / -1; text-align: center; color: var(--muted-foreground); padding: 4rem 2rem; background: var(--card); border: 1px solid var(--border); border-radius: var(--radius);">
            <?= render_icon('inbox', '', 48) ?>
            <p style="margin-top: 1rem; font-size: 1.1rem;">No contact information available at the moment.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
