<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require_once 'config.php';

$role = $_SESSION['role'];
$name = $_SESSION['full_name'];

$species = supabase_query('/species', 'select=species_id');
$species_count = is_array($species) ? count($species) : 0;

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
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Wildlife Conservation</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-link text-light"><?= htmlspecialchars($name) ?> (<?= htmlspecialchars($role) ?>)</span>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Dashboard</h2>
        <div class="row mt-3">
            <div class="col-md-3"><div class="card text-white bg-primary mb-3"><div class="card-body"><h5><?= $species_count ?></h5><p>Species</p></div></div></div>
            <div class="col-md-3"><div class="card text-white bg-success mb-3"><div class="card-body"><h5><?= $habitat_count ?></h5><p>Habitats</p></div></div></div>
            <div class="col-md-3"><div class="card text-white bg-info mb-3"><div class="card-body"><h5><?= $sighting_count ?></h5><p>Sightings</p></div></div></div>
            <div class="col-md-3"><div class="card text-white bg-danger mb-3"><div class="card-body"><h5><?= $critical_threats ?></h5><p>Critical Threats</p></div></div></div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4"><a href="species.php" class="btn btn-outline-primary w-100 p-4">Manage Species</a></div>
            <div class="col-md-4"><a href="sightings.php" class="btn btn-outline-success w-100 p-4">Log Sightings</a></div>
            <div class="col-md-4"><a href="reports.php" class="btn btn-outline-info w-100 p-4">View Reports</a></div>
        </div>
    </div>
</body>
</html>