<?php
require_once 'config.php';

// Get available surveys count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM surveys WHERE is_active = TRUE AND (expires_at IS NULL OR expires_at > NOW())");
$stmt->execute();
$result = $stmt->get_result();
$surveyCount = $result->fetch_assoc()['count'];

// Get average earnings from yesterday
$yesterday = date('Y-m-d', strtotime('-1 day'));
$stmt = $conn->prepare("
    SELECT AVG(surveys.reward) as avg_reward
    FROM survey_responses
    JOIN surveys ON survey_responses.survey_id = surveys.id
    WHERE DATE(survey_responses.completed_at) = ? AND survey_responses.status = 'completed'
");
$stmt->bind_param("s", $yesterday);
$stmt->execute();
$result = $stmt->get_result();
$avgEarnings = $result->fetch_assoc()['avg_reward'] ?: 0;

// Get recent payouts for the live ticker
$stmt = $conn->prepare("
    SELECT pm.name, pr.amount, u.country
    FROM payment_requests pr
    JOIN payment_methods pm ON pr.payment_method_id = pm.id
    JOIN users u ON pr.user_id = u.id
    WHERE pr.status = 'paid'
    ORDER BY pr.processed_at DESC
    LIMIT 10
");
$stmt->execute();
$recentPayouts = $stmt->get_result();

// Check if form was submitted
$formSubmitted = false;
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = sanitize($_POST['email']);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // User exists, redirect to login
            redirect("login.php?email=" . urlencode($email));
        } else {
            // New user, redirect to registration
            redirect("register.php?email=" . urlencode($email));
        }
    } else {
        $formError = "Please enter a valid email address";
    }
    
    $formSubmitted = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Buddy - Get Paid for Taking Surveys</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <?php include 'includes/header.php'; ?>

        <!-- Live Payouts -->
        <div class="live-payouts">
            <div class="payout-label">Live Payouts</div>
            <div class="payout-items">
                <?php while ($payout = $recentPayouts->fetch_assoc()): ?>
                    <div class="payout-item">
                        <div class="payout-icon <?php echo strtolower($payout['name']); ?>"></div>
                        <span><?php echo htmlspecialchars($payout['name']); ?></span>
                        <span class="amount"><?php echo formatCurrency($payout['amount']); ?></span>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Main Content -->
        <main>
            <div class="content-grid">
                <!-- Left Column -->
                <div class="left-column">
                    <h1>
                        <span class="purple">Get paid</span> for taking
                        <br>
                        <span class="navy">free surveys</span>
                    </h1>
                    <p class="subtitle">
                        Your opinions are valuable, and businesses are
                        <br>
                        willing to pay for them. Earn cash today!
                    </p>

                    <div class="info-card">
                        <div class="icon-container">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <span class="bold">Earn up to ৳500 per survey!</span>
                        </div>
                    </div>

                    <div class="survey-count">
                        <span><?php echo $surveyCount; ?> Surveys available in</span>
                        <div class="flag">
                            <div class="flag-icon">*</div>
                            <span>Bangladesh</span>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="icon-container round">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div class="bold">The average user earned ৳<?php echo number_format($avgEarnings, 0); ?> yesterday</div>
                            <div class="small">Cash out from ৳500</div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Sign Up Form -->
                <div class="right-column">
                    <div class="signup-card">
                        <div class="bonus-badge">
                            <i class="fas fa-globe"></i>
                            <span>Sign Up and get up to ৳100 FREE BONUS!</span>
                        </div>
                        
                        <h2>Log in or sign up</h2>
                        
                        <?php if ($formError): ?>
                            <div class="error-message"><?php echo $formError; ?></div>
                        <?php endif; ?>
                        
                        <form action="index.php" method="POST">
                            <div class="form-group">
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                            
                            <button type="submit" class="continue-btn">Continue</button>
                        </form>
                        
                        <div class="social-login">
                            <button class="social-btn google">
                                <i class="fab fa-google"></i>
                                Google
                            </button>
                            <button class="social-btn facebook">
                                <i class="fab fa-facebook-f"></i>
                                Facebook
                            </button>
                            <button class="social-btn apple">
                                <i class="fab fa-apple"></i>
                                Apple
                            </button>
                        </div>
                        
                        <div class="reviews">
                            <p>Check out our 18,897 reviews</p>
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="trustpilot">
                                <i class="fas fa-star"></i>
                                <span>Trustpilot</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Text -->
            <div class="bottom-text">
                <p>
                    Simply by voicing your opinions on current affairs, what washing-up liquid you use or what TV you prefer to
                    watch, you can put some extra money in your back pocket just by using a paid surveys app.
                </p>
            </div>

            <!-- Payment Methods -->
            <div class="payment-methods">
                <?php
                $paymentMethods = ['bank', 'bkash', 'nagad', 'rocket', 'gift', 'spotify', 'google-play', 'ebay'];
                foreach ($paymentMethods as $method):
                ?>
                    <div class="payment-method">
                        <div class="payment-icon <?php echo $method; ?>">
                            <?php echo $method; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <!-- Bottom Section -->
        <section class="bottom-section">
            <h2>
                Earn money by taking
                <br>
                surveys in <span class="highlight">Bangladesh</span>
            </h2>
            <p>
                Make money online by taking exciting surveys on Task Buddy.
                <br>
                Withdraw cash or gift cards and receive them immediately
            </p>
        </section>
        
        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>
