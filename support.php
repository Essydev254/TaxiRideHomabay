<?php
require_once __DIR__ . '/includes/layout.php';
$u = require_role('customer', 'driver');
if (is_post()) {
    post_guard();
    $subj = inp('subject'); $msg = inp('message'); $cat = inp('category'); $bid = (int)inp('booking_id') ?: null;
    if (mb_strlen($subj) < 4 || mb_strlen($msg) < 10) flash('error', 'Add a short subject and describe the problem in at least 10 characters.');
    else {
        if ($bid && !val('SELECT id FROM bookings WHERE id=? AND (customer_id=? OR driver_id=?)', [$bid, $u['id'], $u['id']])) $bid = null;
        q('INSERT INTO tickets (user_id, booking_id, category, subject) VALUES (?,?,?,?)', [$u['id'], $bid, $cat, $subj]);
        $id = (int)db()->lastInsertId();
        q('INSERT INTO ticket_replies (ticket_id, user_id, message) VALUES (?,?,?)', [$id, $u['id'], $msg]);
        notify_role('admin', 'New support ticket', $u['name'] . ': ' . $subj, 'support_view.php?id=' . $id);
        flash('success', 'Ticket sent. The TaxiRide office will reply here.'); redirect('support_view.php?id=' . $id);
    }
}
$tickets = rows('SELECT * FROM tickets WHERE user_id=? ORDER BY id DESC', [$u['id']]);
$mine = rows('SELECT id, ref FROM bookings WHERE customer_id=? OR driver_id=? ORDER BY id DESC LIMIT 20', [$u['id'], $u['id']]);
$cats = $u['role'] === 'driver' ? ['Vehicle issue', 'Payment issue', 'Passenger issue', 'App problem', 'Other'] : ['Complaint', 'Lost item', 'Payment issue', 'Booking issue', 'Other'];
page_start('Support', 'support.php', '', 'Tell us what went wrong. Replies appear on this page and in your notifications.'); ?>
<div class="grid g-side">
  <form class="card" method="post"><h3>New ticket</h3><?= csrf_field() ?>
    <div class="field"><label for="category">What is it about?</label><select id="category" name="category"><?php foreach ($cats as $c): ?><option><?= e($c) ?></option><?php endforeach; ?></select></div>
    <div class="field"><label for="booking_id">Related trip (optional)</label><select id="booking_id" name="booking_id"><option value="">No specific trip</option><?php foreach ($mine as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['ref']) ?></option><?php endforeach; ?></select></div>
    <div class="field"><label for="subject">Subject</label><input id="subject" name="subject" required maxlength="120"></div>
    <div class="field"><label for="message">Describe the problem</label><textarea id="message" name="message" required></textarea></div>
    <button class="btn block">Send ticket</button></form>
  <div class="card"><h3>Your tickets</h3>
  <?php if (!$tickets): ?><div class="empty"><b>No tickets yet</b>If something goes wrong with a trip, tell us here.</div><?php else: ?>
  <div class="tbl-wrap"><table class="tbl"><thead><tr><th>Subject</th><th>Category</th><th>Status</th><th>Updated</th></tr></thead><tbody>
  <?php foreach ($tickets as $t): ?><tr><td><a href="<?= url('support_view.php?id=' . $t['id']) ?>"><?= e($t['subject']) ?></a></td><td><?= e($t['category']) ?></td><td><?= tbadge($t['status']) ?></td><td class="muted small"><?= ago($t['updated_at']) ?></td></tr><?php endforeach; ?>
  </tbody></table></div><?php endif; ?></div>
</div>
<?php page_end();
