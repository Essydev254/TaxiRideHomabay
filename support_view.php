<?php
require_once __DIR__ . '/includes/layout.php';
$u = require_role(); $id = (int)($_GET['id'] ?? 0);
$t = row('SELECT t.*, u.name AS owner, u.role AS owner_role, b.ref FROM tickets t JOIN users u ON u.id=t.user_id LEFT JOIN bookings b ON b.id=t.booking_id WHERE t.id=?', [$id]);
if (!$t || ($u['role'] !== 'admin' && (int)$t['user_id'] !== (int)$u['id'])) { flash('error', 'Ticket not found.'); redirect($u['role'] === 'admin' ? 'admin/tickets.php' : 'support.php'); }
if (is_post()) {
    post_guard();
    if (inp('action') === 'status' && $u['role'] === 'admin' && in_array(inp('status'), ['open', 'in_progress', 'resolved'], true)) {
        q('UPDATE tickets SET status=?, updated_at=NOW() WHERE id=?', [inp('status'), $id]); audit('ticket.status', '#' . $id . ' ' . inp('status'));
        notify((int)$t['user_id'], 'Ticket updated', '"' . $t['subject'] . '" is now ' . str_replace('_', ' ', inp('status')) . '.', 'support_view.php?id=' . $id); flash('success', 'Status updated.');
    } elseif (inp('action') === 'reply' && mb_strlen(inp('message')) >= 2) {
        q('INSERT INTO ticket_replies (ticket_id, user_id, message) VALUES (?,?,?)', [$id, $u['id'], inp('message')]);
        q("UPDATE tickets SET updated_at=NOW(), status=IF(?='admin', IF(status='open','in_progress',status), 'open') WHERE id=?", [$u['role'], $id]);
        if ($u['role'] === 'admin') notify((int)$t['user_id'], 'Reply to your ticket', $t['subject'], 'support_view.php?id=' . $id); else notify_role('admin', 'Ticket reply', $u['name'] . ': ' . $t['subject'], 'support_view.php?id=' . $id);
        flash('success', 'Reply sent.');
    }
    redirect('support_view.php?id=' . $id);
}
$msgs = rows('SELECT r.*, u.name, u.role FROM ticket_replies r JOIN users u ON u.id=r.user_id WHERE ticket_id=? ORDER BY r.id', [$id]);
page_start('Ticket #' . $id, $u['role'] === 'admin' ? 'admin/tickets.php' : 'support.php', '<a class="btn line" href="' . url($u['role'] === 'admin' ? 'admin/tickets.php' : 'support.php') . '">Back to tickets</a>', $t['subject']); ?>
<div class="grid g-main">
  <div class="card"><?php foreach ($msgs as $m): ?><div class="ticket-msg <?= $m['role'] === 'admin' ? 'staff' : '' ?>"><b><?= e($m['name']) ?></b> <span class="muted small"><?= $m['role'] === 'admin' ? 'TaxiRide office' : '' ?> <?= dt($m['created_at']) ?></span><p><?= nl2br(e($m['message'])) ?></p></div><?php endforeach; ?>
    <?php if ($t['status'] !== 'resolved' || $u['role'] === 'admin'): ?><form method="post" class="mt"><?= csrf_field() ?><input type="hidden" name="action" value="reply"><div class="field"><label for="message">Your reply</label><textarea id="message" name="message" required></textarea></div><button class="btn">Send reply</button></form>
    <?php else: ?><p class="muted">This ticket is resolved. Open a new ticket if the problem comes back.</p><?php endif; ?></div>
  <div class="card"><h3>Details</h3><dl class="kv"><dt>Status</dt><dd><?= tbadge($t['status']) ?></dd><dt>Category</dt><dd><?= e($t['category']) ?></dd><dt>From</dt><dd><?= e($t['owner']) ?> (<?= e($t['owner_role']) ?>)</dd><dt>Trip</dt><dd><?= $t['ref'] ? e($t['ref']) : 'None' ?></dd><dt>Opened</dt><dd><?= dt($t['created_at']) ?></dd></dl>
  <?php if ($u['role'] === 'admin'): ?><form method="post" class="mt flex"><?= csrf_field() ?><input type="hidden" name="action" value="status"><select name="status" style="width:auto"><?php foreach (['open' => 'Open', 'in_progress' => 'In progress', 'resolved' => 'Resolved'] as $k => $l): ?><option value="<?= $k ?>" <?= $t['status'] === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select><button class="btn dark sm">Update status</button></form><?php endif; ?></div>
</div>
<?php page_end();
