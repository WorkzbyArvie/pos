<?php
session_start();
include '../includes/db.php';

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-color: #f9f9f9;
        }
        h2 {
            margin-bottom: 20px;
        }
        .nav {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }
        .nav a {
            display: inline-block;
            padding: 12px 18px;
            background-color: #2c3e50;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s ease;
        }
        .nav a:hover {
            background-color: #34495e;
        }
        .nav a.logout {
            background-color: crimson;
        }
        .nav a.logout:hover {
            background-color: darkred;
        }
        .card {
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            max-width: 600px;
        }
    </style>
</head>
<body>

<h2>👨‍🍳 Welcome, Admin!</h2>
<?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
    <p style="color: green;">✅ All best sellers have been reset.</p>
<?php endif; ?>

<div class="nav">
    <a href="inventory.php">📦 Inventory</a>
    <a href="add_item.php">➕ Add Menu Item</a>
    <a href="sales_report.php">📊 Sales Report</a>
    <a href="../logout.php" class="logout">🚪 Logout</a>
    <a href="reset_best_seller.php" onclick="return confirm('Are you sure you want to reset all best sellers?');" style="background-color: orange;">♻️ Reset Best Seller</a>

</div>

<div class="card">
    <p>Select an option above to manage the restaurant system.</p>
    <ul>
        <li>📦 View and manage current stock</li>
        <li>➕ Add new menu items with photo and details</li>
        <li>📊 View sales transactions and totals</li>
        <li>🚪 Logout securely</li>
    </ul>
</div>

</body>
</html>
