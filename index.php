<?php
require_once __DIR__ . '/includes/layout.php';
$vt = rows('SELECT * FROM vehicle_types ORDER BY multiplier');
$areas = ['Town' => 'Around Homa Bay town', 'Intertown' => 'Nearby towns', 'Intercounty' => 'Other counties'];
$locs = [];
foreach (rows('SELECT name, area FROM locations WHERE active=1 ORDER BY name') as $l) $locs[$l['area']][] = $l['name'];
$stats = ['trips' => (int)val("SELECT COUNT(*) FROM bookings WHERE status='completed'"), 'drivers' => (int)val("SELECT COUNT(*) FROM users WHERE role='driver' AND status='active'"), 'rating' => round((float)val('SELECT AVG(stars) FROM ratings'), 1)];
public_start('Taxis in Homa Bay'); ?>
<header class="hero"><div class="hero-in">
  <div>
    <h1>Need a ride in Homa Bay? We are already on the way.</h1>
    <p class="lede">Book a taxi from the pier, the hospital, your gate or the stage. Fixed fares to Kendu Bay, Mbita, Kisumu, Kisii and beyond, with drivers we know by name.</p>
    <div class="flex"><a class="btn" href="<?= url('register.php') ?>">Book your first ride</a><a class="btn line" href="tel:<?= preg_replace('/\s/', '', APP_PHONE) ?>"><?= icon('phone') ?>Call <?= APP_PHONE ?></a></div>
    <p class="mt" style="color:#CFE3E8"><b style="color:#fff"><?= number_format($stats['trips']) ?></b> trips completed &nbsp; <b style="color:#fff"><?= $stats['drivers'] ?></b> verified drivers &nbsp; <b style="color:#fff"><?= $stats['rating'] ?: '5.0' ?></b> average rating</p>
  </div>
  <div id="fares"><form data-fare-form class="card" onsubmit="return false">
    <h3>What will my ride cost?</h3>
    <input type="hidden" name="mode" value="point"><input type="hidden" name="promo" value="">
    <div class="row"><div class="field"><label for="pickup">From</label><select id="pickup" name="pickup"><?= loc_options(1) ?></select></div>
    <div class="field"><label for="drop">To</label><select id="drop" name="drop"><?= loc_options(null) ?></select></div></div>
    <div class="field"><label for="vtype">Vehicle</label><select id="vtype" name="vtype"><?php foreach ($vt as $t): ?><option value="<?= $t['id'] ?>"><?= e($t['name']) ?> (up to <?= $t['seats'] ?> passengers)</option><?php endforeach; ?></select></div>
    <div class="meter" id="meter"><div class="tag">Your fare</div><div class="amt"><small>KES</small>--</div>
      <div class="ln mt"><span>Base fare</span><b data-base>-</b></div><div class="ln"><span>Discount</span><b data-disc>-</b></div><div class="ln"><span>Travel time</span><b data-min>-</b></div><div class="note">Choose where you are going.</div></div>
  </form></div>
</div>
<?= scene_svg() ?></header>

<section class="sec" id="how"><div class="sec-in"><h2>Three steps from the curb to the door</h2>
  <div class="steps">
    <div class="step"><h3>Tell us where</h3><p class="muted">Pick your pickup and drop-off. The fare shows instantly, before you commit.</p></div>
    <div class="step"><h3>Meet your driver</h3><p class="muted">You get the driver's name, phone number and car plate, and you can follow every status change.</p></div>
    <div class="step"><h3>Ride, pay, rate</h3><p class="muted">Pay cash or M-Pesa at the end. Then rate the trip so the best drivers keep getting work.</p></div>
  </div></div></section>

<section class="sec alt" id="coverage"><div class="sec-in"><h2>Where TaxiRide goes</h2><p class="muted">Based in <?= APP_TOWN ?> town. If your destination is not listed, call us and we will quote you.</p>
  <div class="areas"><?php foreach ($areas as $k => $label): ?><div><h3><?= e($label) ?></h3><ul><?php foreach ($locs[$k] ?? [] as $n): ?><li><?= e($n) ?></li><?php endforeach; ?></ul></div><?php endforeach; ?></div></div></section>

<section class="sec"><div class="sec-in"><div class="grid g2" style="align-items:center">
  <div><h2>Built for riders, drivers and the office</h2><p class="muted mb">One system keeps every booking, driver, vehicle and payment in order.</p>
  <ul style="list-style:none;display:grid;gap:10px"><li><?= icon('check') ?> Passengers book, track and rate trips</li><li><?= icon('check') ?> Drivers accept jobs, update trips and see their earnings</li><li><?= icon('check') ?> Admins dispatch, price routes, verify payments and run reports</li></ul></div>
  <div class="card"><h3>Ready when you are</h3><p class="muted mb">It takes a minute to register. Your first trip is one tap away.</p><div class="flex"><a class="btn" href="<?= url('register.php') ?>">Create account</a><a class="btn line" href="<?= url('login.php') ?>">Log in</a></div></div>
</div></div></section>
<?php public_end();
