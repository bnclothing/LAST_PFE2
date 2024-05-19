<?php
include("../../php/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'];

    $sql = $db->prepare("DELETE FROM groupdiscussion WHERE ID_GROUP_DISCUSSION = ?");
    $sql->execute([$id]);

    http_response_code(200);
} else {
    http_response_code(405); // Method Not Allowed
}
?>
