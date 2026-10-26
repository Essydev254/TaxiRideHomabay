<?php
require_once __DIR__ . '/includes/layout.php';
$u = require_role();
if (is_post()) {
    post_guard();
    if (inp('action') === 'profile') {
        $ph = norm_phone(inp('phone')); $name = inp('name'); $em = strtolower(inp('email'));
        if (mb_strlen($name) < 3 || !$ph || !filter_var($em, FILTER_VALIDATE_EMAIL)) flash('error', 'Enter your full name, a valid email and a Kenyan phone number.');
        elseif (val('SELECT id FROM users WHERE email=? AND id<>?', [$em, $u['id']])) flash('error', 'That email is already used by another account.');
        else { q('UPDATE users SET name=?, phone=?, email=? WHERE id=?', [$name, $ph, $em, $u['id']]); audit('profile.update'); flash('success', 'Profile saved.'); }
    } else {
        if (!password_verify($_POST['current'] ?? '', $u['password'])) flash('error', 'Your current password is not correct.');
        elseif (strlen($_POST['new'] ?? '') < 8 || ($_POST['new'] ?? '') !== ($_POST['new2'] ?? '')) flash('error', 'New passwords must match and have at least 8 characters.');
        else { q('UPDATE users SET password=? WHERE id=?', [password_hash($_POST['new'], PASSWORD_DEFAULT), $u['id']]); audit('password.change'); flash('success', 'Password changed.'); }
    }
    redirect('profile.php');
}
$d = $u['role'] === 'driver' ? row('SELECT * FROM drivers WHERE user_id=?', [$u['id']]) : null;
page_start('My profile', 'profile.php', '', 'Keep your contact details up to date so drivers and the office can reach you.'); ?>
<div class="grid g2">
  <form class="card" method="post"><h3>Personal details</h3><?= csrf_field() ?><input type="hidden" name="action" value="profile">
    <div class="field"><label for="name">Full name</label><input id="name" name="name" required value="<?= e($u['name']) ?>"></div>
    <div class="row"><div class="field"><label for="email">Email</label><input id="email" name="email" type="email" required value="<?= e($u['email']) ?>"></div>
    <div class="field"><label for="phone">Phone</label><input id="phone" name="phone" required value="<?= e($u['phone']) ?>"></div></div>
    <button class="btn">Save changes</button>
    <p class="help mt">Member since <?= dt($u['created_at'], 'F Y') ?>. Role: <?= e(ucfirst($u['role'])) ?>.</p>
  </form>
  <form class="card" method="post"><h3>Change password</h3><?= csrf_field() ?><input type="hidden" name="action" value="password">
    <div class="field"><label for="current">Current password</label><input id="current" name="current" type="password" required></div>
    <div class="row"><div class="field"><label for="new">New password</label><input id="new" name="new" type="password" required minlength="8"></div>
    <div class="field"><label for="new2">Confirm new password</label><input id="new2" name="new2" type="password" required></div></div>
    <button class="btn dark">Update password</button>
  </form>
</div>
<?php if ($d): ?><div class="card mt"><h3>Driver documents</h3>
  <dl class="kv"><dt>Driving licence</dt><dd><?= e($d['licence_no']) ?>, expires <?= dt($d['licence_expiry'], 'd M Y') ?></dd><dt>PSV badge</dt><dd><?= e($d['psv_badge']) ?>, expires <?= dt($d['psv_expiry'], 'd M Y') ?></dd><dt>Base area</dt><dd><?= e($d['base_area']) ?></dd></dl>
  <p class="help mt">To change documents, ask the TaxiRide office.</p></div><?php endif; ?>
<?php page_end();
