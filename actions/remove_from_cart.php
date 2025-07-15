<?php
include '../includes/db.php';

$cart_id = $_GET['id'] ?? null;

if ($cart_id) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->bind_param("i", $cart_id); // ✅ fixed here
    $stmt->execute();
}

header("Location: ../cart.php");
exit;
