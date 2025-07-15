<?php
session_start();
include '../includes/db.php';

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $desc        = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $stock       = intval($_POST['stock']);
    $image_path  = null;

    // Handle image upload if provided
    if (!empty($_FILES['image']['name'])) {
        $target_dir  = "../uploads/";
        $filename    = uniqid() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $filename;
        $image_type  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate image type
        if (in_array($image_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = "uploads/" . $filename;
            } else {
                $msg = "❌ Failed to upload image.";
            }
        } else {
            $msg = "❌ Invalid image file type.";
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO menu_items (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $name, $desc, $price, $stock, $image_path);

    if ($stmt->execute()) {
        $msg = "✅ Item added successfully!";
    } else {
        $msg = "❌ Error adding item.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Menu Item</title>
</head>
<body>
    <h2>➕ Add New Menu Item</h2>
    <a href="dashboard.php">← Back to Dashboard</a>
    <br><br>

    <?php if (!empty($msg)): ?>
        <p><?= $msg ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Item Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Price (₱):</label><br>
        <input type="number" name="price" step="0.01" required><br><br>

        <label>Stock:</label><br>
        <input type="number" name="stock" required><br><br>

        <label>Image (JPG, PNG, GIF):</label><br>
        <input type="file" name="image" accept="image/*"><br><br>

        <button type="submit">Add Item</button>
    </form>
</body>
</html>
