<!-- Optimized Header -->
<?php include "../header.php" ?>

<?php 
// Start session for CSRF protection
session_start();

if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $userid = (int)$_GET['delete'];
    
    // Verify CSRF token if provided (for enhanced security)
    $csrf_valid = true;
    if (isset($_GET['csrf']) && isset($_SESSION['csrf_token'])) {
        $csrf_valid = ($_GET['csrf'] === $_SESSION['csrf_token']);
    }
    
    if ($csrf_valid) {
        // Check if user exists before deletion
        $check_query = "SELECT id FROM users WHERE id = ?";
        $check_stmt = $conn->prepare($check_query);
        
        if ($check_stmt) {
            $check_stmt->bind_param("i", $userid);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Prepared statement for secure deletion
                $delete_query = "DELETE FROM users WHERE id = ?"; 
                $delete_stmt = $conn->prepare($delete_query);
                
                if ($delete_stmt) {
                    $delete_stmt->bind_param("i", $userid);
                    
                    if ($delete_stmt->execute()) {
                        // Log the deletion for audit purposes
                        error_log("User ID {$userid} deleted successfully");
                        header("Location: home.php?message=deleted");
                    } else {
                        header("Location: home.php?error=delete_failed");
                    }
                    $delete_stmt->close();
                } else {
                    header("Location: home.php?error=database_error");
                }
            } else {
                header("Location: home.php?error=user_not_found");
            }
            $check_stmt->close();
        } else {
            header("Location: home.php?error=database_error");
        }
    } else {
        header("Location: home.php?error=invalid_request");
    }
} else {
    header("Location: home.php?error=invalid_id");
}
exit;
?>

  