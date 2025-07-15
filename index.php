<?php
session_start();
include 'includes/db.php';

// Redirect if not logged in or not a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get top 3 best sellers first
$top3Query = $conn->query("SELECT id FROM menu_items ORDER BY sales_count DESC LIMIT 3");
$top3Ids = [];
while ($topItem = $top3Query->fetch_assoc()) {
    $top3Ids[] = $topItem['id'];
}

// Fetch all menu items
$sql = "SELECT * FROM menu_items ORDER BY sales_count DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f7f7f7;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header a {
            margin-left: 10px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 8px 12px;
            border-radius: 5px;
        }
        .header a.logout {
            background-color: crimson;
        }
        .card {
            display: inline-block;
            width: 240px;
            margin: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            background-color: #fff;
            vertical-align: top;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }
        .card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .card h3 {
            font-size: 18px;
            margin: 5px 0;
        }
        .card p {
            font-size: 14px;
            margin: 5px 0;
            color: #555;
        }
        .price {
            font-weight: bold;
            margin-bottom: 8px;
        }
        .best-seller {
            color: green;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .out-of-stock {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }
        form {
            margin-top: 10px;
        }
        input[type="number"] {
            width: 60px;
            padding: 4px;
            margin-right: 5px;
        }
        button {
            padding: 5px 10px;
            background-color: #28a745;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        button:disabled {
            background-color: gray;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>🍽 Welcome to the Restaurant</h2>
    <div>
        <a href="cart.php">🛒 View Cart</a>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>
</div>

<hr>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <?php if (!empty($row['image']) && file_exists($row['image'])): ?>
                <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
            <?php else: ?>
                <img src="placeholder.jpg" alt="No Image">
            <?php endif; ?>

            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p><?= htmlspecialchars($row['description']) ?></p>
            <p class="price">₱<?= number_format($row['price'], 2) ?></p>

            <?php if (in_array($row['id'], $top3Ids)): ?>
                <div class="best-seller">🔥 Best Seller</div>
            <?php endif; ?>

            <?php if ($row['stock'] > 0): ?>
                <form method="POST" action="actions/add_to_cart.php">
                    <input type="hidden" name="menu_item_id" value="<?= $row['id'] ?>">
                    <label>Qty:</label>
                    <input type="number" name="quantity" value="1" min="1" max="<?= $row['stock'] ?>" required>
                    <button type="submit">Add to Cart</button>
                </form>
            <?php else: ?>
                <div class="out-of-stock">Out of Stock</div>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No menu items available.</p>
<?php endif; ?>

</body>
</html>
