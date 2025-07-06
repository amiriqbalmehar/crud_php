<!-- Optimized Header -->
<?php include '../header.php'?>

<h1 class="text-center">User Details</h1>
<div class="container">
    <?php
    // Validate and sanitize input
    if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
        $userid = (int)$_GET['user_id']; 

        // Prepared statement for secure data retrieval
        $query = "SELECT id, username, email, password FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("i", $userid);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id = htmlspecialchars($row['id']);
                $user = htmlspecialchars($row['username']);
                $email = htmlspecialchars($row['email']);
                $pass = htmlspecialchars($row['password']);
    ?>
    
    <table class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Password</th>
            </tr>  
        </thead>
        <tbody>
            <tr>
                <td><?php echo $id; ?></td>
                <td><?php echo $user; ?></td>
                <td><?php echo $email; ?></td>
                <td><?php echo $pass; ?></td>
            </tr>
        </tbody>
    </table>
    
    <?php
            } else {
                echo "<div class='alert alert-warning'>User not found.</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='alert alert-danger'>Database error occurred.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Invalid user ID provided.</div>";
    }
    ?>
</div>

<!-- Back Button -->
<div class="container text-center mt-5">
    <a href="home.php" class="btn btn-warning mt-5">Back</a>
</div>

<style>
.alert{padding:.75rem 1.25rem;margin-bottom:1rem;border:1px solid transparent;border-radius:.25rem}
.alert-danger{color:#721c24;background-color:#f8d7da;border-color:#f5c6cb}
.alert-warning{color:#856404;background-color:#fff3cd;border-color:#ffeaa7}
</style>

