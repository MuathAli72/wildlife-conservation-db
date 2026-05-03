<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require_once 'config.php';
$msg = '';

if (isset($_POST['add'])) {
    $data = json_encode([
        'species_id' => (int)$_POST['sid'],
        'habitat_id' => $_POST['hid'] ? (int)$_POST['hid'] : null,
        'user_id' => (int)$_SESSION['user_id'],
        'sighting_date' => $_POST['dt'],
        'count' => (int)$_POST['cnt'],
        'health_status' => $_POST['hs'],
        'notes' => $_POST['nt']
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
    $msg = 'Sighting logged!';
}

if (isset($_GET['del'])) {
    $ch = curl_init($supabase_url . '/sightings?sighting_id=eq.' . $_GET['del']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: $supabase_key", "Authorization: Bearer $supabase_key"
    ]);
    curl_exec($ch); curl_close($ch);
    $msg = 'Deleted.';
}

$list = supabase_query('/sightings', 'select=sighting_id,species:species_id(common_name),habitat:habitat_id(name),sighting_date,count,health_status,user:user_id(full_name)&order=sighting_date.desc');
if (!is_array($list)) $list = [];

$species = supabase_query('/species', 'select=species_id,common_name&order=common_name.asc');
if (!is_array($species)) $species = [];

$habitats = supabase_query('/habitats', 'select=habitat_id,name&order=name.asc');
if (!is_array($habitats)) $habitats = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sightings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark"><div class="container"><a class="navbar-brand" href="dashboard.php">← Back</a></div></nav>
    <div class="container mt-4">
        <?php if($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
        <h3>Log Sighting</h3>
        <form method="POST" class="row g-2 mb-4">
            <div class="col-md-2"><select name="sid" class="form-select" required><option value="">Species</option><?php foreach($species as $s): ?><option value="<?= $s['species_id'] ?>"><?= $s['common_name'] ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><select name="hid" class="form-select"><option value="">Habitat</option><?php foreach($habitats as $h): ?><option value="<?= $h['habitat_id'] ?>"><?= $h['name'] ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><input name="dt" type="date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="col-md-1"><input name="cnt" type="number" class="form-control" placeholder="Count" min="1" required></div>
            <div class="col-md-2"><select name="hs" class="form-select"><option>Healthy</option><option>Injured</option><option>Sick</option><option>Unknown</option></select></div>
            <div class="col-md-2"><input name="nt" class="form-control" placeholder="Notes"></div>
            <div class="col-md-1"><button name="add" class="btn btn-success w-100">+</button></div>
        </form>
        <table class="table table-striped">
            <thead class="table-dark"><tr><th>Species</th><th>Habitat</th><th>Date</th><th>Count</th><th>Health</th><th>Observer</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach($list as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['species']['common_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['habitat']['name'] ?? '') ?></td>
                    <td><?= $r['sighting_date'] ?></td>
                    <td><?= $r['count'] ?></td>
                    <td><span class="badge bg-<?= $r['health_status']=='Healthy'?'success':($r['health_status']=='Injured'?'danger':'warning') ?>"><?= $r['health_status'] ?></span></td>
                    <td><?= htmlspecialchars($r['user']['full_name'] ?? '') ?></td>
                    <td><a href="?del=<?= $r['sighting_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">X</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>