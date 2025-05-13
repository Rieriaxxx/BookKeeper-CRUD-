<?php
require_once 'db.php';
//This is for message 
if (!isset($_GET['id'])) {
    header("Location: index.php?message=No record specified&type=error");
    exit();
}
//Getting the id from the database
$id = $_GET['id'];
//Logic of editing the record
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $book_title = $_POST['book_title'];
    $date_borrowed = $_POST['date_borrowed'];
    $return_due_date = $_POST['return_due_date'];
    $actual_return_date = !empty($_POST['actual_return_date']) ? "'" . $_POST['actual_return_date'] . "'" : "NULL";

    $sql = "UPDATE bookkeeper SET name=?, book_title=?, date_borrowed=?, return_due_date=?, actual_return_date=? WHERE id_number=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $name, $book_title, $date_borrowed, $return_due_date, $_POST['actual_return_date'], $id);
    
    if ($stmt->execute()) {
        header("Location: index.php?message=Record updated successfully&type=success");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Fetch existing record
$sql = "SELECT * FROM bookkeeper WHERE id_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php?message=Record not found&type=error");
    exit();
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Borrower - BookKeeper</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <h1>Edit Borrower</h1>
        
        <?php if (isset($error)): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="book_title">Book Title:</label>
                <input type="text" id="book_title" name="book_title" value="<?php echo htmlspecialchars($row['book_title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="date_borrowed">Date Borrowed:</label>
                <input type="date" id="date_borrowed" name="date_borrowed" value="<?php echo $row['date_borrowed']; ?>" required>
            </div>

            <div class="form-group">
                <label for="return_due_date">Return Due Date:</label>
                <input type="date" id="return_due_date" name="return_due_date" value="<?php echo $row['return_due_date']; ?>" required>
            </div>

            <div class="form-group">
                <label for="actual_return_date">Actual Return Date:</label>
                <input type="date" id="actual_return_date" name="actual_return_date" value="<?php echo $row['actual_return_date']; ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn submit-btn">Update Borrower</button>
                <a href="index.php" class="btn cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
