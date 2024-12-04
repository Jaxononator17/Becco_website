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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);

    // SQL query to insert data into the submissions table
    $sql = "INSERT INTO submissions (name, email, message) VALUES ('$name', '$email', '$message')";

    // Execute the query and check for success
    if ($conn->query($sql) === TRUE) {
        echo "<h2>Thank you, $name!</h2>";
        echo "<p>Your message has been received. We'll get back to you at $email.</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
