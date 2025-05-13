<?php
require_once 'db.php';

// Handle Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id_number';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Base query
$sql = "SELECT * FROM bookkeeper WHERE 
        name LIKE ? OR 
        book_title LIKE ? OR 
        id_number LIKE ?";

// Add sorting
$sql .= " ORDER BY $sort $order";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Function to create sort URL
function getSortUrl($field) {
    global $sort, $order, $search;
    $newOrder = ($sort === $field && $order === 'ASC') ? 'DESC' : 'ASC';
    return "?sort=" . $field . "&order=" . $newOrder . ($search ? "&search=" . urlencode($search) : "");
}

// Function to display sort indicator
function getSortIndicator($field) {
    global $sort, $order;
    if ($sort === $field) {
        return $order === 'ASC' ? ' ↑' : ' ↓';
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookKeeper - Library Borrower System</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <h1>BookKeeper - Library Borrower System</h1>
        
        <?php
        if (isset($_GET['message'])) {
            $messageType = isset($_GET['type']) ? $_GET['type'] : 'error';
            echo "<div class='message " . htmlspecialchars($messageType) . "' id='autoCloseMessage'>";
            echo htmlspecialchars($_GET['message']);
            echo "<button onclick='closeMessage()' class='close-btn'>&times;</button>";
            echo "</div>";
        }
        ?>

        <div class="table-controls">
            <div class="search-container">
                <form action="" method="GET" class="search-form">
                    <input type="text" 
                           name="search" 
                           placeholder="Search by ID, name, or book title..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           class="search-input">
                    <button type="submit" class="btn search-btn">Search</button>
                    <?php if ($search): ?>
                        <a href="index.php" class="btn clear-btn">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="add-btn-container">
                <a href="add.php" class="btn add-btn">Add New Borrower</a>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th><a href="<?php echo getSortUrl('id_number'); ?>" class="sort-link">ID Number<?php echo getSortIndicator('id_number'); ?></a></th>
                    <th><a href="<?php echo getSortUrl('name'); ?>" class="sort-link">Name<?php echo getSortIndicator('name'); ?></a></th>
                    <th><a href="<?php echo getSortUrl('book_title'); ?>" class="sort-link">Book Title<?php echo getSortIndicator('book_title'); ?></a></th>
                    <th><a href="<?php echo getSortUrl('date_borrowed'); ?>" class="sort-link">Date Borrowed<?php echo getSortIndicator('date_borrowed'); ?></a></th>
                    <th><a href="<?php echo getSortUrl('return_due_date'); ?>" class="sort-link">Return Due Date<?php echo getSortIndicator('return_due_date'); ?></a></th>
                    <th><a href="<?php echo getSortUrl('actual_return_date'); ?>" class="sort-link">Actual Return Date<?php echo getSortIndicator('actual_return_date'); ?></a></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                
                <?php
                //Checking if there are any records in the database
                if ($result->num_rows > 0) {
                    //Looping through the records
                    while($row = $result->fetch_assoc()) {
                        //Displaying the data
                        echo "<tr>";
                        echo "<td>" . $row['id_number'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['book_title']) . "</td>";
                        echo "<td>" . $row['date_borrowed'] . "</td>";
                        echo "<td>" . $row['return_due_date'] . "</td>";
                        echo "<td>" . ($row['actual_return_date'] ? $row['actual_return_date'] : 'Not returned') . "</td>";
                        echo "<td class='actions'>";
                        echo "<a href='edit.php?id=" . $row['id_number'] . "' class='btn edit-btn'>Edit</a>";
                        echo "<a href='delete.php?id=" . $row['id_number'] . "' class='btn delete-btn' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    //Displaying a message if there are no records in the database
                    $colspan = $search ? 7 : 7;
                    echo "<tr><td colspan='$colspan' class='no-records'>";
                    echo $search ? "No records found matching: " . htmlspecialchars($search) : "No records found";
                    echo "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        // Function to remove URL parameters
        function removeURLParameters() {
            const urlWithoutParams = window.location.pathname;
            window.history.replaceState({}, document.title, urlWithoutParams);
        }

        // Auto close message after 2 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const message = document.getElementById('autoCloseMessage');
            if (message) {
                setTimeout(function() {
                    message.style.opacity = '0';
                    setTimeout(function() {
                        message.style.display = 'none';
                        // Remove URL parameters after message is hidden
                        removeURLParameters(); 
                    }, 300);
                }, 2000);
            }
        });

        // Function to close message manually
        function closeMessage() {
            const message = document.getElementById('autoCloseMessage');
            if (message) {
                message.style.opacity = '0';
                setTimeout(function() {
                    message.style.display = 'none';
                    // Remove URL parameters after message is hidden
                    removeURLParameters(); 
                }, 300);
            }
        }
    </script>
</body>
</html>
