<?php
session_start();
include 'includes/db.php';

// For now, mock user ID (until login system is complete)
$user_id = $_SESSION['user_id'] ?? 1;

// Fetch items in the user's cart
$sql = "SELECT c.id AS cart_id, m.name, m.price, c.quantity, (m.price * c.quantity) AS total
        FROM cart c
        JOIN menu_items m ON c.menu_item_id = m.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
</head>
<body>
    <h1>🛒 Your Cart</h1>
    <a href="index.php">← Back to Menu</a>
    <hr>

    <?php if ($result->num_rows > 0): ?>
        <form action="actions/checkout.php" method="POST">
            <table border="1" cellpadding="8">
                <tr>
                    <th>Item</th>
                    <th>Price (₱)</th>
                    <th>Qty</th>
                    <th>Total (₱)</th>
                    <th>Action</th>
                </tr>
                <?php
                $grand_total = 0;
                while ($row = $result->fetch_assoc()):
                    $grand_total += $row['total'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= number_format($row['total'], 2) ?></td>
                    <td><a href="actions/remove_from_cart.php?id=<?= $row['cart_id'] ?>">Remove</a></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="3" align="right"><strong>Grand Total:</strong></td>
                    <td colspan="2"><strong>₱<?= number_format($grand_total, 2) ?></strong></td>
                </tr>
            </table>
            <br>
            <button type="submit">✅ Checkout</button>
        </form>
    <?php else: ?>
        <p>Your cart is empty!</p>
    <?php endif; ?>
</body>
</html>
