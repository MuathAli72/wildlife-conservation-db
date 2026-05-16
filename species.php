<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require_once 'config.php';
$msg = '';

if (isset($_POST['add'])) {
    $data = json_encode([
        'common_name'         => $_POST['cn'],
        'scientific_name'     => $_POST['sn'],
        'conservation_status' => $_POST['cs'],
        'population_estimate' => (int)$_POST['pe'],
        'description'         => $_POST['desc']
    ]);
    $ch = curl_init($supabase_url . '/species');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: $supabase_key", "Authorization: Bearer $supabase_key",
        "Content-Type: application/json", "Prefer: return=minimal"
    ]);
    curl_exec($ch); curl_close($ch);
    $msg = 'Species added successfully.';
}

if (isset($_GET['del'])) {
    $ch = curl_init($supabase_url . '/species?species_id=eq.' . (int)$_GET['del']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: $supabase_key", "Authorization: Bearer $supabase_key"
    ]);
    curl_exec($ch); curl_close($ch);
    $msg = 'Species removed.';
}

$list = supabase_query('/species', 'select=*&order=common_name.asc');
if (!is_array($list)) $list = [];

// Map conservation status to badge class
function status_badge($status) {
    $map = [
        'Least Concern'       => 'lc',
        'Near Threatened'     => 'nt',
        'Vulnerable'          => 'vu',
        'Endangered'          => 'en',
        'Critically Endangered' => 'cr',
    ];
    return $map[$status] ?? 'nt';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Species — Wildlife Conservation</title>
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
            <a class="nav-link" href="sightings.php">Sightings</a>
            <a class="nav-link" href="reports.php">Reports</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">

    <?php if ($msg): ?>
        <div class="alert mb-4"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- ADD FORM FIRST -->
    <div class="wl-form-panel mb-4">
        <div class="wl-form-header">
            <div class="wl-form-header-title">Add a new species to the record</div>
            <div class="wl-form-header-sub">All fields are required to create a species entry</div>
        </div>
        <div class="wl-form-body">
            <form method="POST">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Common name</label>
                        <input name="cn" class="form-control" placeholder="e.g. Bengal Tiger" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Scientific name</label>
                        <input name="sn" class="form-control" placeholder="Panthera tigris" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Conservation status</label>
                        <select name="cs" class="form-select">
                            <option>Least Concern</option>
                            <option>Near Threatened</option>
                            <option>Vulnerable</option>
                            <option>Endangered</option>
                            <option>Critically Endangered</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Population estimate</label>
                        <input name="pe" type="number" class="form-control" placeholder="e.g. 3900" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Description</label>
                        <input name="desc" class="form-control" placeholder="Brief description">
                    </div>
                    <div class="col-md-1">
                        <button name="add" type="submit" class="btn btn-wl-add">+</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SPECIES CARDS -->
    <div class="d-flex justify-content-between align-items-baseline mb-3">
        <h3>Species in the field</h3>
        <span class="section-sub"><?= count($list) ?> species on record</span>
    </div>

    <div class="row g-3">
        <?php foreach ($list as $r): ?>
        <div class="col-md-4">
            <div class="wl-card">
                <div class="wl-card-img">
                    <div class="wl-card-img-placeholder">🐾</div>
                    <div class="wl-card-status">
                        <span class="badge badge-<?= status_badge($r['conservation_status']) ?>">
                            <?= htmlspecialchars($r['conservation_status']) ?>
                        </span>
                    </div>
                    <?php if ($r['population_estimate']): ?>
                    <div class="wl-card-pop">est. <?= number_format($r['population_estimate']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="wl-card-body">
                    <div class="wl-card-name"><?= htmlspecialchars($r['common_name']) ?></div>
                    <div class="wl-card-sci"><?= htmlspecialchars($r['scientific_name']) ?></div>
                    <?php if (!empty($r['description'])): ?>
                    <div class="wl-card-habitat mt-2"><?= htmlspecialchars($r['description']) ?></div>
                    <?php endif; ?>
                    <div class="wl-card-footer">
                        <span class="wl-card-sightings">Species #<?= $r['species_id'] ?></span>
                        <a href="?del=<?= (int)$r['species_id'] ?>"
                           class="btn btn-wl-danger"
                           onclick="return confirm('Remove <?= htmlspecialchars(addslashes($r['common_name'])) ?> from the record?')">
                            Remove
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($list)): ?>
        <div class="col-12 text-center py-5" style="font-family:'Lora',serif;color:#7a9a7e;font-style:italic;">
            No species on record yet. Add one above.
        </div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
