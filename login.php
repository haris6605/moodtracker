<?php
// Start a new session
session_start();

// Database connection
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "mood";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

if (mysqli_connect_error()) {
    die('Connect Error('. mysqli_connect_errno().')'. mysqli_connect_error());
} else {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $SELECT = "SELECT id, password FROM users WHERE username = ? LIMIT 1";

    // Prepare statement
    $stmt = $conn->prepare($SELECT);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->store_result();
    $rnum = $stmt->num_rows;

    if ($rnum == 1) { // Username exists
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            // Password is correct, set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user_id; // Store user ID in session
            header("Location: home.php");
        } else {
            // Password is not correct
            echo "Invalid username or password";
        }
    } else {
        echo "Invalid username or password";
    }

    $stmt->close();
    $conn->close();
}
?>
