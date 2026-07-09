<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$userSession = getUser();
// Fetch fresh user data from DB to get the new fields (phone, bio, avatar)
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userSession['id']]);
$profileUser = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $bio = $_POST['bio'] ?? '';
        
        $avatarPath = $profileUser['avatar'];
        
        // Handle Avatar Upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExt = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($fileExt, $allowedExts)) {
                $fileName = 'avatar_' . $profileUser['id'] . '_' . time() . '.' . $fileExt;
                $destPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destPath)) {
                    // Delete old avatar if exists
                    if (!empty($profileUser['avatar']) && file_exists(__DIR__ . '/' . $profileUser['avatar'])) {
                        unlink(__DIR__ . '/' . $profileUser['avatar']);
                    }
                    $avatarPath = 'uploads/avatars/' . $fileName;
                }
            } else {
                setFlash('Invalid image format for avatar. Please use JPG, PNG, GIF, or WEBP.', 'error');
                header('Location: profile.php');
                exit;
            }
        }
        
        $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, phone=?, bio=?, avatar=? WHERE id=?");
        if ($stmt->execute([$name, $email, $phone, $bio, $avatarPath, $profileUser['id']])) {
            // Update session for header display
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_avatar'] = $avatarPath;
            setFlash('Profile updated successfully.');
        } else {
            setFlash('Failed to update profile.', 'error');
        }
        header('Location: profile.php');
        exit;
    } elseif ($_POST['action'] === 'change_password') {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if ($newPassword === $confirmPassword && strlen($newPassword) >= 6) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->execute([$hashed, $profileUser['id']]);
            setFlash('Password changed successfully.');
        } else {
            setFlash('Passwords do not match or are too short (min 6 chars).', 'error');
        }
        header('Location: profile.php');
        exit;
    }
}

$title = 'My Profile';
$is_dash = true;
require_once __DIR__ . '/includes/header_dash.php';
?>

<style>
/* Premium Profile Styles */
.sb-profile-cover {
    background: linear-gradient(135deg, #10b981, #047857);
    height: 120px;
    border-radius: var(--radius) var(--radius) 0 0;
    margin: -1.5rem -1.5rem 0 -1.5rem; /* pull up over the card padding */
    margin-bottom: -60px; /* pull avatar up */
}
.sb-profile-avatar-wrap {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 1rem;
}
.sb-profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid var(--card);
    background: var(--background);
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.sb-profile-avatar-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid var(--card);
    background: linear-gradient(135deg, #1e293b, #0f172a);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.sb-avatar-upload-btn {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background: #10b981;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 3px solid var(--card);
    transition: transform 0.2s, background 0.2s;
}
.sb-avatar-upload-btn:hover {
    transform: scale(1.1);
    background: #059669;
}
#avatar-input {
    display: none;
}
.sb-profile-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 2rem;
    align-items: start;
}
@media (max-width: 900px) {
    .sb-profile-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="sb-page-header">
    <h2><?= render_icon('user', '', 20) ?> Account Settings</h2>
</div>

<form method="POST" enctype="multipart/form-data" id="profile-form" class="sb-profile-grid">
    <input type="hidden" name="action" value="update_profile">
    
    <!-- Left Sidebar: Avatar & Identity -->
    <div class="sb-form-card sb-fade" style="text-align: center; position: relative;">
        <div class="sb-profile-cover"></div>
        
        <div class="sb-profile-avatar-wrap">
            <?php if (!empty($profileUser['avatar'])): ?>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($profileUser['avatar']) ?>" class="sb-profile-avatar" id="avatar-preview">
            <?php else: ?>
                <div class="sb-profile-avatar-placeholder" id="avatar-preview-placeholder">
                    <?= strtoupper(substr($profileUser['name'], 0, 1)) ?>
                </div>
                <img src="" class="sb-profile-avatar" id="avatar-preview" style="display:none;">
            <?php endif; ?>
            
            <label for="avatar-input" class="sb-avatar-upload-btn editable-only" title="Upload new photo" style="display: none;">
                <?= render_icon('edit', '', 14) ?>
            </label>
            <input type="file" name="avatar" id="avatar-input" accept="image/*" disabled>
        </div>
        
        <h3 style="margin-bottom: 0.25rem; font-weight: 700; font-size: 1.25rem;"><?= htmlspecialchars($profileUser['name']) ?></h3>
        <p style="color: var(--muted-foreground); font-size: 0.9rem; margin-bottom: 1.5rem;"><?= htmlspecialchars($profileUser['email']) ?></p>
        
        <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.4rem 1rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">
            <?= render_icon('shield', '', 14) ?> <?= htmlspecialchars($profileUser['role']) ?>
        </span>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); text-align: left;">
            <p style="font-size: 0.85rem; color: var(--muted-foreground); margin-bottom: 0.5rem;">
                <strong>Member Since:</strong><br>
                <?= date('F j, Y', strtotime($profileUser['created_at'] ?? 'now')) ?>
            </p>
            <?php if(!empty($profileUser['phone'])): ?>
            <p style="font-size: 0.85rem; color: var(--muted-foreground); margin-bottom: 0.5rem;">
                <strong>Phone:</strong><br>
                <?= htmlspecialchars($profileUser['phone']) ?>
            </p>
            <?php endif; ?>
            <?php if(!empty($profileUser['bio'])): ?>
            <p style="font-size: 0.85rem; color: var(--muted-foreground);">
                <strong>About Me:</strong><br>
                <?= nl2br(htmlspecialchars($profileUser['bio'])) ?>
            </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Content: Editable Fields -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        <!-- Personal Info Card -->
        <div class="sb-form-card sb-fade" style="animation-delay: 0.1s;">
            <div class="sb-form-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <?= render_icon('file-text', '', 18) ?>
                    <h2 style="margin: 0;">Personal Information</h2>
                </div>
                <button type="button" class="sb-btn sb-btn-outline sb-btn-sm" id="edit-profile-btn" style="border-radius: 999px;">
                    <?= render_icon('edit', '', 14) ?> Edit
                </button>
            </div>
            <div class="sb-form-body">
                <div class="sb-form-row">
                    <div class="sb-form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="sb-form-input profile-input" required value="<?= htmlspecialchars($profileUser['name']) ?>" disabled>
                    </div>
                    <div class="sb-form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="sb-form-input profile-input" required value="<?= htmlspecialchars($profileUser['email']) ?>" disabled>
                    </div>
                </div>
                <div class="sb-form-row">
                    <div class="sb-form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" class="sb-form-input profile-input" value="<?= htmlspecialchars($profileUser['phone'] ?? '') ?>" placeholder="e.g. +1 234 567 8900" disabled>
                    </div>
                    <div class="sb-form-group">
                        <label>Role</label>
                        <input type="text" class="sb-form-input" value="<?= ucfirst(htmlspecialchars($profileUser['role'])) ?>" disabled style="opacity: 0.6; cursor: not-allowed; background: var(--muted);">
                    </div>
                </div>
                <div class="sb-form-group">
                    <label>Bio / About Me</label>
                    <textarea name="bio" class="sb-form-input profile-input" rows="4" placeholder="Tell the community a bit about yourself, your favorite sports, etc..." disabled><?= htmlspecialchars($profileUser['bio'] ?? '') ?></textarea>
                </div>
                <div class="sb-form-actions editable-only" style="margin-top: 1rem; display: none;">
                    <button type="submit" class="sb-btn sb-btn-primary" style="background: #10b981; border-color: #10b981; border-radius: 999px;">
                        <?= render_icon('check', '', 16) ?> Save Profile Changes
                    </button>
                    <button type="button" class="sb-btn sb-btn-ghost" id="cancel-edit-btn" style="border-radius: 999px;">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="sb-profile-grid" style="margin-top: 2rem;">
    <!-- Empty left column to push password card to the right -->
    <div style="display: none;" class="desktop-spacer"></div>
    <style>@media(min-width: 901px) { .desktop-spacer { display: block !important; } }</style>
    
    <div>
        <!-- Security / Password Card -->
        <div class="sb-form-card sb-fade" style="animation-delay: 0.2s;">
            <div class="sb-form-header">
                <?= render_icon('lock', '', 18) ?>
                <h2>Security Settings</h2>
            </div>
            <div class="sb-form-body">
                <form method="POST">
                    <input type="hidden" name="action" value="change_password">
                    <div class="sb-form-row">
                        <div class="sb-form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="sb-form-input" required minlength="6" placeholder="Minimum 6 characters">
                        </div>
                        <div class="sb-form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" class="sb-form-input" required minlength="6" placeholder="Must match new password">
                        </div>
                    </div>
                    <div class="sb-form-actions" style="margin-top: 1rem;">
                        <button type="submit" class="sb-btn sb-btn-secondary" style="border-radius: 999px;">
                            <?= render_icon('key', '', 16) ?> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Profile Edit Mode Logic
document.getElementById('edit-profile-btn').addEventListener('click', function() {
    const inputs = document.querySelectorAll('.profile-input');
    const editables = document.querySelectorAll('.editable-only');
    const avatarInput = document.getElementById('avatar-input');
    
    inputs.forEach(input => input.disabled = false);
    avatarInput.disabled = false;
    
    editables.forEach(el => el.style.display = '');
    this.style.display = 'none';
});

document.getElementById('cancel-edit-btn').addEventListener('click', function() {
    const inputs = document.querySelectorAll('.profile-input');
    const editables = document.querySelectorAll('.editable-only');
    const avatarInput = document.getElementById('avatar-input');
    
    inputs.forEach(input => input.disabled = true);
    avatarInput.disabled = true;
    
    editables.forEach(el => el.style.display = 'none');
    document.getElementById('edit-profile-btn').style.display = '';
});

// Image upload preview logic
document.getElementById('avatar-input').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.getElementById('avatar-preview-placeholder');
            
            preview.src = e.target.result;
            preview.style.display = 'block';
            if(placeholder) placeholder.style.display = 'none';
        }
        reader.readAsDataURL(this.files[0]);
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
