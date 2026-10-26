<?php
require_once __DIR__ . '/includes/layout.php';
$t = inp('token');
$r = row('SELECT * FROM password_resets WHERE token=? AND used=0 AND expires_at>NOW()', [$t]);
if (!$r) { flash('error', 'This reset link is invalid or has expired. Request a new one.'); redirect('forgot.php'); }
if (is_post()) {
    post_guard(); $pw = $_POST['password'] ?? '';
    if (strlen($pw) < 8 || $pw !== ($_POST['password2'] ?? '')) flash('error', 'Passwords must match and have at least 8 characters.');
    else {
        q('UPDATE users SET password=? WHERE id=?', [password_hash($pw, PASSWORD_DEFAULT), $r['user_id']]);
        q('UPDATE password_resets SET used=1 WHERE id=?', [$r['id']]);
        flash('success', 'Password changed. You can log in now.'); redirect('login.php');
    }
}
auth_start('New password', 'Choose a new password.', 'Pick something you will remember and nobody can guess.'); ?>
<h1>New password</h1>
<form method="post" class="mt"><?= csrf_field() ?><input type="hidden" name="token" value="<?= e($t) ?>">
<div class="field"><label for="password">New password</label><input id="password" name="password" type="password" required minlength="8"></div>
<div class="field"><label for="password2">Confirm password</label><input id="password2" name="password2" type="password" required></div>
<button class="btn dark block">Save password</button></form>
<?php auth_end();
