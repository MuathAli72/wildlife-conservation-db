<?php
session_start();
require_once 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $users = supabase_query('/users', ['username' => 'eq.' . $username]);

    if (!empty($users) && $password === 'password123') {
        $user = $users[0];
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wildlife Conservation — Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="wildlife.css">
    <style>
        body { padding-top: 0; }
    </style>
</head>
<body>
<div class="wl-login-wrap">
    <!-- Background wildlife photo -->
    <img class="wl-login-bg"
         src="https://images.unsplash.com/photo-1564760055775-d63b17a55c44?w=1600&q=80"
         alt="">
    <!-- Animal silhouette SVG -->
    <svg class="wl-login-silhouette" viewBox="0 0 280 400" xmlns="http://www.w3.org/2000/svg" fill="white">
        <ellipse cx="140" cy="270" rx="90" ry="60"/>
        <circle cx="140" cy="160" r="55"/>
        <ellipse cx="140" cy="148" rx="75" ry="60" opacity="0.5"/>
        <ellipse cx="68" cy="310" rx="16" ry="42"/>
        <ellipse cx="108" cy="322" rx="16" ry="38"/>
        <ellipse cx="170" cy="322" rx="16" ry="38"/>
        <ellipse cx="210" cy="310" rx="16" ry="42"/>
        <path d="M210 255 Q255 200 268 140 Q240 180 218 248Z"/>
        <path d="M68 255 Q22 200 10 140 Q38 180 60 248Z"/>
    </svg>

    <div class="wl-login-card">
        <div class="wl-login-brand">
            <div class="wl-login-brand-name">Wildlife Conservation</div>
            <div class="wl-login-brand-sub">Field Monitoring System</div>
            <div class="wl-login-divider"></div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger mb-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-wl-primary w-100 py-2">Sign in to the field system</button>
        </form>

        <p class="text-center mt-3" style="font-family:'Lora',serif;font-size:10px;color:#aac0aa;font-style:italic;">
            Demo: admin_wild / password123
        </p>
    </div>
</div>
</body>
</html>
