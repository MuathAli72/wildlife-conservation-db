<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require_once 'config.php';
$msg = '';

if (isset($_POST['add'])) {
    $data = json_encode([
        'species_id'    => (int)$_POST['sid'],
        'habitat_id'    => $_POST['hid'] ? (int)$_POST['hid'] : null,
        'user_id'       => (int)$_SESSION['user_id'],
        'sighting_date' => $_POST['dt'],
        'count'         => (int)$_POST['cnt'],
        'health_status' => $_POST['hs'],
        'notes'         => $_POST['nt']
    ]);
    $ch = curl_init($supabase_url . '/sightings');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: $supabase_key", "Authorization: Bearer $supabase_key",
        "Content-Type: application/json", "Prefer: return=minimal"
    ]);
    curl_exec($ch); curl_close($ch);
    $msg = 'Sighting logged successfully.';
}

if (isset($_GET['del'])) {
    $ch = curl_init($supabase_url . '/sightings?sighting_id=eq.' . (int)$_GET['del']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: $supabase_key", "Authorization: Bearer $supabase_key"
    ]);
    curl_exec($ch); curl_close($ch);
    $msg = 'Sighting removed.';
}

$list = supabase_query('/sightings', 'select=sighting_id,species:species_id(common_name),habitat:habitat_id(name),sighting_date,count,health_status,user:user_id(full_name)&order=sighting_date.desc');
if (!is_array($list)) $list = [];

$species = supabase_query('/species', 'select=species_id,common_name&order=common_name.asc');
if (!is_array($species)) $species = [];

$habitats = supabase_query('/habitats', 'select=habitat_id,name&order=name.asc');
if (!is_array($habitats)) $habitats = [];

function health_badge($status) {
    $map = ['Healthy' => 'ok', 'Injured' => 'injured', 'Sick' => 'sick', 'Unknown' => 'unknown'];
    return $map[$status] ?? 'unknown';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sightings — Wildlife Conservation</title>
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
            <a class="nav-link" href="species.php">Species</a>
            <a class="nav-link" href="reports.php">Reports</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">

    <?php if ($msg): ?>
        <div class="alert mb-4"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- LOG FORM FIRST -->
    <div class="wl-form-panel mb-4">
        <div class="wl-form-header">
            <div class="wl-form-header-title">Log a new sighting</div>
            <div class="wl-form-header-sub">Record field observations for tracking and analysis</div>
        </div>
        <div class="wl-form-body">
            <form method="POST">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Species</label>
                        <select name="sid" class="form-select" required>
                            <option value="">Select species</option>
                            <?php foreach ($species as $s): ?>
                            <option value="<?= (int)$s['species_id'] ?>"><?= htmlspecialchars($s['common_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Habitat</label>
                        <select name="hid" class="form-select">
                            <option value="">Select habitat</option>
                            <?php foreach ($habitats as $h): ?>
                            <option value="<?= (int)$h['habitat_id'] ?>"><?= htmlspecialchars($h['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date</label>
                        <input name="dt" type="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Count</label>
                        <input name="cnt" type="number" class="form-control" placeholder="1" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Health status</label>
                        <select name="hs" class="form-select">
                            <option>Healthy</option>
                            <option>Injured</option>
                            <option>Sick</option>
                            <option>Unknown</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Notes</label>
                        <input name="nt" class="form-control" placeholder="Field notes…">
                    </div>
                    <div class="col-md-1">
                        <button name="add" type="submit" class="btn btn-wl-add">+</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SIGHTINGS TABLE -->
    <div class="d-flex justify-content-between align-items-baseline mb-3">
        <h3>Field sightings</h3>
        <span class="section-sub"><?= count($list) ?> records, most recent first</span>
    </div>

    <table class="wl-table">
        <thead>
            <tr>
                <th>Species</th>
                <th>Habitat</th>
                <th>Date</th>
                <th>Count</th>
                <th>Health</th>
                <th>Observer</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $r): ?>
            <tr>
                <td>
                    <div><?= htmlspecialchars($r['species']['common_name'] ?? '—') ?></div>
                </td>
                <td><?= htmlspecialchars($r['habitat']['name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($r['sighting_date']) ?></td>
                <td><?= (int)$r['count'] ?></td>
                <td>
                    <span class="badge badge-<?= health_badge($r['health_status']) ?>">
                        <?= htmlspecialchars($r['health_status']) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($r['user']['full_name'] ?? '—') ?></td>
                <td>
                    <a href="?del=<?= (int)$r['sighting_id'] ?>"
                       class="btn btn-wl-danger"
                       onclick="return confirm('Remove this sighting?')">
                        Remove
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (empty($list)): ?>
            <tr>
                <td colspan="7" class="text-center py-4" style="font-family:'Lora',serif;color:#7a9a7e;font-style:italic;">
                    No sightings recorded yet. Log one above.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>
</body>
</html>
