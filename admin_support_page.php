<?php
// **IMPORTANT:  session_start() MUST be the very first thing in your PHP file, before any HTML or other output!**
session_start();

$admin_name = "Admin"; // Default admin name
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets - SurvLab</title>
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

        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            margin-bottom: 20px;
            overflow-x: auto;
        }
        .filter-tabs a {
            padding: 10px 15px;
            text-decoration: none;
            color: #718096;
            border-bottom: 2px solid transparent;
            margin-right: 15px;
            white-space: nowrap;
            transition: color 0.3s ease, border-bottom-color 0.3s ease;
        }
        .filter-tabs a:hover {
            color: #2d3748;
            border-bottom-color: #4299e1;
        }
        .filter-tabs a.active {
            color: #2d3748;
            border-bottom-color: #4299e1;
            font-weight: bold;
        }

        /* Tickets Table */
        .tickets-table {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        .tickets-table table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .tickets-table thead th {
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
            color: #4a5568;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .tickets-table tbody td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #edf2f7;
            color: #2d3748;
        }
        .tickets-table tbody tr:hover {
            background-color: #f7fafc;
        }
        .tickets-table .action-buttons button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
            background-color: #4299e1;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .tickets-table .action-buttons button:hover {
            background-color: #3182ce;
        }
        .tickets-table .priority {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            text-align: center;
        }
        .tickets-table .priority.high {
            background-color: #f56565;
            color: #fff;
        }
        .tickets-table .priority.medium {
            background-color: #f6e05e;
            color: #1a202c;
        }
        .tickets-table .priority.low {
            background-color: #48bb78;
            color: #fff;
        }
        .tickets-table .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            text-align: center;
        }
        .tickets-table .status.open {
            background-color: #4299e1;
            color: #fff;
        }
        .tickets-table .status.answered {
            background-color: #48bb78;
            color: #fff;
        }
        .tickets-table .status.closed {
            background-color: #718096;
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
            .filter-tabs {
                overflow-x: auto;
            }
            .tickets-table {
                overflow-x: auto;
            }
            .tickets-table table {
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
            <li class="active"><a href="#"><i class="fas fa-ticket-alt"></i> Support Ticket</a></li>
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
            <h1>Support Tickets</h1>
        </div>

        <div class="filter-tabs">
            <a href="#" class="active">All Tickets</a>
            <a href="#">Pending Ticket</a>
            <a href="#">Answered Ticket</a>
            <a href="#">Closed Ticket</a>
        </div>

        <div class="tickets-table">
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Submitted By</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Last Reply</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>[Ticket #67305616] Aut explicabo Dolor</td>
                        <td>Mechelle Bowers</td>
                        <td><span class="status open">Open</span></td>
                        <td><span class="priority high">High</span></td>
                        <td>1 day ago</td>
                        <td class="action-buttons">
                            <button>Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>[Ticket #341987] Impedit accusamus</td>
                        <td>Nelson Juma</td>
                        <td><span class="status answered">Answered</span></td>
                        <td><span class="priority medium">Medium</span></td>
                        <td>1 day ago</td>
                        <td class="action-buttons">
                            <button>Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>[Ticket #300766] Perferendis Nam</td>
                        <td>yamada tarou</td>
                        <td><span class="status answered">Answered</span></td>
                        <td><span class="priority low">Low</span></td>
                        <td>1 day ago</td>
                        <td class="action-buttons">
                            <button>Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>[Ticket #633087] Fugit magni eveniet</td>
                        <td>Yssouf Kangah</td>
                        <td><span class="status open">Open</span></td>
                        <td><span class="priority high">High</span></td>
                        <td>5 days ago</td>
                        <td class="action-buttons">
                            <button>Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>[Ticket #927509] Id amet eveniet</td>
                        <td>Alo Kia</td>
                        <td><span class="status answered">Answered</span></td>
                        <td><span class="priority low">Low</span></td>
                        <td>6 days ago</td>
                        <td class="action-buttons">
                            <button>Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>[Ticket #375379] Sed dignissimos</td>
                        <td>Waqar Ahmad</td>
                        <td><span class="status answered">Answered</span></td>
                        <td><span class="priority low">Low</span></td>
                        <td>6 days ago</td>
                        <td class="action-buttons">
                            <button>Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>[Ticket #584869] Quidem perspiciatis</td>
                        <td>Alo Kia</td>
                        <td><span class="status open">Open</span></td>
                        <td><span class="priority medium">Medium</span></td>
                        <td>2 weeks ago</td>
                        <td class="action-buttons">
                            <button>Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>[Ticket #644260] Incidunt dicta ut</td>
                        <td>Kaiser 01673710800</td>
                        <td><span class="status answered">Answered</span></td>
                        <td><span class="priority medium">Medium</span></td>
                        <td>2 weeks ago</td>
                        <td class="action-buttons">
                            <button>Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>[Ticket #17608] Voluptas error officiis</td>
                        <td>-</td>
                        <td><span class="status open">Open</span></td>
                        <td><span class="priority medium">Medium</span></td>
                        <td>2 weeks ago</td>
                        <td class="action-buttons">
                            <button>Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>[Ticket #300817] Obcaecati aliquid</td>
                        <td>-</td>
                        <td><span class="status closed">Closed</span></td>
                        <td><span class="priority medium">Medium</span></td>
                        <td>2 weeks ago</td>
                        <td class="action-buttons">
                            <button>Details</button>
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
