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
    $user_id = $_SESSION['user_id']; // Assuming you have stored user_id in session during login
    $activity = $_POST['activity'];
    $date = $_POST['date'];
    $mood = $_POST['mood'];
    $mental_health = $_POST['mental_health'];
    $goal = $_POST['goal'];

    $INSERT = "INSERT INTO mood_tracker (user_id, activity, date, mood, mental_health, goal) VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare statement
    $stmt = $conn->prepare($INSERT);
    $stmt->bind_param("isssss", $user_id, $activity, $date, $mood, $mental_health, $goal);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Entry successfully inserted
        header("Location: home.php");
    } else {
        // Error occurred
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
