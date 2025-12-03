<?php
session_start();
require('includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit((string)$id)) {
    header("Location: category_list_user.php");
    exit;
}

$stmt = $db->prepare("SELECT COUNT(*) FROM species WHERE category_id=?");
$stmt->execute([$id]);
$count = $stmt->fetchColumn();

if ($count > 0) {
    echo "Cannot delete: this category has species assigned.";
    exit;
}

$stmt = $db->prepare("DELETE FROM categories WHERE category_id=?");
$stmt->execute([$id]);

header("Location: category_list_user.php");
exit;
?>
