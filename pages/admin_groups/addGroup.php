<?php
include("../../php/database.php");
session_start();

if (isset($_POST["submit"])) {
    // Handle form data
    $name = $_POST['name'];
    $type = $_POST['type'];
    $entity_type = $_POST['entity_type'];

    // Check if a group discussion with the same name and different type exists
    $checkStatement = $db->prepare("SELECT COUNT(*) AS count FROM groupdiscussion WHERE NAME = ? AND TYPE = ?");
    $checkStatement->execute([$name, $type]);
    $result = $checkStatement->fetch();

    if ($result['count'] > 0) {
        // A group discussion with the same name and different type already exists, do not insert
        echo "<script>alert('A group discussion with the same name and different type already exists');
            window.location.href = './';
        </script>";
    } else {
        // Insert user data into the users table
        $statement = $db->prepare("INSERT INTO groupdiscussion (NAME, TYPE, entity_type) VALUES (?,?,?)");
        $statement->execute([$name, $type, $entity_type]);

        header("Location: ./index.php");
        exit(); // Terminate the script execution after redirection
    }
}
?>
