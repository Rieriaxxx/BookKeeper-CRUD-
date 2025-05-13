<?php
require_once 'db.php';
//This is for message 
if (!isset($_GET['id'])) {
    header("Location: index.php?message=No record specified&type=error");
    exit();
}
//Getting the id from the database
$id = $_GET['id'];

// Prepare and execute the delete statement
$sql = "DELETE FROM bookkeeper WHERE id_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php?message=Record deleted successfully&type=success");
} else {
    header("Location: index.php?message=Error deleting record: " . $conn->error . "&type=error");
}
exit();
?>
