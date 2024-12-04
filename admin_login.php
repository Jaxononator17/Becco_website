<?php
// admin_login.php - Admin Login page
session_start();
require_once 'config.php';
require_once 'auth_admin.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if (login_admin($pdo, $username, $password)) {
            header('Location: admin.php');
            exit;
        } else {
            $error_message = 'Invalid username or password';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles_admin.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body>
    <div class="auth-container">
        <h1>Admin Login</h1>
        <?php if ($error_message): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
