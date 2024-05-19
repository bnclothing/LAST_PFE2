<?php
include("../../php/database.php");

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['id_user'])) {
    // Redirect to login page if not logged in
    header("Location: ../../login.php");
    exit();
}

// Get the user ID from the session
$userId = $_SESSION['id_user'];

// Get search parameters
$searchBy = $_POST["searchBy"];
$search = $_POST["search"];

// Validate and sanitize inputs
$allowedColumns = ['complaint', 'time', 'ID_USER']; // replace with actual column names you allow for searching
if (!in_array($searchBy, $allowedColumns)) {
    die("Invalid search column");
}

// Initialize the base SQL query
$sql = "SELECT c.*, s.*, t.*, u.*
        FROM complaints c
        JOIN complaint_status s ON c.STATUS = s.id_status
        JOIN complaint_type t ON c.TYPE = t.id_type
        JOIN users u ON c.ID_USER = u.ID_USER";

// Modify the query based on user role
if ($_SESSION["name_role"] == "admin") {
    if ($searchBy == "ID_USER") {
        // Use a subquery to find user IDs where the concatenated name matches the search term
        $sql .= " WHERE c.ID_USER IN (SELECT ID_USER FROM users WHERE EMAIL LIKE :search)";
    } else {
        $sql .= " WHERE $searchBy LIKE :search";
    }
} else {
    $sql .= " WHERE c.ID_USER = :userId AND $searchBy LIKE :search";
}

// Prepare the statement
$statement = $db->prepare($sql);

// Bind the parameters
$searchParam = "%" . $search . "%";
$statement->bindParam(':search', $searchParam, PDO::PARAM_STR);
if ($_SESSION["name_role"] != "admin") {
    $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
}

// Execute the statement
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

// Output the results
foreach ($result as $value) {
    echo '<section class="row-fadeIn-wrapper">';
    echo '<article class="row nfl">';
    echo "<ul>";
    echo "<li>" . htmlspecialchars($value["complaint"], ENT_QUOTES, 'UTF-8') . "</li>";
    echo "<li>" . htmlspecialchars($value["EMAIL"], ENT_QUOTES, 'UTF-8') . "</li>";
    echo "<li>" . htmlspecialchars($value["time"], ENT_QUOTES, 'UTF-8') . "</li>";
    echo "<li>" . htmlspecialchars($value["type_labelle"], ENT_QUOTES, 'UTF-8') . "</li>";
    echo "<li>" . htmlspecialchars($value["status_labelle"], ENT_QUOTES, 'UTF-8') . "</li>";
    echo "<li>";
    echo "<a href='../complaint/?id=" . htmlspecialchars($value["ID_COMPLAINT"], ENT_QUOTES, 'UTF-8') . "'><span><i class='fa-regular fa-folder-open'></i></span></a>";
    echo "</li>";
    echo "</ul>";
    echo "</article>";
    echo "</section>";
}
