<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'task_buddy');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Site configuration
define('SITE_NAME', 'Task Buddy');
define('SITE_URL', 'http://localhost/task_buddy');
define('ADMIN_EMAIL', 'admin@primeopinion.com');

// Session configuration
session_start();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to redirect to a URL
function redirect($url) {
    header("Location: $url");
    exit;
}

// Function to sanitize input data
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Function to generate a random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Function to format currency
function formatCurrency($amount) {
    return '৳' . number_format($amount, 2);
}

// Function to get user data
function getUserData($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return false;
}

// Function to check if a survey is available for a user
function isSurveyAvailable($surveyId, $userId) {
    global $conn;
    
    // Get user data
    $userData = getUserData($userId);
    if (!$userData) return false;
    
    // Get survey data
    $stmt = $conn->prepare("SELECT * FROM surveys WHERE id = ? AND is_active = TRUE AND (expires_at IS NULL OR expires_at > NOW())");
    $stmt->bind_param("i", $surveyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) return false;
    
    $survey = $result->fetch_assoc();
    
    // Check if user already took this survey
    $stmt = $conn->prepare("SELECT id FROM survey_responses WHERE user_id = ? AND survey_id = ?");
    $stmt->bind_param("ii", $userId, $surveyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) return false;
    
    // Check age restrictions if user has date of birth
    if ($userData['date_of_birth']) {
        $age = date_diff(date_create($userData['date_of_birth']), date_create('today'))->y;
        if ($age < $survey['min_age'] || $age > $survey['max_age']) return false;
    }
    
    // Check country restrictions if survey has country limitations
    if (!empty($survey['countries'])) {
        $allowedCountries = explode(',', $survey['countries']);
        if (!in_array($userData['country'], $allowedCountries)) return false;
    }
    
    return true;
}

// Function to log activity
function logActivity($userId, $action, $details = '') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $stmt->bind_param("isss", $userId, $action, $details, $ipAddress);
    $stmt->execute();
}
?>
