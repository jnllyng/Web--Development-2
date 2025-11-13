<?php
session_start();
require('includes/db_connect.php');

$category_id = $_POST['category_id'] ?? null;
$content = trim($_POST['content'] ?? '');
$user_id = $_SESSION['user_id'] ?? null;
$guest_name = trim($_POST['guest_name'] ?? '');

if (!$category_id || !$content) {
    die("Invalid comment data.");
}

if ($user_id) {
    $stmt = $db->prepare("INSERT INTO comments (category_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$category_id, $user_id, $content]);
} else {
    $stmt = $db->prepare("INSERT INTO comments (category_id, guest_name, content) VALUES (?, ?, ?)");
    $stmt->execute([$category_id, $guest_name, $content]);
}

header("Location: species_details.php?id=$category_id");
exit;
?>
