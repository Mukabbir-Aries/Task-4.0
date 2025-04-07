<?php
// **IMPORTANT:  session_start() MUST be the very first thing in your PHP file, before any HTML or other output!**
session_start();

$admin_name = "Admin"; // Default admin name,
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Surveys - SurvLab</title>
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

        /* Page Title */
        .page-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .page-title h1 {
            font-size: 1.8rem;
            color: #2d3748;
            font-weight: bold;
        }
        .page-title .filter-button button {
            background-color: #4299e1;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .page-title .filter-button button:hover {
            background-color: #3182ce;
        }

        /* Surveys Table */
        .surveys-table {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        .surveys-table table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .surveys-table thead th {
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
            color: #4a5568;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .surveys-table tbody td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #edf2f7;
            color: #2d3748;
        }
        .surveys-table tbody tr:hover {
            background-color: #f7fafc;
        }
        .surveys-table .action-buttons {
            display: flex;
            gap: 10px;
        }
        .surveys-table .action-buttons button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
        }
        .surveys-table .action-buttons .questions-button {
            background-color: #4299e1;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .surveys-table .action-buttons .questions-button:hover {
            background-color: #3182ce;
        }
        .surveys-table .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            text-align: center;
        }
        .surveys-table .status.approved {
            background-color: #48bb78;
            color: #fff;
        }
        .surveys-table .status.pending {
            background-color: #f6e05e;
            color: #1a202c;
        }
        .surveys-table .status.rejected {
            background-color: #f56565;
            color: #fff;
        }

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
            .page-title {
                flex-direction: column;
                align-items: flex-start;
            }
            .page-title .filter-button {
                margin-top: 10px;
            }
            .surveys-table {
                overflow-x: auto;
            }
            .surveys-table table {
                width: auto;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">SurvLab</div>
        <ul class="sidebar-menu">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#"><i class="fas fa-folder"></i> Manage Categories</a></li>
            <li class="active"><a href="#"><i class="fas fa-list-alt"></i> Manage Surveys</a></li>
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

        <div class="page-title">
            <h1>All Surveys</h1>
            <div class="filter-button">
                <button>+ Filter</button>
            </div>
        </div>

        <div class="surveys-table">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Surveyor</th>
                        <th>Total Questions</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Test1234</td>
                        <td>Test</td>
                        <td>Tester1</td>
                        <td>4</td>
                        <td><span class="status approved">Approved</span></td>
                        <td>2024-07-28 03:25 PM</td>
                        <td class="action-buttons">
                            <button class="questions-button">Questions</button>
                        </td>
                    </tr>
                    <tr>
                        <td>dorje5654</td>
                        <td>Software</td>
                        <td>dorje5654</td>
                        <td>3</td>
                        <td><span class="status approved">Approved</span></td>
                        <td>2024-07-26 10:31 PM</td>
                        <td class="action-buttons">
                            <button class="questions-button">Questions</button>
                        </td>
                    </tr>
                    <tr>
                        <td>inde</td>
                        <td>Software</td>
                        <td>tyrexcvbh</td>
                        <td>1</td>
                        <td><span class="status approved">Approved</span></td>
                        <td>2024-07-15 06:22 AM</td>
                        <td class="action-buttons">
                            <button class="questions-button">Questions</button>
                        </td>
                    </tr>
                    <tr>
                        <td>File Delete Cron</td>
                        <td>Test</td>
                        <td>MMMBR125</td>
                        <td>2</td>
                        <td><span class="status approved">Approved</span></td>
                        <td>2024-07-14 08:10 PM</td>
                        <td class="action-buttons">
                            <button class="questions-button">Questions</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Politico</td>
                        <td>Education</td>
                        <td>olhaotim</td>
                        <td>5</td>
                        <td><span class="status approved">Approved</span></td>
                        <td>2024-07-12 07:43 PM</td>
                        <td class="action-buttons">
                            <button class="questions-button">Questions</button>
                        </td>
                    </tr>
                    <tr>
                        <td>4528</td>
                        <td>Software</td>
                        <td>mmbrirweb</td>
                        <td>2</td>
                        <td><span class="status approved">Approved</span></td>
                        <td>2024-07-12 02:12 PM</td>
                        <td class="action-buttons">
                            <button class="questions-button">Questions</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Aashish Chaturvedi</td>
                        <td>Education</td>
                        <td>testuser1000</td>
                        <td>1</td>
                        <td><span class="status approved">Approved</span></td>
                        <td>2024-07-10 11:38 AM</td>
                        <td class="action-buttons">
                            <button class="questions-button">Questions</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Bangla</td>
                        <td>Satisfaction</td>
                        <td>surveyor555</td>
                        <td>2</td>
                        <td><span class="status approved">Approved</span></td>
                        <td>2024-07-10 08:03 AM</td>
                        <td class="action-buttons">
                            <button class="questions-button">Questions</button>
                        </td>
                    </tr>
                    <tr>
                        <td>test06</td>
                        <td>Test</td>
                        <td>dishugrewal</td>
                        <td>2</td>
                        <td><span class="status approved">Approved</span></td>
                        <td>2024-07-08 10:48 PM</td>
                        <td class="action-buttons">
                            <button class="questions-button">Questions</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Meer Alamin</td>
                        <td>Software</td>
                        <td>meeralamin</td>
                        <td>2</td>
                        <td><span class="status approved">Approved</span></td>
                        <td>2024-07-07 01:18 AM</td>
                        <td class="action-buttons">
                            <button class="questions-button">Questions</button>
                        </td>
                    </tr>
                    <tr>
                        <td>lala</td>
                        <td>Fitness</td>
                        <td>Lavadalavda</td>
                        <td>2</td>
                        <td><span class="status approved">Approved</span></td>
                        <td>2024-07-03 02:44 PM</td>
                        <td class="action-buttons">
                            <button class="questions-button">Questions</button>
                        </td>
                    </tr>
                </tbody>
            </table>
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
