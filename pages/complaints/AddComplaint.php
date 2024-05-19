<?php
// Include your database connection file
include("../../php/database.php");

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Check if the user is logged in and has a valid session
    session_start();
    if (!isset($_SESSION['id_user'])) {
        // Redirect back to the form with an error message if the user is not logged in
        header("Location: addComplaint.php?error=not_logged_in");
        exit();
    }

    // Retrieve form data
    $type = $_POST['Type'];
    $message = $_POST['Message'];
    $userId = $_SESSION['id_user'];

    // Prepare and execute the SQL statement to insert data into the complaint table
    $sql = "INSERT INTO complaints (complaint, TYPE, STATUS, ID_USER) VALUES (:complaint, :type, 3, :userId)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':complaint', $message);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':userId', $userId);

    if ($stmt->execute()) {
        // Redirect to a success page or back to the form with a success message
        header("Location: ./index.php");
        exit();
    } else {
        // Redirect back to the form with an error message
        header("Location: ./index.php");
        exit();
    }
}
