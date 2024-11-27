<?php

session_start();
require_once 'auth.php';

$host = 'localhost'; 
$dbname = 'becco'; 
$user = 'jax'; 
$pass = 'jax';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Becco Youth Group</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="image-container">
    <div class="inner-container">
        <div class="left-buttons">
            <button>Calendar</button>
            <button>Staff</button>
        </div>
        <img src="309591042_192781796476042_3925392253524476422_n.jpg" alt="Becco YG logo">
        <div class="right-buttons">
            <button>Photo Gallery</button>
            <button>About Us</button>
        </div>
    </div>
</div>
<div class ="becco-start-end-graphic">
    <img src="GLOW.jpg" alt="Becco Glow Banner">
</div>
<div class="announcement-container-img">
 <img src = "442440267_459879439766275_127080721334848971_n.jpg" alt = "Anncouncement">
</div>
<div class ="announcement-container-img2">
    <img src = "435464436_439965525091000_8187432567710359257_n.jpg" alt = Anncouncement2">
</div>
<div class ="announcement-text-container">
    <h1>Announcements</h1>
</div>
<div class ="announcement-text-container2">
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit,<br>
        sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.<br>
        Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi<br>
        ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit<br>
        in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur<br>
        sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt<br>
        mollit anim id est laborum.
    </p>
</div>
<div class="footer">
    <h1>Let's Stay in touch!!</h1>
    <hr class = "footer-line">
    <br><br>
    <p>Need a Ride?</p>
    <p><a href ="contact.html"> Contact Us!</a></p>
    <a href ="https://www.facebook.com/beccoyouthgroup" class ="facebook-link">
        <img src = "facebookdarkmode_logo.png" class ="facebook-image" alt="Facebook Logo" >
    </a>
    <p>© 2024 Becco Youth Group. All rights reserved.</p>
</div>
</body>
</html>