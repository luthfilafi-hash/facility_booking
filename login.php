<?php
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        loginUser($user);
        setFlash('Welcome back, ' . $user['name'] . '!');
        header('Location: index.php');
        exit;
    } else {
        setFlash('Invalid email or password.', 'error');
    }
}

$title = "Sign In";
$hide_nav = true;
require_once __DIR__ . '/includes/header_public.php';
?>

<style>
/* Dark theme split screen layout */
body { margin: 0; min-height: 100vh; display: flex; overflow: hidden; font-family: var(--font-sans); background: var(--background); }
.auth-split { display: flex; width: 100vw; height: 100vh; }
.auth-left { flex: 1; position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; background: var(--background); }
.auth-logo { position: absolute; top: 3rem; left: 3rem; display: flex; align-items: center; gap: 0.8rem; font-weight: 800; font-size: 1.25rem; color: var(--foreground); }
.auth-logo svg { width: 24px; height: 24px; color: var(--primary); }
.auth-form-container { width: 100%; max-width: 380px; text-align: center; display: flex; flex-direction: column; }
.auth-title { font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--foreground); }
.auth-subtitle { font-size: 0.9rem; color: var(--muted-foreground); margin-bottom: 2.5rem; }

.auth-tabs { display: flex; gap: 1rem; margin-bottom: 2rem; }
.auth-tab { flex: 1; padding: 10px; text-align: center; font-size: 0.9rem; font-weight: 600; color: var(--muted-foreground); border-radius: 12px; cursor: pointer; transition: all 0.2s; border: 1.5px solid var(--border); background: var(--input); }
.auth-tab:hover { border-color: rgba(255,255,255,0.25); color: var(--foreground); }
.auth-tab.active { background: rgba(255,255,255,0.1); color: var(--foreground); border-color: var(--primary); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }

.auth-input-group { position: relative; margin-bottom: 1.2rem; text-align: left; }
.auth-input-group .icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--muted-foreground); display: flex; }
.auth-input-group .icon svg { width: 18px; height: 18px; }
.auth-input { width: 100%; border: 1.5px solid var(--border); border-radius: 12px; padding: 0.9rem 1rem 0.9rem 3rem; font-size: 0.95rem; color: var(--foreground); background: var(--input); transition: all 0.2s; font-family: var(--font-sans); }
.auth-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(92, 225, 230, 0.15); background: rgba(255,255,255,0.08); }
.auth-input::placeholder { color: rgba(255,255,255,0.4); }
.auth-input-group .check { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: var(--primary); display: none; }
.auth-input:focus ~ .check, .auth-input:valid ~ .check { display: flex; }

.auth-submit { background: linear-gradient(135deg, var(--teal-dark), var(--primary)); color: var(--primary-foreground); width: 100%; border-radius: 12px; padding: 0.9rem; font-weight: 600; font-size: 1rem; border: none; cursor: pointer; margin-top: 1rem; margin-bottom: 2rem; transition: all 0.3s; box-shadow: 0 4px 15px rgba(92, 225, 230, 0.3); }
.auth-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(92, 225, 230, 0.5); }

.auth-divider { display: flex; align-items: center; text-align: center; color: var(--muted-foreground); font-size: 0.8rem; margin-bottom: 1.5rem; }
.auth-divider::before, .auth-divider::after { content: ''; flex: 1; border-bottom: 1px solid var(--border); }
.auth-divider::before { margin-right: 1rem; }
.auth-divider::after { margin-left: 1rem; }

.auth-socials { display: flex; gap: 1rem; justify-content: center; margin-bottom: 2rem; }
.auth-social-btn { width: 44px; height: 44px; border-radius: 50%; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; background: rgba(255,255,255,0.05); }
.auth-social-btn:hover { background: rgba(255,255,255,0.1); transform: translateY(-2px); border-color: rgba(255,255,255,0.3); }
.auth-social-btn svg { width: 20px; height: 20px; }
.social-google { color: #fff; }
.social-apple { color: #fff; background: rgba(255,255,255,0.1) !important; border-color: transparent; }
.social-apple svg { fill: #fff; color: #fff; }
.social-fb { color: #fff; background: #1877F2 !important; border-color: #1877F2; }

.auth-footer { font-size: 0.75rem; color: var(--muted-foreground); line-height: 1.5; padding: 0 1rem; }

.auth-right { flex: 1.1; background: linear-gradient(135deg, rgba(15,23,42,1) 0%, rgba(92,225,230,0.15) 100%); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; }
.auth-right::before { content: ''; position: absolute; width: 100%; height: 100%; background: url('data:image/svg+xml;utf8,<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="2" fill="rgba(255,255,255,0.05)"/></svg>') repeat; opacity: 0.5; }
.auth-right-img { position: relative; z-index: 2; max-width: 70%; animation: float 6s ease-in-out infinite; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.6)); border-radius: 20px; object-fit: cover; object-position: right center; width: 400px; height: 400px; }

@keyframes float {
    0% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(2deg); }
    100% { transform: translateY(0px) rotate(0deg); }
}

@media(max-width: 900px) {
    .auth-right { display: none; }
    .auth-logo { top: 1.5rem; left: 1.5rem; }
}

/* Hide global flash messages container if any, we'll put it in the form */
.sb-flash-wrap { display: none; }
.local-flash { background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.5); color: #fca5a5; padding: 0.75rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1rem; text-align: center; }
</style>

<div class="auth-split">
    <div class="auth-left">
        <div class="auth-logo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
            UniReserve
        </div>
        
        <div class="auth-form-container">
            <h2 class="auth-title">Welcome Back</h2>
            <div class="auth-subtitle">Welcome Back, Please enter Your details</div>
            
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="local-flash">
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="auth-input-group">
                    <div class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </div>
                    <input type="email" name="email" class="auth-input" required placeholder="Email Address">
                    <div class="check">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                </div>
                
                <div class="auth-input-group">
                    <div class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <input type="password" name="password" class="auth-input" required placeholder="Password">
                </div>
                
                <button type="submit" class="auth-submit">Continue</button>
            </form>
            
            <div class="auth-footer">
                Join the thousands of smart students who trust us to manage their facility bookings. Log in to access your dashboard, track your activities, and make informed booking decisions.
            </div>
        </div>
    </div>
    
    <div class="auth-right">
        <!-- Premium 3D Sports Graphic -->
        <img src="<?= BASE_URL ?>/images/login_3d_sports.png" alt="Sports Equipment" class="auth-right-img" style="border-radius: 20px; object-fit: cover; width: 400px; height: 400px; box-shadow: 0 30px 60px rgba(0,0,0,0.25);">
    </div>
</div>

</body>
</html>
