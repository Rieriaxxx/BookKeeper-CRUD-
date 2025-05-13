<?php
require_once 'db.php';

//Logic of adding a new record
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $book_title = $_POST['book_title'];
    $date_borrowed = $_POST['date_borrowed'];
    $return_due_date = $_POST['return_due_date'];
    //Insert the new data into the database
    $sql = "INSERT INTO bookkeeper (name, book_title, date_borrowed, return_due_date) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $book_title, $date_borrowed, $return_due_date);
    //Message execute if the action is successful
    if ($stmt->execute()) {
        header("Location: index.php?message=Record added successfully&type=success");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Borrower - BookKeeper</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <h1>Add New Borrower</h1>
        
        <?php if (isset($error)): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="book_title">Book Title:</label>
                <input type="text" id="book_title" name="book_title" required>
            </div>

            <div class="form-group">
                <label for="date_borrowed">Date Borrowed:</label>
                <input type="date" id="date_borrowed" name="date_borrowed" required>
            </div>

            <div class="form-group">
                <label for="return_due_date">Return Due Date:</label>
                <input type="date" id="return_due_date" name="return_due_date" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn submit-btn">Add Borrower</button>
                <a href="index.php" class="btn cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
