<header>
    <div class="logo">
        <i class="fas fa-poll"></i>
        <span>Task Buddy</span>
    </div>
    
    <?php if (isLoggedIn()): ?>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="surveys.php">Surveys</a></li>
                <li><a href="rewards.php">Rewards</a></li>
                <li><a href="help.php">Help</a></li>
                <li class="user-menu">
                    <div class="user-menu-toggle">
                        <div class="user-avatar">
                            <?php echo substr($_SESSION['first_name'], 0, 1); ?>
                        </div>
                        <span><?php echo htmlspecialchars($_SESSION['first_name']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="user-menu-dropdown">
                        <ul>
                            <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                            <li><a href="cashout.php"><i class="fas fa-wallet"></i> Cash Out</a></li>
                            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                            <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
                        </ul>
                    </div>
                </div>
            </ul>
        </nav>
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    <?php else: ?>
        <nav>
            <ul>
                <li><a href="index.php#how-it-works">How it works</a></li>
                <li><a href="rewards.php">Rewards</a></li>
                <li class="language">
                    <i class="fas fa-globe"></i>
                    <span>English</span>
                </li>
                <li><a href="login.php" class="login-btn">Log in or Sign up</a></li>
            </ul>
        </nav>
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    <?php endif; ?>
    
    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <div class="mobile-menu-header">
            <div class="logo">
                <i class="fas fa-poll"></i>
                <span>Task Buddy</span>
            </div>
            <div class="mobile-menu-close">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <div class="mobile-menu-content">
            <ul>
                <?php if (isLoggedIn()): ?>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="surveys.php"><i class="fas fa-poll"></i> Surveys</a></li>
                    <li><a href="rewards.php"><i class="fas fa-gift"></i> Rewards</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                    <li><a href="cashout.php"><i class="fas fa-wallet"></i> Cash Out</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="help.php"><i class="fas fa-question-circle"></i> Help</a></li>
                <?php else: ?>
                    <li><a href="index.php#how-it-works"><i class="fas fa-info-circle"></i> How it works</a></li>
                    <li><a href="rewards.php"><i class="fas fa-gift"></i> Rewards</a></li>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Log in</a></li>
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> Sign up</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <?php if (isLoggedIn()): ?>
            <div class="mobile-menu-footer">
                <a href="logout.php" class="continue-btn"><i class="fas fa-sign-out-alt"></i> Log Out</a>
            </div>
        <?php endif; ?>
    </div>
</header>
