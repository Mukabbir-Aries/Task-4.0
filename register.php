<?php
require_once 'config.php';

$email = '';
$errors = [];

// Get email from URL parameter
if (isset($_GET['email'])) {
    $email = sanitize($_GET['email']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect('index.php');
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        redirect('login.php?email=' . urlencode($email));
    }
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $firstName = sanitize($_POST['first_name']);
    $lastName = sanitize($_POST['last_name']);
    $country = sanitize($_POST['country']);
    $dob = sanitize($_POST['date_of_birth']);
    $gender = sanitize($_POST['gender']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate password
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }
    
    // Validate other fields
    if (empty($firstName)) {
        $errors[] = "First name is required";
    }
    
    if (empty($country)) {
        $errors[] = "Country is required";
    }
    
    // If no errors, create user
    if (empty($errors)) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Generate verification token
        $verificationToken = generateToken();
        
        // Insert user into database
        $stmt = $conn->prepare("
            INSERT INTO users (email, password, first_name, last_name, country, date_of_birth, gender, verification_token)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssssss", $email, $hashedPassword, $firstName, $lastName, $country, $dob, $gender, $verificationToken);
        
        if ($stmt->execute()) {
            // Get the new user ID
            $userId = $conn->insert_id;
            
            // Log the registration
            logActivity($userId, 'registration', 'User registered successfully');
            
            // Set session
            $_SESSION['user_id'] = $userId;
            $_SESSION['first_name'] = $firstName;
            
            // Redirect to dashboard
            redirect('dashboard.php?welcome=1');
        } else {
            $errors[] = "Registration failed: " . $conn->error;
        }
    }
}

// Get list of countries
$countries = [
    'Bangladesh', 'India', 'Pakistan', 'Nepal', 'Sri Lanka', 'Bhutan', 'Maldives',
    'United States', 'United Kingdom', 'Canada', 'Australia', 'Germany', 'France'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <?php include 'includes/header.php'; ?>
        
        <div class="auth-container">
            <div class="auth-card">
                <h2>Create your account</h2>
                
                <?php if (!empty($errors)): ?>
                    <div class="error-box">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="register.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                            <small>Must be at least 8 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select id="country" name="country" required>
                            <option value="">Select your country</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth">
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender">
                                <option value="">Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                                <option value="Prefer not to say">Prefer not to say</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group terms">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a></label>
                    </div>
                    
                    <button type="submit" class="continue-btn">Create Account</button>
                </form>
                
                <div class="auth-footer">
                    Already have an account? <a href="login.php">Log in</a>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>