<?php
session_start();
include '../includes/db.php';

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get filter inputs
$start_date = $_GET['start_date'] ?? '';
$end_date   = $_GET['end_date'] ?? '';
$product_id = $_GET['product_id'] ?? '';

// Build base query
$sql = "SELECT s.*, m.name AS item_name, u.username
        FROM sales s
        JOIN menu_items m ON s.menu_item_id = m.id
        JOIN users u ON s.user_id = u.id
        WHERE 1";

if (!empty($start_date)) {
    $sql .= " AND s.sale_date >= '$start_date 00:00:00'";
}
if (!empty($end_date)) {
    $sql .= " AND s.sale_date <= '$end_date 23:59:59'";
}
if (!empty($product_id)) {
    $sql .= " AND s.menu_item_id = " . intval($product_id);
}

$sql .= " ORDER BY s.sale_date DESC";

$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Get product list for dropdown
$products = $conn->query("SELECT id, name FROM menu_items");

$total_sales = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; background: #f9f9f9; }
        h2 { margin-bottom: 15px; }
        a.back { text-decoration: none; background: #007bff; color: white; padding: 8px 12px; border-radius: 5px; }
        form { margin-top: 20px; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.05); display: flex; gap: 15px; flex-wrap: wrap; align-items: center; }
        input, select { padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 8px 15px; background: #28a745; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
        th { background-color: #e9ecef; }
        .total-row { font-weight: bold; background-color: #f1f1f1; }
    </style>
</head>
<body>

<h2>📊 Sales Report</h2>
<a href="dashboard.php" class="back">← Back to Dashboard</a>

<form method="GET">
    <label>Start Date: <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>"></label>
    <label>End Date: <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>"></label>
    <label>Product:
        <select name="product_id">
            <option value="">-- All --</option>
            <?php while ($p = $products->fetch_assoc()): ?>
                <option value="<?= $p['id'] ?>" <?= ($product_id == $p['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </label>
    <button type="submit">🔍 Filter</button>
</form>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Date & Time</th>
            <th>Customer</th>
            <th>Item</th>
            <th>Quantity</th>
            <th>Total Price (₱)</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()):
            $total_sales += $row['total_price'];
        ?>
        <tr>
            <td><?= date("Y-m-d H:i:s", strtotime($row['sale_date'])) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['item_name']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>₱<?= number_format($row['total_price'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
        <tr class="total-row">
            <td colspan="4">Total Sales</td>
            <td>₱<?= number_format($total_sales, 2) ?></td>
        </tr>
    </table>
<?php else: ?>
    <p>No sales data found for the selected filters.</p>
<?php endif; ?>

</body>
</html>
