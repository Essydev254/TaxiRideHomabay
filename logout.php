<?php
require_once __DIR__ . '/includes/functions.php';
if (is_post() && hash_equals($_SESSION['csrf'] ?? '', $_POST['_csrf'] ?? '')) { audit('logout'); }
$_SESSION = []; session_destroy();
session_start(); flash('info', 'You have been logged out.');
redirect('login.php');
