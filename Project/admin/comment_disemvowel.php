<?php
session_start();
require('../includes/db_connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

$comment_id = $_GET['id'] ?? null;

if (!$comment_id) {
    echo "Invalid comment ID.";
    exit;
}

$stmt = $db->prepare("SELECT content, species_id FROM comments WHERE comment_id = ?");
$stmt->execute([$comment_id]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comment) {
    echo "Comment not found.";
    exit;
}

$disemvoweled = preg_replace('/[aeiouAEIOU]/', '', $comment['content']);

$stmt = $db->prepare("UPDATE comments SET content = ? WHERE comment_id = ?");
$stmt->execute([$disemvoweled, $comment_id]);

$species_id = $comment['species_id'];
header("Location: ../species_details.php?id=$species_id");
exit;
?>
