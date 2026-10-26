<?php
require_once __DIR__ . '/includes/layout.php';
$u = require_role();
if (isset($_GET['go'])) {
    $n = row('SELECT * FROM notifications WHERE id=? AND user_id=?', [(int)$_GET['go'], $u['id']]);
    if ($n) { q('UPDATE notifications SET is_read=1 WHERE id=?', [$n['id']]); if ($n['link']) redirect($n['link']); }
    redirect('notifications.php');
}
if (is_post()) { post_guard(); q('UPDATE notifications SET is_read=1 WHERE user_id=?', [$u['id']]); flash('success', 'All notifications marked as read.'); redirect('notifications.php'); }
$per = 15; $total = (int)val('SELECT COUNT(*) FROM notifications WHERE user_id=?', [$u['id']]);
$off = (min(page_no(), max(1, (int)ceil($total / $per))) - 1) * $per;
$list = rows("SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC LIMIT $per OFFSET $off", [$u['id']]);
page_start('Notifications', 'notifications.php', '<form method="post">' . csrf_field() . '<button class="btn line">Mark all as read</button></form>', 'Updates about your bookings, trips and account.'); ?>
<div class="card">
<?php if (!$list): ?><div class="empty"><b>You are all caught up</b>New updates will show up here.</div><?php endif; ?>
<?php foreach ($list as $n): ?>
  <a class="docwarn" style="color:inherit;<?= $n['is_read'] ? '' : 'background:#F3FAFB;font-weight:600' ?>" href="?go=<?= $n['id'] ?>"><span><b><?= e($n['title']) ?></b><br><span class="muted" style="font-weight:400"><?= e($n['message']) ?></span></span><span class="muted small nowrap"><?= ago($n['created_at']) ?></span></a>
<?php endforeach; ?>
<?= pager($total, $per) ?></div>
<?php page_end();
