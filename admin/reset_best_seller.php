<?php
session_start();
include '../includes/db.php';

// Allow only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Reset all sales_count to 0
$conn->query("UPDATE menu_items SET sales_count = 0");

header("Location: dashboard.php?reset=success");
exit;
?>
