<?php
require_once __DIR__ . '/includes/layout.php';
if (user()) redirect(home_for(user()['role']));
$err = '';
if (is_post()) {
    post_guard();
    $email = strtolower(inp('email')); $pass = $_POST['password'] ?? '';
    $u = row('SELECT * FROM users WHERE email=?', [$email]);
    if (!$u || !password_verify($pass, $u['password'])) $err = 'Email or password is not correct.';
    elseif ($u['status'] !== 'active') $err = 'This account is suspended. Call ' . APP_PHONE . '.';
    else {
        session_regenerate_id(true); $_SESSION['uid'] = (int)$u['id'];
        q('UPDATE users SET last_login=NOW() WHERE id=?', [$u['id']]);
        audit('login', $u['role']);
        redirect(home_for($u['role']));
    }
}
auth_start('Log in', 'Welcome back to TaxiRide.', 'Book rides around Homa Bay, take trips to Kisumu, Kisii or Migori, and manage the whole operation from one place.'); ?>
<h1>Log in</h1><p class="muted mb">Use the email you registered with.</p>
<?php if ($err): ?><div class="alert bad"><?= e($err) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
  <div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" required autofocus value="<?= e(inp('email')) ?>"></div>
  <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required></div>
  <button class="btn dark block">Log in</button>
</form>
<p class="mt small"><a href="<?= url('forgot.php') ?>">Forgot your password?</a> &nbsp;|&nbsp; New here? <a href="<?= url('register.php') ?>">Create a passenger account</a></p>
<div class="demo"><b>Demo accounts (password in brackets)</b>Admin <code>admin@taxiride.co.ke</code> (admin123)<br>Driver <code>peter@taxiride.co.ke</code> (driver123)<br>Passenger <code>achieng@example.com</code> (customer123)</div>
<?php auth_end();
