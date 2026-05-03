<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require_once 'config.php';

$sightings_raw = supabase_query('/sightings', 'select=species:species_id(common_name)');
$species_counts = [];
if (is_array($sightings_raw)) {
    foreach ($sightings_raw as $s) {
        $name = $s['species']['common_name'] ?? 'Unknown';
        $species_counts[$name] = ($species_counts[$name] ?? 0) + 1;
    }
}

$status_raw = supabase_query('/species', 'select=conservation_status');
$status_counts = [];
if (is_array($status_raw)) {
    foreach ($status_raw as $s) {
        $st = $s['conservation_status'] ?? 'Unknown';
        $status_counts[$st] = ($status_counts[$st] ?? 0) + 1;
    }
}

$threats_raw = supabase_query('/threats', 'select=habitat:habitat_id(name)');
$habitat_threats = [];
if (is_array($threats_raw)) {
    foreach ($threats_raw as $t) {
        $hname = $t['habitat']['name'] ?? 'None';
        $habitat_threats[$hname] = ($habitat_threats[$hname] ?? 0) + 1;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark"><div class="container"><a class="navbar-brand" href="dashboard.php">← Back</a></div></nav>
    <div class="container mt-4">
        <h2>Reports</h2>
        <div class="row mt-4">
            <div class="col-md-6"><div class="card"><div class="card-body"><h5>Sightings by Species</h5><canvas id="c1"></canvas></div></div></div>
            <div class="col-md-6"><div class="card"><div class="card-body"><h5>Conservation Status</h5><canvas id="c2"></canvas></div></div></div>
        </div>
        <div class="row mt-4"><div class="col-12"><div class="card"><div class="card-body"><h5>Threats by Habitat</h5><canvas id="c3"></canvas></div></div></div></div>
    </div>
    <script>
    new Chart(c1,{type:'bar',data:{labels:[<?php foreach($species_counts as $k=>$v) echo "'$k',"; ?>],datasets:[{label:'Sightings',data:[<?php foreach($species_counts as $v) echo "$v,"; ?>],backgroundColor:'#198754'}]}});
    new Chart(c2,{type:'pie',data:{labels:[<?php foreach($status_counts as $k=>$v) echo "'$k',"; ?>],datasets:[{data:[<?php foreach($status_counts as $v) echo "$v,"; ?>],backgroundColor:['#198754','#ffc107','#fd7e14','#dc3545','#6f42c1']}]}});
    new Chart(c3,{type:'bar',data:{labels:[<?php foreach($habitat_threats as $k=>$v) echo "'$k',"; ?>],datasets:[{label:'Threats',data:[<?php foreach($habitat_threats as $v) echo "$v,"; ?>],backgroundColor:'#dc3545'}]}});
    </script>
</body>
</html>