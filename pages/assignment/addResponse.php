<?php
include("../../php/database.php");
session_start();

if (isset($_POST["submit"])) {
    // Handle form data
    $response = $_POST["Response"];
    $id_assignment = $_POST["id_assignment"];
    $file_path = ""; // Set a default value

    // Check if a file was uploaded
    if ($_FILES["file"]["name"]) {
        // File upload path
        $targetDir = "uploads/";
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = $targetDir . $fileName;
    
        // Upload file to server
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
            $file_path = $targetFilePath;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    // Prepare the SQL statement
    $sql = $db->prepare("INSERT INTO assignments_responses (user_id, id_assignment, response, file_path) VALUES (?, ?, ?, ?)");
    $sql->execute([$_SESSION["id_user"], $id_assignment, $response, $file_path]);

    // Redirect to the assignments page
    header("Location: /?assignment=".$id_assignment);
    exit();
}
