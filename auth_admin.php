<?php
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function login_admin($pdo, $username, $password) {
    $sql = 'SELECT * FROM admin_users WHERE username = :username';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}
?>
