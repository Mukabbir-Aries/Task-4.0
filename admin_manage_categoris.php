<?php
// **IMPORTANT: session_start() MUST be the very first thing in your PHP file, before any HTML or other output!**
session_start();

$admin_name = "Admin"; // Default admin name, since we're not fetching from a database

//  index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SurvLab Dashboard</title>
    <style>
        /* Basic Reset and Font */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: sans-serif;
            background-color: #f4f6f8;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background-color: #242933;
            color: #b8c2cc;
            width: 250px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar-logo {
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            color: #b8c2cc;
            padding: 10px 15px;
            text-decoration: none;
            margin-bottom: 5px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar-menu li a:hover {
            background-color: #373e4a;
            color: #fff;
        }
        .sidebar-menu li a i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        .sidebar-menu li.active a {
            background-color: #4a5568;
            color: #fff;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background-color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .header-search {
            display: flex;
            align-items: center;
            background-color: #f0f2f5;
            border-radius: 5px;
            padding: 5px 10px;
        }
        .header-search input {
            border: none;
            background: transparent;
            padding: 8px;
            outline: none;
        }
        .header-search i {
            color: #718096;
            margin-right: 5px;
        }
        .header-actions {
            display: flex;
            align-items: center;
        }
        .header-actions a {
            color: #718096;
            margin-left: 15px;
            text-decoration: none;
            font-size: 1.1rem;
        }
        .header-actions .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #cbd5e0; /* Placeholder */
            display: flex;
            justify-content: center;
            align-items: center;
            color: #4a5568;
            font-weight: bold;
            margin-left: 15px;
        }

        /* Dashboard Content */
        .dashboard-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Info Cards */
        .info-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #718096;
            font-size: 0.9rem;
        }
        .card-header i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        .card-body {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2d3748;
        }
        .card-footer {
            display: flex;
            justify-content: flex-end;
            color: #4a5568;
            font-size: 0.8rem;
            margin-top: 15px;
        }
        .card-footer i {
            margin-left: 5px;
        }

        /* Deposits & Withdrawals Section */
        .deposits-withdrawals {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .deposits-withdrawals-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .dw-header {
            color: #718096;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .dw-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .dw-label {
            color: #4a5568;
        }
        .dw-value {
            font-weight: bold;
            color: #2d3748;
        }
        .dw-progress {
            background-color: #edf2f7;
            border-radius: 5px;
            height: 8px;
            overflow: hidden;
            margin-top: 5px;
        }
        .dw-progress-bar {
            background-color: #48bb78; /* Example color */
            height: 100%;
            width: 70%; /* Example width */
            border-radius: 5px;
        }

        /* Survey Status Section */
        .survey-status {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .survey-status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            text-align: center;
        }
        .survey-status-item {
            padding: 15px;
            border-radius: 5px;
            background-color: #f7fafc;
            color: #4a5568;
        }
        .survey-status-item .value {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2d3748;
        }
        .survey-status-item .label {
            font-size: 0.9rem;
        }
        .survey-status-item a {
            display: block;
            margin-top: 10px;
            color: #4299e1;
            text-decoration: none;
            font-size: 0.8rem;
        }

        /* Report Sections (Simplified) */
        .report-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            color: #718096;
            font-weight: bold;
        }
        /* Add more specific styling for tables and charts as needed */

        /* Responsive Design (Basic) */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                padding: 10px;
                margin-bottom: 20px;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
            .sidebar-logo {
                margin-bottom: 0;
            }
            .sidebar-menu {
                display: flex;
                overflow-x: auto;
            }
            .sidebar-menu li {
                margin-right: 10px;
            }
            .main-content {
                padding: 10px;
            }
            .dashboard-content {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            .deposits-withdrawals {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">SurvLab</div>
        <ul class="sidebar-menu">
            <li class="active"><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="categories.php"><i class="fas fa-folder"></i> Manage Categories</a></li>
            <li><a href="#"><i class="fas fa-list-alt"></i> Manage Surveys</a></li>
            <li><a href="#"><i class="fas fa-user-cog"></i> Manage Users</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Manage Surveyors</a></li>
            <li>
                <a href="#">
                    <i class="fas fa-money-bill-wave"></i> Deposits
                    <?php if (true): ?>
                        <i class="fas fa-caret-down"></i>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-money-bill-alt"></i> Withdrawals
                    <?php if (true): ?>
                        <i class="fas fa-caret-down"></i>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="#"><i class="fas fa-ticket-alt"></i> Support Ticket</a></li>
            <li><a href="#"><i class="fas fa-chart-bar"></i> Report</a></li>
            <li><a href="#"><i class="fas fa-bell"></i> Subscribers</a></li>
            <li><a href="#"><i class="fas fa-cog"></i> System Setting</a></li>
            <li><a href="#"><i class="fas fa-ellipsis-h"></i> Extra</a></li>
            <li><a href="#"><i class="fas fa-flag"></i> Report & Request</a></li>
        </ul>
        <div style="margin-top: auto; font-size: 0.8rem; color: #718096;">SurvLab v2.0</div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="header-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search here...">
            </div>
            <div class="header-actions">
                <a href="#"><i class="fas fa-bell"></i></a>
                <a href="#"><i class="fas fa-envelope"></i></a>
                <div class="user-avatar"><?php echo $admin_name; ?></div>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="info-card">
                <div class="card-header"><i class="fas fa-users"></i> Total Users</div>
                <div class="card-body">
                    <?php
                    $totalUsers = 782;
                    echo $totalUsers;
                    ?>
                </div>
                <div class="card-footer">View Details <i class="fas fa-arrow-right"></i></div>
            </div>
            <div class="info-card">
                <div class="card-header"><i class="fas fa-user-check"></i> Active Users</div>
                <div class="card-body">
                    <?php
                    $activeUsers = 782;
                    echo $activeUsers;
                    ?>
                </div>
                <div class="card-footer">View Details <i class="fas fa-arrow-right"></i></div>
            </div>
            <div class="info-card">
                <div class="card-header"><i class="fas fa-envelope"></i> Email Unverified Users</div>
                <div class="card-body">
                    <?php
                    $emailUnverifiedUsers = 0;
                    echo $emailUnverifiedUsers;
                    ?>
                </div>
                <div class="card-footer">View Details <i class="fas fa-arrow-right"></i></div>
            </div>
            <div class="info-card">
                <div class="card-header"><i class="fas fa-mobile-alt"></i> Mobile Unverified Users</div>
                <div class="card-body">
                    <?php
                    $mobileUnverifiedUsers = 0;
                    echo $mobileUnverifiedUsers;
                    ?>
                </div>
                <div class="card-footer">View Details <i class="fas fa-arrow-right"></i></div>
            </div>
            <div class="info-card">
                <div class="card-header"><i class="fas fa-users"></i> Total Surveyors</div>
                <div class="card-body">
                    <?php
                    $totalSurveyors = 673;
                    echo $totalSurveyors;
                    ?>
                </div>
                <div class="card-footer">View Details <i class="fas fa-arrow-right"></i></div>
            </div>
            <div class="info-card">
                <div class="card-header"><i class="fas fa-user-check"></i> Active Surveyors</div>
                <div class="card-body">
                    <?php
                    $activeSurveyors = 673;
                    echo $activeSurveyors;
                    ?>
                </div>
                <div class="card-footer">View Details <i class="fas fa-arrow-right"></i></div>
            </div>
            <div class="info-card">
                <div class="card-header"><i class="fas fa-envelope"></i> Email Unverified Surveyors</div>
                <div class="card-body">
                    <?php
                    $emailUnverifiedSurveyors = 0;
                    echo $emailUnverifiedSurveyors;
                    ?>
                </div>
                <div class="card-footer">View Details <i class="fas fa-arrow-right"></i></div>
            </div>
            <div class="info-card">
                <div class="card-header"><i class="fas fa-mobile-alt"></i> Mobile Unverified Surveyors</div>
                <div class="card-body">
                    <?php
                    $mobileUnverifiedSurveyors = 0;
                    echo $mobileUnverifiedSurveyors;
                    ?>
                </div>
                <div class="card-footer">View Details <i class="fas fa-arrow-right"></i></div>
            </div>
        </div>

        <div class="deposits-withdrawals">
            <div class="deposits-withdrawals-card">
                <div class="dw-header">Surveyor Deposits</div>
                <div class="dw-item">
                    <div class="dw-label">Total Deposited</div>
                    <div class="dw-value">
                        <?php
                        $totalDeposited = "$19,748.00 USD";
                        echo $totalDeposited;
                        ?>
                    </div>
                </div>
                <div class="dw-item">
                    <div class="dw-label">Pending Deposits</div>
                    <div class="dw-value">
                         <?php
                         $pendingDeposits = 16;
                         echo $pendingDeposits;
                        ?>
                    </div>
                </div>
                <div class="dw-item">
                    <div class="dw-label">Rejected Deposits</div>
                    <div class="dw-value">
                        <?php
                        $rejectedDeposits = 0;
                        echo $rejectedDeposits;
                        ?>
                    </div>
                </div>
                <div class="dw-item">
                    <div class="dw-label">Deposit Progress</div>
                    <div class="dw-value">
                        <?php
                        $depositProgress = "70%";
                        echo $depositProgress;
                        ?>
                    </div>
                </div>
                <div class="dw-progress">
                    <div class="dw-progress-bar"></div>
                </div>
            </div>
            <div class="deposits-withdrawals-card">
                <div class="dw-header">Surveyor Withdrawals</div>
                <div class="dw-item">
                    <div class="dw-label">Total Withdrawals</div>
                    <div class="dw-value">
                        <?php
                         $totalWithdrawals = "$15,480.00 USD";
                         echo $totalWithdrawals;
                        ?>
                    </div>
                </div>
                <div class="dw-item">
                    <div class="dw-label">Pending Withdrawals</div>
                    <div class="dw-value">
                        <?php
                        $pendingWithdrawals = 23;
                        echo $pendingWithdrawals;
                        ?>
                    </div>
                </div>
                <div class="dw-item">
                    <div class="dw-label">Rejected Withdrawals</div>
                    <div class="dw-value">
                        <?php
                        $rejectedWithdrawals = 0;
                        echo $rejectedWithdrawals;
                        ?>
                    </div>
                </div>
                 <div class="dw-item">
                    <div class="dw-label">Withdrawals Progress</div>
                    <div class="dw-value">
                        <?php
                        $withdrawalsProgress = "30%";
                        echo $withdrawalsProgress;
                        ?>
                    </div>
                </div>
                <div class="dw-progress">
                    <div class="dw-progress-bar" style="width: 30%; background-color: #f56565;"></div>
                </div>
            </div>
        </div>

        <div class="survey-status">
            <div class="report-header">Survey Status</div>
            <div class="survey-status-grid">
                <div class="survey-status-item">
                    <div class="value">
                        <?php
                        $totalSurveys = 34;
                        echo $totalSurveys;
                        ?>
                    </div>
                    <div class="label">Total Surveys</div>
                </div>
                <div class="survey-status-item">
                    <div class="value">
                        <?php
                        $publishedSurveys = 28;
                        echo $publishedSurveys;
                        ?>
                    </div>
                    <div class="label">Published Surveys</div>
                </div>
                <div class="survey-status-item">
                    <div class="value">
                        <?php
                        $pendingSurveys = 6;
                        echo $pendingSurveys;
                         ?>
                    </div>
                    <div class="label">Pending Surveys</div>
                </div>
                <div class="survey-status-item">
                    <div class="value">
                        <?php
                        $draftSurveys = 0;
                        echo $draftSurveys;
                        ?>
                    </div>
                    <div class="label">Draft Surveys</div>
                </div>
                <div class="survey-status-item">
                    <div class="value">
                        <?php
                        $rejectedSurveys = 0;
                        echo $rejectedSurveys;
                        ?>
                    </div>
                    <div class="label">Rejected Surveys</div>
                </div>
                <div class="survey-status-item">
                    <a href="#">View All Surveys</a>
                </div>
            </div>
        </div>

        <div class="report-section">
            <div class="report-header">Latest Survey Reports</div>
            <div>
                <?php
                echo "[Table or Chart Placeholder]";
                ?>
            </div>
        </div>
    </div>
    <script>
        // You can add JavaScript here for interactive features,
        // such as toggling submenus in the sidebar, or updating
        // data in the dashboard cards.

        const sidebarMenu = document.querySelector('.sidebar-menu');
        if (sidebarMenu) {
            sidebarMenu.addEventListener('click', (event) => {
                const target = event.target.closest('li');
                if (target) {
                    // Remove active class from other items
                    const activeItem = sidebarMenu.querySelector('.active');
                    if (activeItem) {
                        activeItem.classList.remove('active');
                    }
                    // Add active class to the clicked item
                    target.classList.add('active');
                    // If you have submenus, you would toggle them here
                    // e.g., target.classList.toggle('open');
                }
            });
        }
    </script>
</body>
</html>
<?php
//  categories.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - SurvLab</title>
    <style>
        /* Basic Reset and Font */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: sans-serif;
            background-color: #f4f6f8;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background-color: #242933;
            color: #b8c2cc;
            width: 250px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar-logo {
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            color: #b8c2cc;
            padding: 10px 15px;
            text-decoration: none;
            margin-bottom: 5px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar-menu li a:hover {
            background-color: #373e4a;
            color: #fff;
        }
        .sidebar-menu li a i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        .sidebar-menu li.active a {
            background-color: #4a5568;
            color: #fff;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background-color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .header-search {
            display: flex;
            align-items: center;
            background-color: #f0f2f5;
            }