<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $desc  = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $item['image'];

    // Update image if new one is uploaded
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/";
        $filename   = uniqid() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = "uploads/" . $filename;
        }
    }

    $stmt = $conn->prepare("UPDATE menu_items SET name=?, description=?, price=?, stock=?, image=? WHERE id=?");
    $stmt->bind_param("ssdisi", $name, $desc, $price, $stock, $image, $id);
    $stmt->execute();

    header("Location: inventory.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Menu Item</title>
</head>
<body>

<h2>✏️ Edit Menu Item</h2>
<a href="inventory.php">← Back to Inventory</a><br><br>

<form method="POST" enctype="multipart/form-data">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" required><?= htmlspecialchars($item['description']) ?></textarea><br><br>

    <label>Price (₱):</label><br>
    <input type="number" name="price" step="0.01" value="<?= $item['price'] ?>" required><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" value="<?= $item['stock'] ?>" required><br><br>

    <label>Replace Image:</label><br>
    <input type="file" name="image" accept="image/*"><br><br>

    <button type="submit">Update Item</button>
</form>

</body>
</html>
