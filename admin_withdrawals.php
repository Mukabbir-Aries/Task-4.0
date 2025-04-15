<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Database connection using PDO
$servername = "localhost"; // Change if necessary
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$dbname = "task_buddy_db"; // Updated database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch admin name from database using session admin_id
    if (isset($_SESSION['admin_id'])) {
        $stmtAdmin = $conn->prepare("SELECT admin_name FROM admins WHERE id = :id");
        $stmtAdmin->execute(['id' => $_SESSION['admin_id']]);
        $adminData = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
        if ($adminData && !empty($adminData['admin_name'])) {
            $admin_name = $adminData['admin_name'];
        } else {
            $admin_name = "Admin";
        }
    } else {
        $admin_name = "Admin";
    }
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'], $_POST['transaction_id'])) {
        $action = $_POST['action'];
        $transactionId = (int)$_POST['transaction_id'];

        // Fetch withdrawal amount and user_id for the transaction
        $stmt = $conn->prepare("SELECT amount, user_id FROM transactions WHERE id = :id");
        $stmt->execute(['id' => $transactionId]);
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$transaction) {
            // Transaction not found, redirect back
            header("Location: admin_withdrawals.php");
            exit;
        }

        if ($action === 'approve') {
            // Begin transaction
            $conn->beginTransaction();
            try {
                // Update transaction status to approved
                $updateStmt = $conn->prepare("UPDATE transactions SET status = 'approved' WHERE id = :id");
                $updateStmt->execute(['id' => $transactionId]);

                // Deduct amount from user's balance
                $deductStmt = $conn->prepare("UPDATE users SET balance = balance - :amount WHERE id = :user_id");
                $deductStmt->execute(['amount' => $transaction['amount'], 'user_id' => $transaction['user_id']]);

                $conn->commit();
            } catch (Exception $e) {
                $conn->rollBack();
                error_log("Error approving withdrawal: " . $e->getMessage());
            }
        } elseif ($action === 'reject') {
            // Update transaction status to rejected
            $updateStmt = $conn->prepare("UPDATE transactions SET status = 'rejected' WHERE id = :id");
            $updateStmt->execute(['id' => $transactionId]);
        }
        // Redirect to avoid form resubmission
        header("Location: admin_withdrawals.php");
        exit;
    }
}

// Fetch pending withdrawal requests
$pendingWithdrawals = [];
try {
    $stmt = $conn->prepare("SELECT t.id, t.user_id, t.amount, t.payment_method, t.bank_name, t.account_number, t.bkash_number, t.status, u.username FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.type = 'withdrawal' AND t.status = 'pending' ORDER BY t.id DESC");
    $stmt->execute();
    $pendingWithdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching pending withdrawals: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Withdrawals - Task Buddy</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 960px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h1 {
            margin-bottom: 20px;
            color: #2d3748;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            color: #2d3748;
        }
        th {
            background-color: #f7fafc;
            font-weight: 600;
        }
        button {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-right: 8px;
        }
        .approve-btn {
            background-color: #48bb78;
            color: white;
        }
        .approve-btn:hover {
            background-color: #38a169;
        }
        .reject-btn {
            background-color: #f56565;
            color: white;
        }
        .reject-btn:hover {
            background-color: #c53030;
        }
        .details {
            font-size: 0.9rem;
            color: #4a5568;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header .admin-name {
            font-weight: bold;
            color: #2d3748;
        }
        .sidebar {
            width: 200px;
            background-color: #242933;
            color: #b8c2cc;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .sidebar a {
            color: #b8c2cc;
            text-decoration: none;
            display: block;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        .sidebar a:hover {
            color: white;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admindashboard.php">Dashboard</a>
        <a href="admin_withdrawals.php" style="font-weight: bold; color: white;">Withdrawals</a>
        <a href="manage_surveys.php">Manage Surveys</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="support_ticket.php">Support Ticket</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <div class="header">
            <div class="admin-name">Welcome, <?php echo htmlspecialchars($admin_name); ?></div>
        </div>
        <h1>Pending Withdrawal Requests</h1>
        <?php if (count($pendingWithdrawals) === 0): ?>
            <p>No pending withdrawal requests.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingWithdrawals as $withdrawal): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($withdrawal['id']); ?></td>
                            <td><?php echo htmlspecialchars($withdrawal['username']); ?></td>
                            <td>$<?php echo number_format($withdrawal['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($withdrawal['payment_method']); ?></td>
                            <td class="details">
                                <?php
                                if ($withdrawal['payment_method'] === 'Bank Transfer') {
                                    echo "Bank: " . htmlspecialchars($withdrawal['bank_name']) . "<br>";
                                    echo "Account: " . htmlspecialchars($withdrawal['account_number']);
                                } elseif ($withdrawal['payment_method'] === 'Bkash') {
                                    echo "Bkash Number: " . htmlspecialchars($withdrawal['bkash_number']);
                                }
                                ?>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($withdrawal['id']); ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="approve-btn">Approve</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($withdrawal['id']); ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="reject-btn">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
