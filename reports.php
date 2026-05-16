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

// Encode for JS safely
function js_labels($arr) {
    return implode(',', array_map(fn($k) => json_encode((string)$k), array_keys($arr)));
}
function js_values($arr) {
    return implode(',', array_values($arr));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports — Wildlife Conservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="wildlife.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #d0dbc8;
            padding: 22px;
        }
        .chart-card h4 {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-style: italic;
            color: #1a2a1c;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            Wildlife Conservation
            <small>Field Monitoring System</small>
        </a>
        <div class="navbar-nav ms-auto d-flex flex-row align-items-center gap-3">
            <a class="nav-link" href="species.php">Species</a>
            <a class="nav-link" href="sightings.php">Sightings</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">

    <div class="d-flex justify-content-between align-items-baseline mb-4">
        <h2>Field Reports</h2>
        <span class="section-sub">Data from live records</span>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="chart-card">
                <h4>Sightings by species</h4>
                <canvas id="c1" height="220"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-card">
                <h4>Conservation status breakdown</h4>
                <canvas id="c2" height="220"></canvas>
            </div>
        </div>
        <div class="col-12">
            <div class="chart-card">
                <h4>Threats by habitat</h4>
                <canvas id="c3" height="120"></canvas>
            </div>
        </div>
    </div>

</div>

<script>
const chartDefaults = {
    font: { family: "'Lora', Georgia, serif", size: 11 },
    color: '#5a7a5e'
};
Chart.defaults.font.family = chartDefaults.font.family;
Chart.defaults.font.size   = chartDefaults.font.size;
Chart.defaults.color       = chartDefaults.color;

new Chart(document.getElementById('c1'), {
    type: 'bar',
    data: {
        labels: [<?= js_labels($species_counts) ?>],
        datasets: [{
            label: 'Sightings',
            data: [<?= js_values($species_counts) ?>],
            backgroundColor: 'rgba(74,124,82,0.75)',
            borderColor: '#2e4a30',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { family: "'Lora',serif", size: 10 } } },
            y: { grid: { color: '#eef2ec' }, beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

new Chart(document.getElementById('c2'), {
    type: 'doughnut',
    data: {
        labels: [<?= js_labels($status_counts) ?>],
        datasets: [{
            data: [<?= js_values($status_counts) ?>],
            backgroundColor: ['#4a7c52','#7aaa82','#e8a050','#c05030','#8a1a1a'],
            borderColor: '#fff',
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 16, font: { family: "'Lora',serif", size: 10 } } }
        }
    }
});

new Chart(document.getElementById('c3'), {
    type: 'bar',
    data: {
        labels: [<?= js_labels($habitat_threats) ?>],
        datasets: [{
            label: 'Threats',
            data: [<?= js_values($habitat_threats) ?>],
            backgroundColor: 'rgba(192,80,48,0.75)',
            borderColor: '#8a2010',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { family: "'Lora',serif", size: 10 } } },
            y: { grid: { color: '#eef2ec' }, beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});
</script>

</body>
</html>
