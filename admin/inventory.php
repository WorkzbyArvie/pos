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
    <title>Inventory</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        img { width: 100px; height: auto; }
        a.btn {
            padding: 5px 10px;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin: 0 5px;
        }
        .edit-btn { background-color: #007bff; }
        .delete-btn { background-color: crimson; }
    </style>
</head>
<body>

<h2>📦 Inventory</h2>
<a href="dashboard.php">← Back to Dashboard</a><br><br>

<table>
    <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price (₱)</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td>
            <?php if (!empty($row['image'])): ?>
                <img src="../<?= htmlspecialchars($row['image']) ?>" alt="Image">
            <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td>₱<?= number_format($row['price'], 2) ?></td>
        <td><?= max(0, (int)$row['stock']) ?></td> <!-- Fix: show 0 if stock is negative -->
        <td>
            <a class="btn edit-btn" href="edit_item.php?id=<?= $row['id'] ?>">Edit</a>
            <a class="btn delete-btn" href="delete_item.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
