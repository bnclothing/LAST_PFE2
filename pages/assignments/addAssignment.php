<?php
include("../../php/database.php");
session_start();

if (isset($_POST["submit"])) {
    // Handle form data
    $module = $_POST["module"];
    $title = $_POST["title"];
    $description = $_POST["description"];
    $file_path = ""; // Set a default value
    $due_date = $_POST["due_date"];

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
    $sql = $db->prepare("INSERT INTO assignment (ID_MODULE, ID_TEACHER, TITLE, DESCRIPTION, file_path, DUE_DATE) VALUES (?, ?, ?, ?, ?, ?)");
    $sql->execute([$module, $_SESSION["id_user"], $title, $description, $file_path, $due_date]);

    // Redirect to the assignments page
    header("Location: index.php");
    exit();
}
