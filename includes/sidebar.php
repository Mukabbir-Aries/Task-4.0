<aside class="sidebar">
    <div class="sidebar-header">
        <div class="user-profile">
            <div class="user-avatar-large">
                <?php echo substr($_SESSION['first_name'], 0, 1); ?>
            </div>
            <div class="user-info">
                <h3><?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></h3>
                <p><?php echo htmlspecialchars($userData['email']); ?></p>
            </div>
        </div>
    </div>
    <nav class="sidebar-menu">
        <ul>
            <li>
                <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="surveys.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'surveys.php' ? 'active' : ''; ?>">
                    <i class="fas fa-poll"></i>
                    <span>Surveys</span>
                </a>
            </li>
            <li>
                <a href="cashout.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'cashout.php' ? 'active' : ''; ?>">
                    <i class="fas fa-wallet"></i>
                    <span>Cash Out</span>
                </a>
            </li>
            <li>
                <a href="history.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i>
                    <span>History</span>
                </a>
            </li>
            <li>
                <a href="referrals.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'referrals.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-plus"></i>
                    <span>Referrals</span>
                </a>
            </li>
            <li>
                <a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li>
                <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a href="help.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'help.php' ? 'active' : ''; ?>">
                    <i class="fas fa-question-circle"></i>
                    <span>Help & Support</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>