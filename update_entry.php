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

    $SELECT = "SELECT * FROM mood_tracker WHERE id = ? AND user_id = ? LIMIT 1";

    // Prepare statement
    $stmt = $conn->prepare($SELECT);
    $stmt->bind_param("ii", $entry_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Display update form with pre-filled data
        echo "<!DOCTYPE html>
              <html>
              <head>
                  <title>Update Entry</title>
                  <style>
                      body {
                          font-family: Arial, sans-serif;
                          background-color: #f4f4f4;
                          padding: 20px;
                      }
                      form {
                          background-color: #fff;
                          padding: 20px;
                          border-radius: 5px;
                          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                      }
                      label {
                          display: block;
                          margin-bottom: 5px;
                      }
                      input[type='text'],
                      input[type='date'] {
                          width: 100%;
                          padding: 8px;
                          margin-bottom: 20px;
                          border-radius: 5px;
                          border: 1px solid #ddd;
                      }
                      input[type='submit'] {
                          background: #50a3a2;
                          color: #ffffff;
                          border: 0;
                          padding: 10px 20px;
                          border-radius: 5px;
                          cursor: pointer;
                      }
                      input[type='submit']:hover {
                          background: #077187;
                      }
                  </style>
              </head>
              <body>
              <form action='submit_update.php' method='post'>
                  <input type='hidden' name='entry_id' value='" . $row['id'] . "'>
                  <label for='activity'>Activity:</label>
                  <input type='text' id='activity' name='activity' value='" . $row['activity'] . "' required><br>
                  <label for='date'>Date:</label>
                  <input type='date' id='date' name='date' value='" . $row['date'] . "' required><br>
                  <label for='mood'>Mood:</label>
                  <input type='text' id='mood' name='mood' value='" . $row['mood'] . "' required><br>
                  <label for='mental_health'>Mental Health:</label>
                  <input type='text' id='mental_health' name='mental_health' value='" . $row['mental_health'] . "' required><br>
                  <label for='goal'>Goal:</label>
                  <input type='text' id='goal' name='goal' value='" . $row['goal'] . "' required><br>
                  <input type='submit' value='Submit Update'>
                </form>
              </body>
              </html>";
    } else {
        echo "Error: Entry not found.";
    }

    $stmt->close();
    $conn->close();
}
?>
