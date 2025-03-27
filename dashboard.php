<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get user data
$userId = $_SESSION['user_id'];
$userData = getUserData($userId);

if (!$userData) {
    // Invalid user ID in session
    session_destroy();
    redirect('login.php');
}

// Get available surveys
$stmt = $conn->prepare("
    SELECT s.*, c.name as category_name, c.icon as category_icon
    FROM surveys s
    LEFT JOIN categories c ON s.category_id = c.id
    WHERE s.is_active = TRUE 
    AND (s.expires_at IS NULL OR s.expires_at > NOW())
    AND (s.countries IS NULL OR s.countries LIKE ?)
    AND NOT EXISTS (
        SELECT 1 FROM survey_responses 
        WHERE survey_id = s.id AND user_id = ?
    )
    ORDER BY s.reward DESC
    LIMIT 10
");
$countryPattern = '%' . $userData['country'] . '%';
$stmt->bind_param("si", $countryPattern, $userId);
$stmt->execute();
$availableSurveys = $stmt->get_result();

// Get completed surveys
$stmt = $conn->prepare("
    SELECT s.title, s.reward, sr.completed_at, sr.status
    FROM survey_responses sr
    JOIN surveys s ON sr.survey_id = s.id
    WHERE sr.user_id = ?
    ORDER BY sr.completed_at DESC
    LIMIT 5
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$completedSurveys = $stmt->get_result();

// Get user balance
$balance = $userData['balance'];

// Get welcome message flag
$showWelcome = isset($_GET['welcome']) && $_GET['welcome'] == 1;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Task Buddy</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <?php include 'includes/header.php'; ?>
        
        <!-- Welcome Modal -->
        <?php if ($showWelcome): ?>
        <div class="modal" id="welcomeModal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Welcome to Task Buddy!</h2>
                <p>Thank you for joining our community. Here's what you can do now:</p>
                <ul>
                    <li>Complete your profile to get more survey opportunities</li>
                    <li>Take your first survey and start earning</li>
                    <li>Invite friends to earn bonus rewards</li>
                </ul>
                <div class="bonus-badge">
                    <i class="fas fa-gift"></i>
                    <span>We've added ৳50 to your account as a welcome bonus!</span>
                </div>
                <button class="continue-btn" id="startSurveys">Start Taking Surveys</button>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="dashboard-container">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="dashboard-content">
                <div class="dashboard-header">
                    <h1>Welcome back, <?php echo htmlspecialchars($userData['first_name']); ?>!</h1>
                    <div class="balance-card">
                        <div class="balance-label">Your Balance</div>
                        <div class="balance-amount"><?php echo formatCurrency($balance); ?></div>
                        <a href="cashout.php" class="cashout-btn">Cash Out</a>
                    </div>
                </div>
                
                <div class="dashboard-grid">
                    <!-- Available Surveys -->
                    <div class="dashboard-card surveys-card">
                        <div class="card-header">
                            <h2>Available Surveys</h2>
                            <a href="surveys.php" class="view-all">View All</a>
                        </div>
                        
                        <?php if ($availableSurveys->num_rows > 0): ?>
                            <div class="survey-list">
                                <?php while ($survey = $availableSurveys->fetch_assoc()): ?>
                                    <div class="survey-item">
                                        <div class="survey-icon">
                                            <i class="fas fa-<?php echo $survey['category_icon'] ?: 'poll'; ?>"></i>
                                        </div>
                                        <div class="survey-details">
                                            <h3><?php echo htmlspecialchars($survey['title']); ?></h3>
                                            <div class="survey-meta">
                                                <span><i class="fas fa-clock"></i> <?php echo $survey['duration_minutes']; ?> min</span>
                                                <span><i class="fas fa-list"></i> <?php echo $survey['total_questions']; ?> questions</span>
                                                <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($survey['category_name']); ?></span>
                                            </div>
                                        </div>
                                        <div class="survey-reward">
                                            <div class="reward-amount"><?php echo formatCurrency($survey['reward']); ?></div>
                                            <a href="take-survey.php?id=<?php echo $survey['id']; ?>" class="take-survey-btn">Take Survey</a>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-search"></i>
                                <p>No surveys available right now. Check back soon!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="dashboard-card activity-card">
                        <div class="card-header">
                            <h2>Recent Activity</h2>
                            <a href="activity.php" class="view-all">View All</a>
                        </div>
                        
                        <?php if ($completedSurveys->num_rows > 0): ?>
                            <div class="activity-list">
                                <?php while ($activity = $completedSurveys->fetch_assoc()): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon <?php echo $activity['status']; ?>">
                                            <?php if ($activity['status'] === 'completed'): ?>
                                                <i class="fas fa-check"></i>
                                            <?php elseif ($activity['status'] === 'disqualified'): ?>
                                                <i class="fas fa-times"></i>
                                            <?php else: ?>
                                                <i class="fas fa-clock"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="activity-details">
                                            <h3><?php echo htmlspecialchars($activity['title']); ?></h3>
                                            <div class="activity-date">
                                                <?php echo date('M j, Y g:i A', strtotime($activity['completed_at'])); ?>
                                            </div>
                                        </div>
                                        <div class="activity-reward">
                                            <?php if ($activity['status'] === 'completed'): ?>
                                                <span class="reward-amount">+<?php echo formatCurrency($activity['reward']); ?></span>
                                            <?php elseif ($activity['status'] === 'disqualified'): ?>
                                                <span class="disqualified">Disqualified</span>
                                            <?php else: ?>
                                                <span class="pending">Pending</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-history"></i>
                                <p>No activity yet. Start taking surveys to see your history here.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Referral Banner -->
                <div class="referral-banner">
                    <div class="referral-content">
                        <h2>Invite Friends & Earn More</h2>
                        <p>Get ৳100 for each friend who joins and completes their first survey!</p>
                        <div class="referral-link">
                            <input type="text" value="<?php echo SITE_URL; ?>/?ref=<?php echo $userId; ?>" readonly>
                            <button class="copy-btn" id="copyReferral"><i class="fas fa-copy"></i> Copy</button>
                        </div>
                    </div>
                    <div class="referral-image">
                        <img src="assets/images/referral.png" alt="Refer friends">
                    </div>
                </div>
            </main>
        </div>
        
        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Welcome modal functionality
        const welcomeModal = document.getElementById('welcomeModal');
        if (welcomeModal) {
            const closeBtn = document.querySelector('.close');
            const startBtn = document.getElementById('startSurveys');
            
            closeBtn.onclick = function() {
                welcomeModal.style.display = "none";
            }
            
            startBtn.onclick = function() {
                welcomeModal.style.display = "none";
                document.querySelector('.surveys-card').scrollIntoView({ behavior: 'smooth' });
            }
            
            window.onclick = function(event) {
                if (event.target == welcomeModal) {
                    welcomeModal.style.display = "none";
                }
            }
        }
        
        // Copy referral link
        const copyBtn = document.getElementById('copyReferral');
        if (copyBtn) {
            copyBtn.onclick = function() {
                const referralInput = document.querySelector('.referral-link input');
                referralInput.select();
                document.execCommand('copy');
                
                copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => {
                    copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copy';
                }, 2000);
            }
        }
    </script>
</body>
</html>
