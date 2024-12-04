<?php

session_start();
require_once 'auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

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

// Handle search
$search_results = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_sql = 'SELECT `id`, `name`, `address`, `phone`, `birthday` FROM directory WHERE `name` LIKE :search';
    $search_stmt = $pdo->prepare($search_sql);
    $search_stmt->execute(['search' => $search_term]);
    $search_results = $search_stmt->fetchAll();
}

// Get all entries for main table
$sql = 'SELECT `id`, `name`, `address`, `phone`, `birthday` FROM directory';
$stmt = $pdo->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Lookup</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>

<body>
    <div class="hero-section">
        <a href="index.html"><img src="309591042_192781796476042_3925392253524476422_n.jpg" alt="Becco YG logo" height="220px"></a>
    </div>

    <!-- Search moved to hero section -->
    <div class="hero-search">
        <h2>Search for an entry to add to your list</h2>
        <form action="" method="GET" class="search-form">
            <label for="search">Search by Name:</label>
            <input type="text" id="search" name="search" required>
            <input type="submit" value="Search">
        </form>
        
        <?php if (isset($_GET['search'])): ?>
            <div class="search-results">
                <h3>Search Results</h3>
                <?php if ($search_results && count($search_results) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Birthday</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($search_results as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['birthday']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No entries found matching your search.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Table section with container -->
    <div class="table-container">
        <h2>All Entries in Database</h2>
        <table class="half-width-left-align">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Birthday</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['birthday']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <div class="admin-button">
        <a href="admin.php"><button>Admin Page</button></a>
    </div>
</body>
</html>
