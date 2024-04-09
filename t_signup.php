<?php

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
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username or email already exists
    $SELECT = "SELECT id FROM doctor WHERE username = ? OR email = ? LIMIT 1";
    $stmt = $conn->prepare($SELECT);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    $rnum = $stmt->num_rows;

    if ($rnum == 0) {
        // Username and email are available, proceed with insertion
        $INSERT = "INSERT INTO doctor (first_name, last_name, email, username, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($INSERT);
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $username, $hashed_password);
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! You can now log in.'); window.location.href='t_login.html';</script>";
        } else {
            echo "<script>alert('Error: Unable to register. Please try again later.');</script>";
        }
    } else {
        echo "<script>alert('Username or email already exists. Please choose a different one.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
