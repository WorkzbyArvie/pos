<?php
session_start();
include '../includes/db.php';

$user_id = $_SESSION['user_id'] ?? 1;

$sql = "SELECT * FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();

while ($item = $cart_items->fetch_assoc()) {
    $menu_item_id = $item['menu_item_id'];
    $quantity = $item['quantity'];

    // Get price
    $price_stmt = $conn->prepare("SELECT price FROM menu_items WHERE id = ?");
    $price_stmt->bind_param("i", $menu_item_id);
    $price_stmt->execute();
    $price_result = $price_stmt->get_result();
    $price = $price_result->fetch_assoc()['price'];

    $total = $price * $quantity;

    // Insert into sales
    $insert = $conn->prepare("INSERT INTO sales (user_id, menu_item_id, quantity, total_price) VALUES (?, ?, ?, ?)");
    $insert->bind_param("iiid", $user_id, $menu_item_id, $quantity, $total);
    $insert->execute();

    // Update stock
    $update_stock = $conn->prepare("UPDATE menu_items SET stock = stock - ?, sales_count = sales_count + ? WHERE id = ?");
    $update_stock->bind_param("iii", $quantity, $quantity, $menu_item_id);
    $update_stock->execute();
}

// Clear cart
$clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$clear->bind_param("i", $user_id);
$clear->execute();

header("Location: ../index.php?checkout=success");
exit;

