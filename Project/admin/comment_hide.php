<?php
session_start();
require('../includes/db_connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

$comment_id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$comment_id || !$action) {
    echo "Invalid request.";
    exit;
}

$stmt = $db->prepare("SELECT category_id FROM comments WHERE comment_id = ?");
$stmt->execute([$comment_id]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comment) {
    echo "Comment not found.";
    exit;
}

$category_id = $comment['category_id'];

if ($action === 'hide') {
    $stmt = $db->prepare("UPDATE comments SET visible = 0 WHERE comment_id = ?");
} elseif ($action === 'unhide') {
    $stmt = $db->prepare("UPDATE comments SET visible = 1 WHERE comment_id = ?");
} else {
    echo "Invalid action.";
    exit;
}

$stmt->execute([$comment_id]);

header("Location: ../species_details.php?id=$category_id");
exit;
