<?php
session_start();
include '../includes/db.php';

$user_id = $_SESSION['user_id'] ?? 1;
$menu_item_id = $_POST['menu_item_id'];
$quantity = $_POST['quantity'];

// First check if the item already exists in the cart
$check = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND menu_item_id = ?");
$check->bind_param("ii", $user_id, $menu_item_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Item exists, update quantity
    $existing = $result->fetch_assoc();
    $new_quantity = $existing['quantity'] + $quantity;

    $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND menu_item_id = ?");
    $update->bind_param("iii", $new_quantity, $user_id, $menu_item_id);
    $update->execute();
} else {
    // Item doesn't exist, insert new row
    $insert = $conn->prepare("INSERT INTO cart (user_id, menu_item_id, quantity) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $user_id, $menu_item_id, $quantity);
    $insert->execute();
}

header("Location: ../index.php?added=true");
exit;

