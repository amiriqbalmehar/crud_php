<?php include "../header.php" ?>

<?php 
// Generate CSRF token for security
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if(isset($_POST['create'])) {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token mismatch");
    }
    
    // Validate and sanitize input
    $user = sanitize_input($_POST['user']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = $_POST['pass'];
    
    // Validation
    $errors = [];
    if (empty($user)) $errors[] = "Username is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (strlen($pass) < 6) $errors[] = "Password must be at least 6 characters";
    
    if (empty($errors)) {
        // Hash password for security
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
        
        // Check if email already exists
        $check_query = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows == 0) {
            // Prepared statement for secure insertion
            $query = "INSERT INTO users(username, email, password) VALUES(?, ?, ?)";
            $stmt = $conn->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("sss", $user, $email, $hashed_password);
                
                if ($stmt->execute()) {
                    echo "<script type='text/javascript'>alert('User added successfully!'); window.location='home.php';</script>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
        } else {
            $errors[] = "Email already exists";
        }
        $check_stmt->close();
    }
    
    if (!empty($errors)) {
        echo "<div class='alert alert-danger'>" . implode("<br>", $errors) . "</div>";
    }
}
?>

<h1 class="text-center">Add User Details</h1>
<div class="container">
    <form action="" method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="form-group">
            <label for="user" class="form-label">Username*</label>
            <input type="text" name="user" id="user" class="form-control" required maxlength="255" value="<?php echo isset($_POST['user']) ? htmlspecialchars($_POST['user']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email ID*</label>
            <input type="email" name="email" id="email" class="form-control" required maxlength="255" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
    
        <div class="form-group">
            <label for="pass" class="form-label">Password*</label>
            <input type="password" name="pass" id="pass" class="form-control" required minlength="6" maxlength="255">
            <small class="form-text text-muted">Password must be at least 6 characters long</small>
        </div>    

        <div class="form-group">
            <input type="submit" name="create" class="btn btn-primary mt-2" value="Submit">
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
.pagination{display:flex;padding-left:0;list-style:none;border-radius:.25rem}
.page-item{display:block}
.page-link{position:relative;display:block;padding:.5rem .75rem;margin-left:-1px;line-height:1.25;color:#007bff;background-color:#fff;border:1px solid #dee2e6;text-decoration:none}
.page-link:hover{z-index:2;color:#0056b3;background-color:#e9ecef;border-color:#dee2e6}
.page-item.active .page-link{z-index:1;color:#fff;background-color:#007bff;border-color:#007bff}
.justify-content-center{justify-content:center!important}
</style>