<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require_once 'config.php';
$msg = '';

if (isset($_POST['add'])) {
    $data = json_encode([
        'common_name' => $_POST['cn'],
        'scientific_name' => $_POST['sn'],
        'conservation_status' => $_POST['cs'],
        'population_estimate' => (int)$_POST['pe'],
        'description' => $_POST['desc']
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
    $msg = 'Species added!';
}

if (isset($_GET['del'])) {
    $ch = curl_init($supabase_url . '/species?species_id=eq.' . $_GET['del']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: $supabase_key", "Authorization: Bearer $supabase_key"
    ]);
    curl_exec($ch); curl_close($ch);
    $msg = 'Species deleted.';
}

$list = supabase_query('/species', 'select=*&order=common_name.asc');
if (!is_array($list)) $list = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Species Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark"><div class="container"><a class="navbar-brand" href="dashboard.php">← Back</a></div></nav>
    <div class="container mt-4">
        <?php if($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
        <h3>Species Management</h3>
        <form method="POST" class="row g-2 mb-4">
            <div class="col-md-2"><input name="cn" class="form-control" placeholder="Common Name" required></div>
            <div class="col-md-2"><input name="sn" class="form-control" placeholder="Scientific Name" required></div>
            <div class="col-md-2"><select name="cs" class="form-select"><option>Least Concern</option><option>Near Threatened</option><option>Vulnerable</option><option>Endangered</option><option>Critically Endangered</option></select></div>
            <div class="col-md-2"><input name="pe" type="number" class="form-control" placeholder="Population"></div>
            <div class="col-md-3"><input name="desc" class="form-control" placeholder="Description"></div>
            <div class="col-md-1"><button name="add" class="btn btn-success w-100">+</button></div>
        </form>
        <table class="table table-striped">
            <thead class="table-dark"><tr><th>Common Name</th><th>Scientific Name</th><th>Status</th><th>Population</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach($list as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['common_name']) ?></td>
                    <td><em><?= htmlspecialchars($r['scientific_name']) ?></em></td>
                    <td><span class="badge bg-warning"><?= htmlspecialchars($r['conservation_status']) ?></span></td>
                    <td><?= number_format($r['population_estimate']) ?></td>
                    <td><a href="?del=<?= $r['species_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">X</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>