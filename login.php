<?php
require_once 'config.php';

$email = '';
$error = '';

// Get email from URL parameter
if (isset($_GET['email'])) {
    $email = sanitize($_GET['email']);
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Validate credentials
    $stmt = $conn->prepare("SELECT id, password, first_name, is_active FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Check if account is active
        if (!$user['is_active']) {
            $error = "Your account has been deactivated. Please contact support.";
        } 
        // Verify password
        else if (password_verify($password, $user['password'])) {
            // Update last login time
            $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
            
            // Log the login
            logActivity($user['id'], 'login', 'User logged in successfully');
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            
            // Redirect to dashboard
            redirect('dashboard.php');
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Email not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <?php include 'includes/header.php'; ?>
        
        <div class="auth-container">
            <div class="auth-card">
                <h2>Log in to your account</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="error-box">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <div class="forgot-password">
                            <a href="forgot-password.php">Forgot password?</a>
                        </div>
                    </div>
                    
                    <div class="form-group remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    
                    <button type="submit" class="continue-btn">Log in</button>
                </form>
                
                <div class="divider">
                    <span>or</span>
                </div>
                
                <div class="social-login">
                    <button class="social-btn google">
                        <i class="fab fa-google"></i>
                        Continue with Google
                    </button>
                    <button class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                        Continue with Facebook
                    </button>
                    <button class="social-btn apple">
                        <i class="fab fa-apple"></i>
                        Continue with Apple
                    </button>
                </div>
                
                <div class="auth-footer">
                    Don't have an account? <a href="register.php">Sign up</a>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>