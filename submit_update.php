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
    $activity = $_POST['activity'];
    $date = $_POST['date'];
    $mood = $_POST['mood'];
    $mental_health = $_POST['mental_health'];
    $goal = $_POST['goal'];

    $UPDATE = "UPDATE mood_tracker SET activity = ?, date = ?, mood = ?, mental_health = ?, goal = ? WHERE id = ? AND user_id = ?";

    // Prepare statement
    $stmt = $conn->prepare($UPDATE);
    $stmt->bind_param("ssssiii", $activity, $date, $mood, $mental_health, $goal, $entry_id, $_SESSION['user_id']);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header("Location: home.php");
        } else {
            echo "No changes made to the entry.";
        }
    } else {
        echo "Update failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
