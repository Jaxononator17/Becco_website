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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['name']) && isset($_POST['address']) && isset($_POST['phone']) && isset($_POST['birthday'])) {
        // Insert new entry
        $name = htmlspecialchars($_POST['name']);
        $address = htmlspecialchars($_POST['address']);
        $phone = htmlspecialchars($_POST['phone']);
        $birthday = htmlspecialchars($_POST['birthday']);
        
        $insert_sql = 'INSERT INTO directory (name, address, phone, birthday) VALUES (:name, :address, :phone, :birthday)';
        $stmt_insert = $pdo->prepare($insert_sql);
        $stmt_insert->execute(['name' => $name, 'address' => $address, 'phone' => $phone, 'birthday' => $birthday]);
    } elseif (isset($_POST['delete_id'])) {
        // Delete an entry
        $delete_id = (int) $_POST['delete_id'];
        
        $delete_sql = 'DELETE FROM directory WHERE id = :id';
        $stmt_delete = $pdo->prepare($delete_sql);
        $stmt_delete->execute(['id' => $delete_id]);
    }
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
    <link rel="stylesheet" href="member.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body>
    <!-- Hero Section -->
    <div class="image-container">
        <div class="inner-container">
            <div class="left-buttons">
                <button>2024 Calendar</button>
                <button>Current Staff</button>
            </div>
            <img src="309591042_192781796476042_3925392253524476422_n.jpg" alt="Becco YG logo">
            <div class="right-buttons">
                <button>Prayer Requests</button>
                <a href="member_directory.php"><button>Member Directory</button></a>
            </div>
        </div>
    </div>

    <div class="hero-section">
        <h1 class="hero-title">Entry Lookup</h1>
        <p class="hero-subtitle">"Look up an entry that fits your needs"</p>
        
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($search_results as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($row['birthday']); ?></td>
                                    <td>
                                        <form action="index5.php" method="post" style="display:inline;">
                                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                            <input type="submit" value="Delete">
                                        </form>
                                    </td>
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
                    <th>Actions</th>
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
                    <td>
                        <form action="index5.php" method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" value="Delete">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Form section with container -->
    <div class="form-container">
        <h2>Add a New Entry</h2>
        <form action="index5.php" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <br><br>
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>
            <br><br>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>
            <br><br>
            <label for="birthday">Birthday:</label>
            <input type="text" id="birthday" name="birthday" required>
            <br><br>
            <input type="submit" value="Add Entry">
        </form>
    </div>
</body>
</html>
