<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = $_POST['id'] ?? null;
$stock = $_POST['stock'] ?? null;

if ($id && $stock !== null) {
    $stmt = $conn->prepare("UPDATE menu_items SET stock = ? WHERE id = ?");
    $stmt->bind_param("ii", $stock, $id);
    $stmt->execute();
}

header("Location: inventory.php");
exit;
