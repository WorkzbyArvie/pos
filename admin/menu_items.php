<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$result = $conn->query("SELECT * FROM menu_items");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Menu Items</title>
</head>
<body>
    <h2>📋 Manage Menu Items</h2>
    <a href="dashboard.php">← Back to Dashboard</a>
    <br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Sales</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td>
                    <?php if ($row['image']): ?>
                        <img src="../<?= $row['image'] ?>" width="60">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td><?= $row['stock'] ?></td>
                <td><?= $row['sales_count'] ?></td>
                <td>
                    <a href="edit_item.php?id=<?= $row['id'] ?>">✏️ Edit</a> |
                    <a href="delete_item.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this item?')">🗑️ Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
