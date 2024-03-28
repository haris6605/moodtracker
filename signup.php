<?php
// Database connection
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "mood";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

if (mysqli_connect_error()) {
    die('Connect Error('. mysqli_connect_errno().')'. mysqli_connect_error());
} else {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $INSERT = "INSERT Into users (first_name, last_name, email, username, password) values(?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($INSERT);
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $username, $hashed_password);
    $stmt->execute();
    echo "New record inserted successfully";

    // Redirect to login page
    header("Location: login.html");
    $stmt->close();
    $conn->close();
}
?>
