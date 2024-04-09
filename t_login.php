<?php
session_start();

// Database connection
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "mood"; // Replace with your database name

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

if (mysqli_connect_error()) {
    die('Connect Error('. mysqli_connect_errno().')'. mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $SELECT = "SELECT id, username, password FROM doctor WHERE username = ? LIMIT 1";

    // Prepare statement
    $stmt = $conn->prepare($SELECT);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $username, $hashed_password);
    $stmt->store_result();
    $rnum = $stmt->num_rows;

    if ($rnum == 1) { // Username exists
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            // Password is correct
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username; // Store the doctor's username in the session
            header("Location: doctor_homepage.php"); // Redirect to the doctor's homepage
        } else {
            // Password is not correct
            echo "<script>alert('Invalid username or password');</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
