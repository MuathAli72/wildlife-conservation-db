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
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wildlife Conservation - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1516426122078-c23e76319801?w=1200'); background-size: cover; min-height: 100vh; }
        .login-card { background: rgba(255,255,255,0.95); border-radius: 15px; padding: 30px; margin-top: 100px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container"><span class="navbar-brand">Wildlife Conservation Monitoring</span></div>
    </nav>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card">
                    <h3 class="text-center mb-4">System Login</h3>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                    <p class="text-center mt-3 text-muted small">Demo: admin_wild / password123</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>