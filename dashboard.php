<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require_once 'config.php';

$role = $_SESSION['role'];
$name = $_SESSION['full_name'];

$species  = supabase_query('/species',  'select=species_id');
$species_count = is_array($species)  ? count($species)  : 0;

$habitats = supabase_query('/habitats', 'select=habitat_id');
$habitat_count = is_array($habitats) ? count($habitats) : 0;

$sightings = supabase_query('/sightings', 'select=sighting_id');
$sighting_count = is_array($sightings) ? count($sightings) : 0;

$threats = supabase_query('/threats', 'select=threat_id&severity=gte.4');
$critical_threats = is_array($threats) ? count($threats) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Wildlife Conservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="wildlife.css">
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            Wildlife Conservation
            <small>Field Monitoring System</small>
        </a>
        <div class="navbar-nav ms-auto d-flex flex-row align-items-center gap-3">
            <span class="navbar-text"><?= htmlspecialchars($name) ?> — <?= htmlspecialchars($role) ?></span>
            <a class="nav-link" href="species.php">Species</a>
            <a class="nav-link" href="sightings.php">Sightings</a>
            <a class="nav-link" href="reports.php">Reports</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<!-- HERO -->
<div class="wl-hero">
    <img src="https://images.unsplash.com/photo-1564760055775-d63b17a55c44?w=1600&q=80" alt="Wildlife">
    <svg class="wl-hero-silhouette" viewBox="0 0 280 200" xmlns="http://www.w3.org/2000/svg" fill="white">
        <ellipse cx="140" cy="145" rx="68" ry="42"/>
        <circle cx="140" cy="88" r="36"/>
        <ellipse cx="140" cy="82" rx="52" ry="40" opacity="0.5"/>
        <ellipse cx="85" cy="168" rx="11" ry="26"/>
        <ellipse cx="113" cy="176" rx="11" ry="24"/>
        <ellipse cx="165" cy="176" rx="11" ry="24"/>
        <ellipse cx="193" cy="168" rx="11" ry="26"/>
        <path d="M200 136 Q232 106 242 76 Q222 98 207 128Z"/>
        <path d="M80 136 Q48 106 38 76 Q58 98 73 128Z"/>
    </svg>
    <div class="wl-hero-content">
        <div class="wl-hero-eyebrow">Welcome back</div>
        <div class="wl-hero-title">Every species<br><em>tells a story.</em></div>
        <div class="wl-hero-sub"><?= $species_count ?> species monitored across <?= $habitat_count ?> habitats — updated today.</div>
    </div>
</div>

<!-- STAT BAR -->
<div class="wl-stats">
    <div class="wl-stat">
        <div class="wl-stat-icon">🦁</div>
        <div class="wl-stat-num"><?= $species_count ?></div>
        <div class="wl-stat-lbl">Species tracked</div>
    </div>
    <div class="wl-stat">
        <div class="wl-stat-icon">🌍</div>
        <div class="wl-stat-num"><?= $habitat_count ?></div>
        <div class="wl-stat-lbl">Habitats monitored</div>
    </div>
    <div class="wl-stat">
        <div class="wl-stat-icon">🦅</div>
        <div class="wl-stat-num"><?= $sighting_count ?></div>
        <div class="wl-stat-lbl">Sightings logged</div>
    </div>
    <div class="wl-stat">
        <div class="wl-stat-icon">⚠️</div>
        <div class="wl-stat-num danger"><?= $critical_threats ?></div>
        <div class="wl-stat-lbl">Critical threats</div>
    </div>
</div>

<!-- QUICK NAV -->
<div class="container mt-4 mb-5">
    <div class="row g-3">
        <div class="col-md-4">
            <a href="species.php" class="btn btn-wl-outline w-100 py-3" style="font-size:14px;">
                🦁 &nbsp; Manage Species
            </a>
        </div>
        <div class="col-md-4">
            <a href="sightings.php" class="btn btn-wl-outline w-100 py-3" style="font-size:14px;">
                🦅 &nbsp; Log Sightings
            </a>
        </div>
        <div class="col-md-4">
            <a href="reports.php" class="btn btn-wl-outline w-100 py-3" style="font-size:14px;">
                📊 &nbsp; View Reports
            </a>
        </div>
    </div>
</div>

</body>
</html>
