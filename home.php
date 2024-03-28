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
}

// Fetch user's previous entries
$user_id = $_SESSION['user_id']; // Assuming you have stored user_id in session during login
$query = "SELECT * FROM mood_tracker WHERE user_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
</head>
<body>
    <div style="text-align: right;">
        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 
        <a href="logout.php">Logout</a>
    </div>

    <!-- Mood Tracker Form -->
    <form action="submit_entry.php" method="post">
        <label for="activity">Activity:</label>
        <input type="text" id="activity" name="activity" required><br><br>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required><br><br>
        <label for="mood">Mood:</label>
        <input type="text" id="mood" name="mood" required><br><br>
        <label for="mental_health">Mental Health:</label>
        <input type="text" id="mental_health" name="mental_health" required><br><br>
        <label for="goal">Goal:</label>
        <input type="text" id="goal" name="goal" required><br><br>
        <input type="submit" value="Submit">
    </form>

    <!-- Display Previous Entries -->
    <h2>Your Mood Tracker Entries:</h2>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Date: " . $row['date'] . " - Mood: " . $row['mood'] . " - Mental Health: " . $row['mental_health'] . " - Goal: " . $row['goal'];
            // Update button
            echo "<form action='update_entry.php' method='post' style='display: inline;'>
                    <input type='hidden' name='entry_id' value='" . $row['id'] . "'>
                    <input type='submit' value='Update'>
                  </form>";
            // Delete button
            echo "<form action='delete_entry.php' method='post' style='display: inline;'>
                    <input type='hidden' name='entry_id' value='" . $row['id'] . "'>
                    <input type='submit' value='Delete'>
                  </form><br>";
        }
    } else {
        echo "No entries found.";
    }
    ?>

</body>
</html>
