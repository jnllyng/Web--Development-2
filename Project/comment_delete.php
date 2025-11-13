<?php
session_start();
require('includes/db_connect.php');

$is_admin = isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';

$comment_id = $_GET['id'] ?? null;
$redirect = $_GET['redirect'] ?? 'species_details.php';

if (!$comment_id) {
    header("Location: $redirect");
    exit;
}

if ($is_admin) {
    $stmt = $db->prepare("DELETE FROM comments WHERE comment_id = ?");
    $stmt->execute([$comment_id]);
} else {
    $user_id = $_SESSION['user_id'] ?? 0;
    $stmt = $db->prepare("DELETE FROM comments WHERE comment_id = ? AND user_id = ?");
    $stmt->execute([$comment_id, $user_id]);
}
header("Location: $redirect");
exit;
?>
