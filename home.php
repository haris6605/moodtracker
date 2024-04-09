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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #50a3a2;
            color: #ffffff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #077187 1px solid;
        }
        header a {
            color: #ffffff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
        }
        .main-content {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            margin-top: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        input[type="submit"] {
            background: #50a3a2;
            color: #ffffff;
            border: 0;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #077187;
        }
        .entry {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 
            <a href="logout.php">Logout</a>
        </div>
    </header>
    <div class="container main-content">
        <!-- Mood Tracker Form -->
        <form action="submit_entry.php" method="post">
            <label for="activity">Activity:</label>
            <input type="text" id="activity" name="activity" required><br>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required><br>
            <label for="mood">Mood:</label>
            <input type="text" id="mood" name="mood" required><br>
            <label for="mental_health">Mental Health:</label>
            <input type="text" id="mental_health" name="mental_health" required><br>
            <label for="goal">Goal:</label>
            <input type="text" id="goal" name="goal" required><br>
            <input type="submit" value="Submit">
        </form>

        <!-- Display Previous Entries -->
        <h2>Your Mood Tracker Entries:</h2>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='entry'>";
                echo "Date: " . $row['date'] . " - Activity: " . $row['activity'] . " - Mood: " . $row['mood'] . " - Mental Health: " . $row['mental_health'] . " - Goal: " . $row['goal'];
                // Update button
                echo "<form action='update_entry.php' method='post' style='display: inline;'>
                        <input type='hidden' name='entry_id' value='" . $row['id'] . "'>
                        <input type='submit' value='Update'>
                      </form>";
                // Delete button
                echo "<form action='delete_entry.php' method='post' style='display: inline;'>
                        <input type='hidden' name='entry_id' value='" . $row['id'] . "'>
                        <input type='submit' value='Delete'>
                      </form>";
                echo "</div>";
            }
        } else {
            echo "No entries found.";
        }
        ?>
    </div>
</body>
</html>
