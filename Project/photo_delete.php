<?php
session_start();
require('includes/db_connect.php');

$user_id = $_SESSION['user_id'] ?? null;
$photo_id = $_GET['id'] ?? null;

if (!$user_id || !$photo_id) {
    header("Location: gallery.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM photos WHERE photo_id = ? AND user_id = ?");
$stmt->execute([$photo_id, $user_id]);
$photo = $stmt->fetch(PDO::FETCH_ASSOC);

if ($photo) {
    if (file_exists($photo['photo_url'])) {
        unlink($photo['photo_url']);
    }

    $stmt_del = $db->prepare("DELETE FROM photos WHERE photo_id = ?");
    $stmt_del->execute([$photo_id]);
}

header("Location: species_details.php?id=" . $photo['species_id']);
exit;
?>
