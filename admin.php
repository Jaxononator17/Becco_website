<?php
session_start();
//require_once 'auth_admin.php';

// Check if admin is logged in
//if (!is_admin_logged_in()) {
   // header('Location: admin_login.php');
    //exit;
//}

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

// Handle delete
if (isset($_POST['delete_id'])) {
    $delete_id = (int) $_POST['delete_id'];
    $delete_sql = 'DELETE FROM directory WHERE id = :id';
    $stmt_delete = $pdo->prepare($delete_sql);
    $stmt_delete->execute(['id' => $delete_id]);
}

// Handle calendar update
if (isset($_POST['event_title']) && isset($_POST['event_description']) && isset($_POST['event_date'])) {
    $event_title = htmlspecialchars($_POST['event_title']);
    $event_description = htmlspecialchars($_POST['event_description']);
    $event_date = htmlspecialchars($_POST['event_date']);
    $insert_sql = 'INSERT INTO events (title, description, event_date) VALUES (:title, :description, :event_date)';
    $stmt_insert = $pdo->prepare($insert_sql);
    $stmt_insert->execute(['title' => $event_title, 'description' => $event_description, 'event_date' => $event_date]);
}

// Handle new member addition
if (isset($_POST['new_name']) && isset($_POST['new_address']) && isset($_POST['new_phone']) && isset($_POST['new_birthday'])) {
    $new_name = htmlspecialchars($_POST['new_name']);
    $new_address = htmlspecialchars($_POST['new_address']);
    $new_phone = htmlspecialchars($_POST['new_phone']);
    $new_birthday = htmlspecialchars($_POST['new_birthday']);
    $insert_member_sql = 'INSERT INTO directory (name, address, phone, birthday) VALUES (:name, :address, :phone, :birthday)';
    $stmt_insert_member = $pdo->prepare($insert_member_sql);
    $stmt_insert_member->execute(['name' => $new_name, 'address' => $new_address, 'phone' => $new_phone, 'birthday' => $new_birthday]);
}

// Handle user deletion
if (isset($_POST['delete_user_id'])) {
    $delete_user_id = (int) $_POST['delete_user_id'];
    $delete_user_sql = 'DELETE FROM users WHERE id = :id';
    $stmt_delete_user = $pdo->prepare($delete_user_sql);
    $stmt_delete_user->execute(['id' => $delete_user_id]);
}

// Handle new user addition
if (isset($_POST['new_username']) && isset($_POST['new_password']) && isset($_POST['new_email'])) {
    $new_username = htmlspecialchars($_POST['new_username']);
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $new_email = htmlspecialchars($_POST['new_email']);
    $insert_user_sql = 'INSERT INTO users (username, password, email) VALUES (:username, :password, :email)';
    $stmt_insert_user = $pdo->prepare($insert_user_sql);
    $stmt_insert_user->execute(['username' => $new_username, 'password' => $new_password, 'email' => $new_email]);
}

// Get all entries for main table
$sql = 'SELECT `id`, `name`, `address`, `phone`, `birthday` FROM directory';
$stmt = $pdo->query($sql);

// Get all calendar events
$calendar_sql = 'SELECT `id`, `title`, `description`, `event_date` FROM events';
$calendar_stmt = $pdo->query($calendar_sql);

// Handle calendar event deletion
if (isset($_POST['delete_event_id'])) {
    $delete_event_id = (int) $_POST['delete_event_id'];
    $delete_event_sql = 'DELETE FROM events WHERE id = :id';
    $stmt_delete_event = $pdo->prepare($delete_event_sql);
    $stmt_delete_event->execute(['id' => $delete_event_id]);
}


// Get all users
$users_sql = 'SELECT `id`, `username`, `password` FROM users';
$users_stmt = $pdo->query($users_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
    <link rel="stylesheet" href="styles_admin.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <style>
        /* General Styles */
    body {
        margin: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
        background-color: black; /* Black background */
        font-family: Arial, sans-serif;
        color: #ffff33; /* Neon yellow text */
    }

    /* Hero Section */
    .hero-section {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: black; /* Black background */
        padding: 20px 0;
        position: relative;
    }

    .hero-section img {
        max-width: 220px;
        height: auto;
        display: block;
        margin: 0 20px;
        transition: transform 0.3s ease;
    }

    .hero-section img:hover {
        transform: scale(1.1);
    }

    /* Admin Container */
    .admin-container {
        width: 80%;
        max-width: 1000px;
        background-color: #222222; /* Dark grey background */
        color: #ffff33; /* Neon yellow text */
        padding: 20px;
        margin-top: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Ensure spacing between elements */
    }

    /* Section Headers */
    .admin-container h2, .admin-container h3 {
        color: #33ccff; /* Neon blue text */
        text-align: center;
    }

    /* Forms */
    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
    }

    form label {
        color: #ff33ff; /* Neon purple text */
        margin-bottom: 5px;
    }

    form input[type="text"],
    form input[type="date"],
    form input[type="password"],
    form input[type="email"],
    form textarea,
    form input[type="submit"] {
        width: 80%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #333333; /* Dark grey background for inputs */
        color: #ffff33; /* Neon yellow text */
    }

    form input[type="submit"] {
        background-color: #333333; /* Dark grey background for button */
        color: #ffff33; /* Neon yellow text */
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    form input[type="submit"]:hover {
        background-color: #555555; /* Darker grey on hover */
    }

    /* Tables */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table, th, td {
        border: 2px solid #33ccff; /* Neon blue border */
    }

    th, td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #333333; /* Dark grey background */
        color: #ff33ff; /* Neon purple text */
    }

    td {
        background-color: #222222; /* Slightly lighter grey background */
        color: #ffff33; /* Neon yellow text */
    }

    /* Buttons */
    button {
        padding: 10px 20px;
        font-size: 16px;
        color: white;
        background-color: black;
        border: 2px solid white;
        cursor: pointer;
        transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
    }

    button:hover {
        background-color: #555555; 
        color: #ffff33; 
        transform: scale(1.1);
    }

    .error {
        color: red;
        text-align: center;
        margin-bottom: 20px;
    }

    </style>
</head>

<body>
    <div class="hero-section">
        <a href="index.html"><img src="309591042_192781796476042_3925392253524476422_n.jpg" alt="Becco YG logo" height="220px"></a>
    </div>

    <div class="admin-container">
        <h2>Admin Dashboard</h2>

        <!-- Users table section -->
        <div class="users-section">
            <h3>All Users</h3>
            <table class="half-width-left-align">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users_stmt->fetch()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['password']); ?></td>
                        <td>
                            <form action="admin.php" method="post" style="display:inline;">
                                <input type="hidden" name="delete_user_id" value="<?php echo $row['id']; ?>">
                                <input type="submit" value="Delete">
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h3>Add New User</h3>
            <form action="admin.php" method="post">
                <label for="new_username">Username:</label>
                <input type="text" id="new_username" name="new_username" required>
                <br><br>
                <label for="new_password">Password:</label>
                <input type="password" id="new_password" name="new_password" required>
                <br><br>
                <input type="submit" value="Add User">
            </form>
        </div>

        <!-- Search section -->
        <div class="search-section">
            <h3>Search Member Directory</h3>
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
                                        <form action="admin.php" method="post" style="display:inline;">
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
        <!-- Member directory table -->
        <div class="table-container">
            <h3>All Entries in Member Directory</h3>
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
                            <form action="admin.php" method="post" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <input type="submit" value="Delete">
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Add new member section -->
        <div class="add-member-section">
            <h3>Add New Member</h3>
            <form action="admin.php" method="post">
                <label for="new_name">Name:</label>
                <input type="text" id="new_name" name="new_name" required>
                <br><br>
                <label for="new_address">Address:</label>
                <input type="text" id="new_address" name="new_address" required>
                <br><br>
                <label for="new_phone">Phone:</label>
                <input type="text" id="new_phone" name="new_phone" required>
                <br><br>
                <label for="new_birthday">Birthday:</label>
                <input type="date" id="new_birthday" name="new_birthday" required>
                <br><br>
                <input type="submit" value="Add Member">
            </form>
        </div>

<!-- Calendar update section -->
<div class="calendar-section">
    <h3>Update Calendar</h3>
    <form action="admin.php" method="post">
        <label for="event_title">Event Title:</label>
        <input type="text" id="event_title" name="event_title" required>
        <br><br>
        <label for="event_description">Event Description:</label>
        <textarea id="event_description" name="event_description" required></textarea>
        <br><br>
        <label for="event_date">Event Date:</label>
        <input type="date" id="event_date" name="event_date" required>
        <br><br>
        <input type="submit" value="Add Event">
    </form>

    <h3>All Calendar Events</h3>
    <table class="half-width-left-align">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Event Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $calendar_stmt->fetch()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                <td>
                    <form action="admin.php" method="post" style="display:inline;">
                        <input type="hidden" name="delete_event_id" value="<?php echo $row['id']; ?>">
                        <input type="submit" value="Delete">
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

