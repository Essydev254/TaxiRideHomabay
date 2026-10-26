<?php
require_once __DIR__ . '/includes/layout.php';
$link = '';
if (is_post()) {
    post_guard();
    $u = row("SELECT id FROM users WHERE email=? AND status='active'", [strtolower(inp('email'))]);
    if ($u) {
        $t = bin2hex(random_bytes(32));
        q('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?,DATE_ADD(NOW(), INTERVAL 1 HOUR))', [$u['id'], $t]);
        $link = url('reset.php?token=' . $t);
        audit('password.reset.request');
    }
    flash('success', 'If that email is registered, a reset link has been created.');
}
auth_start('Forgot password', 'Locked out? It happens.', 'Enter your email and we will create a link that lets you choose a new password.'); ?>
<h1>Reset password</h1><p class="muted mb">The link works for one hour.</p>
<form method="post"><?= csrf_field() ?><div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" required></div><button class="btn dark block">Create reset link</button></form>
<?php if ($link): ?><div class="demo"><b>Your reset link</b>This project has no email server, so the link is shown here instead of being emailed:<br><a href="<?= e($link) ?>">Choose a new password</a></div><?php endif; ?>
<p class="mt small"><a href="<?= url('login.php') ?>">Back to log in</a></p>
<?php auth_end();
