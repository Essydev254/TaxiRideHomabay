<?php
require_once __DIR__ . '/includes/layout.php';
if (user()) redirect(home_for(user()['role']));
$err = []; $v = ['name' => '', 'email' => '', 'phone' => ''];
if (is_post()) {
    post_guard();
    $v = ['name' => inp('name'), 'email' => strtolower(inp('email')), 'phone' => inp('phone')];
    $ph = norm_phone($v['phone']); $pw = $_POST['password'] ?? '';
    if (mb_strlen($v['name']) < 3) $err[] = 'Enter your full name.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $err[] = 'Enter a valid email address.';
    if (!$ph) $err[] = 'Enter a Kenyan phone number, for example 0712 345 678.';
    if (strlen($pw) < 8) $err[] = 'Password must have at least 8 characters.';
    if ($pw !== ($_POST['password2'] ?? '')) $err[] = 'The two passwords do not match.';
    if (!$err && val('SELECT id FROM users WHERE email=?', [$v['email']])) $err[] = 'An account with this email already exists. Try logging in.';
    if (!$err) {
        q("INSERT INTO users (role,name,email,phone,password) VALUES ('customer',?,?,?,?)", [$v['name'], $v['email'], $ph, password_hash($pw, PASSWORD_DEFAULT)]);
        $id = (int)db()->lastInsertId(); session_regenerate_id(true); $_SESSION['uid'] = $id;
        notify($id, 'Welcome to TaxiRide', 'Book your first ride in Homa Bay in under a minute.', 'customer/book.php');
        flash('success', 'Account created. Welcome to TaxiRide!'); redirect('customer/dashboard.php');
    }
}
auth_start('Create account', 'A ride is one tap away.', 'Register once and book town rides, trips to nearby towns and intercounty journeys. See your fare before you ride.'); ?>
<h1>Create account</h1><p class="muted mb">For passengers. Drivers are added by the TaxiRide office.</p>
<?php if ($err): ?><div class="alert bad"><?= implode('<br>', array_map('e', $err)) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
  <div class="field"><label for="name">Full name</label><input id="name" name="name" required value="<?= e($v['name']) ?>"></div>
  <div class="row"><div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" required value="<?= e($v['email']) ?>"></div>
  <div class="field"><label for="phone">Phone number</label><input id="phone" name="phone" required placeholder="07XX XXX XXX" value="<?= e($v['phone']) ?>"></div></div>
  <div class="row"><div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required minlength="8"><div class="help">At least 8 characters</div></div>
  <div class="field"><label for="password2">Confirm password</label><input id="password2" name="password2" type="password" required></div></div>
  <button class="btn dark block">Create my account</button>
</form>
<p class="mt small">Already registered? <a href="<?= url('login.php') ?>">Log in</a></p>
<?php auth_end();
