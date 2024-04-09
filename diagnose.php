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

$patient_id = $_GET['patient_id'];
$doctor_id = $_SESSION['user_id']; // Assuming you have stored the doctor's user ID in the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $notes = $_POST['notes'];

    // Insert the diagnosis into the database
    $INSERT = "INSERT INTO diagnoses (user_id, doctor_id, diagnosis, treatment, notes) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($INSERT);
    $stmt->bind_param("iisss", $patient_id, $doctor_id, $diagnosis, $treatment, $notes);
    $stmt->execute();
    $stmt->close();
}

// Fetch all diagnoses for this patient made by this doctor
$SELECT = "SELECT diagnosis, treatment, notes, date FROM diagnoses WHERE user_id = ? AND doctor_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($SELECT);
$stmt->bind_param("ii", $patient_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Diagnose Patient</title>
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
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
            height: 100px;
        }
        button {
            background: #50a3a2;
            color: #ffffff;
            border: 0;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #077187;
        }
        .diagnosis-record {
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
            Diagnose Patient
        </div>
    </header>
    <div class="container main-content">
        <form action="diagnose.php?patient_id=<?php echo $patient_id; ?>" method="post">
            <label for="diagnosis">Diagnosis:</label>
            <textarea id="diagnosis" name="diagnosis" required></textarea><br>
            <label for="treatment">Treatment:</label>
            <textarea id="treatment" name="treatment" required></textarea><br>
            <label for="notes">Notes:</label>
            <textarea id="notes" name="notes"></textarea><br>
            <button type="submit">Save Diagnosis</button>
        </form>

        <h3>Previous Diagnoses:</h3>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='diagnosis-record'>";
                echo "<p><strong>Date:</strong> " . $row['date'] . "</p>";
                echo "<p><strong>Diagnosis:</strong> " . $row['diagnosis'] . "</p>";
                echo "<p><strong>Treatment:</strong> " . $row['treatment'] . "</p>";
                echo "<p><strong>Notes:</strong> " . $row['notes'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No previous diagnoses found.</p>";
        }
        ?>
    </div>
</body>
</html>
