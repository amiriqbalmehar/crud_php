<!-- Optimized Header -->
<?php include "../header.php"?>

<div class="container">
    <h1 class="text-center">Data to perform CRUD Operations</h1>
    <a href="create.php" class='btn btn-outline-dark mb-2'> <i class="bi bi-person-plus"></i> Create New User</a>

    <?php
    // Display messages
    if (isset($_GET['message'])) {
        switch($_GET['message']) {
            case 'deleted':
                echo "<div class='alert alert-success'>User deleted successfully!</div>";
                break;
        }
    }
    
    if (isset($_GET['error'])) {
        switch($_GET['error']) {
            case 'delete_failed':
                echo "<div class='alert alert-danger'>Failed to delete user.</div>";
                break;
            case 'user_not_found':
                echo "<div class='alert alert-warning'>User not found.</div>";
                break;
            case 'database_error':
                echo "<div class='alert alert-danger'>Database error occurred.</div>";
                break;
            case 'invalid_request':
                echo "<div class='alert alert-danger'>Invalid request.</div>";
                break;
            case 'invalid_id':
                echo "<div class='alert alert-danger'>Invalid user ID.</div>";
                break;
        }
    }
    ?>
    // Pagination for better performance with large datasets
    $limit = 10; // Number of records per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Get total count for pagination
    $count_query = "SELECT COUNT(*) as total FROM users";
    $count_result = $conn->query($count_query);
    $total_records = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_records / $limit);

    // Optimized query with prepared statement and pagination
    $query = "SELECT id, username, email, password FROM users LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
    ?>
    
    <table class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Password</th>
                <th scope="col" colspan="3" class="text-center">CRUD Operations</th>
            </tr>  
        </thead>
        <tbody>
            <?php
            while($row = $result->fetch_assoc()) {
                $id = htmlspecialchars($row['id']);
                $user = htmlspecialchars($row['username']);
                $email = htmlspecialchars($row['email']);
                $pass = htmlspecialchars($row['password']);

                echo "<tr>";
                echo "<th scope='row'>{$id}</th>";
                echo "<td>{$user}</td>";
                echo "<td>{$email}</td>";
                echo "<td>{$pass}</td>";
                echo "<td class='text-center'><a href='view.php?user_id={$id}' class='btn btn-primary'><i class='bi bi-eye'></i> View</a></td>";
                echo "<td class='text-center'><a href='update.php?edit&user_id={$id}' class='btn btn-secondary'><i class='bi bi-pencil'></i> EDIT</a></td>";
                echo "<td class='text-center'><a href='delete.php?delete={$id}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this user?\")'><i class='bi bi-trash'></i> DELETE</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page-1; ?>">Previous</a>
                </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page+1; ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>

    <?php
        } else {
            echo "<p class='text-center'>No users found.</p>";
        }
        $stmt->close();
    } else {
        echo "<p class='text-center text-danger'>Error loading users.</p>";
    }
    ?>
</div>

<!-- Back button -->
<div class="container text-center mt-5">
    <a href="../index.php" class="btn btn-warning mt-5">Back</a>
</div>

<?php
// Clean output buffer
if (ob_get_level()) {
    ob_end_flush();
}
?>

