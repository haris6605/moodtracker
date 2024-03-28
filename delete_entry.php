<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

// Database connection
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "mood";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

if (mysqli_connect_error()) {
    die('Connect Error('. mysqli_connect_errno().')'. mysqli_connect_error());
} else {
    $entry_id = $_POST['entry_id'];

    $DELETE = "DELETE FROM mood_tracker WHERE id = ? AND user_id = ?";

    // Prepare statement
    $stmt = $conn->prepare($DELETE);
    $stmt->bind_param("ii", $entry_id, $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Entry successfully deleted
        header("Location: home.php");
    } else {
        // Error occurred
        echo "Error: Could not delete entry. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>
