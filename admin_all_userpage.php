
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
    <title>Manage Users - SurvLab</title>
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
        .page-title .filter-options {
            display: flex;
            gap: 10px;
        }
        .page-title .filter-options select {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            outline: none;
            font-size: 0.9rem;
        }
        .page-title .filter-options button {
            background-color: #4299e1;
            color: #fff;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .page-title .filter-options button:hover {
            background-color: #3182ce;
        }

        /* Users Table */
        .users-table {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        .users-table table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .users-table thead th {
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
            color: #4a5568;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .users-table tbody td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #edf2f7;
            color: #2d3748;
        }
        .users-table tbody tr:hover {
            background-color: #f7fafc;
        }
        .users-table .action-buttons {
            display: flex;
            gap: 10px;
        }
        .users-table .action-buttons button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
        }
        .users-table .action-buttons .details-button {
            background-color: #4299e1;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .users-table .action-buttons .details-button:hover {
            background-color: #3182ce;
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
            .page-title .filter-options {
                flex-direction: column;
                gap: 10px;
            }
            .users-table {
                overflow-x: auto;
            }
            .users-table table {
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
            <li class="active"><a href="#"><i class="fas fa-user-cog"></i> Manage Users</a></li>
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
            <h1>All Users</h1>
            <div class="filter-options">
                <select>
                    <option value="username">Username</option>
                    <option value="email">Email</option>
                </select>
                <select>
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="banned">Banned</option>
                    <option value="email_unverified">Email Unverified</option>
                    <option value="mobile_unverified">Mobile Unverified</option>
                    <option value="kyc_pending">KYC Pending</option>
                    <option value="with_balance">With Balance</option>
                    <option value="no_balance">No Balance</option>
                </select>
                <button>Filter</button>
            </div>
        </div>

        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email + Mobile</th>
                        <th>Country</th>
                        <th>Joined At</th>
                        <th>Balance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Tester 1234<br>@tester1234</td>
                        <td>tester1234@test.com<br>37321994</td>
                        <td>MD</td>
                        <td>2024-07-30 06:36 PM<br>1 day ago</td>
                        <td>$0.00 USD</td>
                        <td class="action-buttons">
                            <button class="details-button">Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Demo User<br>@forpush</td>
                        <td>admin@gcore.com<br>14035377</td>
                        <td>DQ</td>
                        <td>2024-07-22 09:53 AM<br>1 week ago</td>
                        <td>$0.00 USD</td>
                        <td class="action-buttons">
                            <button class="details-button">Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Adaniowo Matthew<br>@mjaymannie</td>
                        <td>mjaysnippet@gmail.com<br>4420185</td>
                        <td>JE</td>
                        <td>2024-07-18 11:16 AM<br>1 week ago</td>
                        <td>$333,333.00 USD</td>
                        <td class="action-buttons">
                            <button class="details-button">Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>De Arf<br>@jokojoko</td>
                        <td>masjokosokigmail.com<br>5648078</td>
                        <td>BR</td>
                        <td>2024-07-18 09:00 AM<br>1 week ago</td>
                        <td>$333,333.00 USD</td>
                        <td class="action-buttons">
                            <button class="details-button">Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>ARREY NTOH AYUK NDIP<br>@tyrexcom</td>
                        <td>ayukndiparreyntohj@gmail.com<br>38157488</td>
                        <td>RS</td>
                        <td>2024-07-15 11:15 AM<br>2 weeks ago</td>
                        <td>$555,555.00 USD</td>
                        <td class="action-buttons">
                            <button class="details-button">Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>MR MBR<br>@clwet84767</td>
                        <td>clwet84767@gcartep.com<br>58023845</td>
                        <td>GP</td>
                        <td>2024-07-12 07:05 PM<br>2 weeks ago</td>
                        <td>$1,777,676.00 USD</td>
                        <td class="action-buttons">
                            <button class="details-button">Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>MW Merchants<br>@mwmerchants</td>
                        <td>mwmerchants@gmail.net.pl<br>5015740</td>
                        <td>BZ</td>
                        <td>2024-07-10 04:13 PM<br>3 weeks ago</td>
                        <td>$0.00 USD</td>
                        <td class="action-buttons">
                            <button class="details-button">Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>username 555<br>@usename555</td>
                        <td>usename555@gmail.com<br>42143006</td>
                        <td>SK</td>
                        <td>2024-07-10 11:43 AM<br>2 weeks ago</td>
                        <td>$888,888.00 USD</td>
                        <td class="action-buttons">
                            <button class="details-button">Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>imran khan<br>@macanuel</td>
                        <td>techonetw@gmail.com<br>67344253</td>
                        <td>BW</td>
                        <td>2024-07-06 07:18 PM<br>2 weeks ago</td>
                        <td>$333,333.00 USD</td>
                        <td class="action-buttons">
                            <button class="details-button">Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>ALI ÇETİN<br>@alcat</td>
                        <td>alicaocetin@gmail.com<br>4416588</td>
                        <td>OG</td>
                        <td>2024-07-05 05:47 PM<br>2 weeks ago</td>
                        <td>$0.00 USD</td>
                        <td class="action-buttons">
                            <button class="details-button">Details</button>
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
