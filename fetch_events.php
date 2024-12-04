<?php
// Database connection details
$servername = "localhost";
$username = "jax";
$password = "jax";
$dbname = "becco";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch events from the database
$sql = "SELECT title, description, event_date FROM events ORDER BY event_date ASC";
$result = $conn->query($sql);

$events = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Close the connection
$conn->close();

// Return events as JSON
header('Content-Type: application/json');
echo json_encode($events);
?>
