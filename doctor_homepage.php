<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: t_login.html");
    exit;
}

// Database connection
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "mood"; // Replace with your database name

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

if (mysqli_connect_error()) {
    die('Connect Error('. mysqli_connect_errno().')'. mysqli_connect_error());
}

$username = $_SESSION['username']; // Make sure you have set this session variable during login

?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Homepage</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header a {
            color: #fff;
            text-decoration: none;
            background-color: #0069d9;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .header a:hover {
            background-color: #0056b3;
        }

        .patient-records {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .patient-record {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .patient-record:last-child {
            border-bottom: none;
        }

        .patient-record h3 {
            margin-top: 0;
        }

        .patient-record p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <span>Welcome, Dr. <?php echo htmlspecialchars($username); ?></span>
        <a href="logoutdoc.php">Logout</a>
    </div>

    <div class="patient-records">
        <h2>Patient Records</h2>
        <?php
        // Fetch patient records
        $query = "SELECT users.first_name, users.last_name, mood_tracker.* FROM users JOIN mood_tracker ON users.id = mood_tracker.user_id ORDER BY users.last_name, mood_tracker.date DESC";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $current_patient = "";
            while ($row = $result->fetch_assoc()) {
                $patient_name = $row['first_name'] . " " . $row['last_name'];
                if ($current_patient != $patient_name) {
                    if ($current_patient != "") {
                        echo "</div>"; // Close the previous patient record div
                    }
                    echo "<div class='patient-record'>";
                    echo "<h3>" . $patient_name . "</h3>";
                    $current_patient = $patient_name;
                }
                echo "<p>Activity: " . $row['activity'] . " | Date: " . $row['date'] . " | Mood: " . $row['mood'] . " | Mental Health: " . $row['mental_health'] . " | Goal: " . $row['goal'] . "</p>";
                echo "<a href='diagnose.php?patient_id=" . $row['user_id'] . "' class='diagnose-btn'>Diagnose</a>";

            }
            echo "</div>"; // Close the last patient record div
        } else {
            echo "<p>No patient records found.</p>";
        }
        ?>
    </div>

</body>
</html>
