<!-- Optimized Header -->
<?php include "../header.php"?>

<?php
// Generate CSRF token for security
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user = $email = $pass = '';
$userid = 0;

// Validate and get user ID
if(isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $userid = (int)$_GET['user_id']; 
    
    // Fetch existing user data with prepared statement
    $query = "SELECT id, username, email, password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user = $row['username'];
            $email = $row['email'];
            $pass = $row['password'];
        } else {
            echo "<script>alert('User not found!'); window.location='home.php';</script>";
            exit;
        }
        $stmt->close();
    }
} else {
    echo "<script>alert('Invalid user ID!'); window.location='home.php';</script>";
    exit;
}

// Process form submission
if(isset($_POST['update'])) {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token mismatch");
    }
    
    // Validate and sanitize input
    $new_user = sanitize_input($_POST['user']);
    $new_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $new_pass = $_POST['pass'];
    
    // Validation
    $errors = [];
    if (empty($new_user)) $errors[] = "Username is required";
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (strlen($new_pass) < 6) $errors[] = "Password must be at least 6 characters";
    
    if (empty($errors)) {
        // Hash password for security
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
        
        // Check if email already exists for other users
        $check_query = "SELECT id FROM users WHERE email = ? AND id != ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("si", $new_email, $userid);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows == 0) {
            // Prepared statement for secure update
            $query = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($query);
            
            if ($update_stmt) {
                $update_stmt->bind_param("sssi", $new_user, $new_email, $hashed_password, $userid);
                
                if ($update_stmt->execute()) {
                    echo "<script type='text/javascript'>alert('User data updated successfully!'); window.location='home.php';</script>";
                } else {
                    echo "<div class='alert alert-danger'>Error updating user: " . $update_stmt->error . "</div>";
                }
                $update_stmt->close();
            }
        } else {
            $errors[] = "Email already exists for another user";
        }
        $check_stmt->close();
    }
    
    if (!empty($errors)) {
        echo "<div class='alert alert-danger'>" . implode("<br>", $errors) . "</div>";
    }
}             
?>

<h1 class="text-center">Update User Details</h1>
<div class="container">
    <form action="" method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="form-group">
            <label for="user" class="form-label">Username*</label>
            <input type="text" name="user" id="user" class="form-control" required maxlength="255" value="<?php echo htmlspecialchars(isset($_POST['user']) ? $_POST['user'] : $user); ?>">
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email ID*</label>
            <input type="email" name="email" id="email" class="form-control" required maxlength="255" value="<?php echo htmlspecialchars(isset($_POST['email']) ? $_POST['email'] : $email); ?>">
            <small class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
    
        <div class="form-group">
            <label for="pass" class="form-label">New Password*</label>
            <input type="password" name="pass" id="pass" class="form-control" required minlength="6" maxlength="255">
            <small class="form-text text-muted">Enter a new password (minimum 6 characters)</small>
        </div>    

        <div class="form-group">
            <input type="submit" name="update" class="btn btn-primary mt-2" value="Update">
        </div>
    </form>    
</div>

<!-- Back button -->
<div class="container text-center mt-5">
    <a href="home.php" class="btn btn-warning mt-5">Back</a>
</div>

<style>
.alert{padding:.75rem 1.25rem;margin-bottom:1rem;border:1px solid transparent;border-radius:.25rem}
.alert-danger{color:#721c24;background-color:#f8d7da;border-color:#f5c6cb}
</style>

