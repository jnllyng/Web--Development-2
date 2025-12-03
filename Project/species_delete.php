<?php
session_start();
require('includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? 'Animal';

if (!$id) {
    header("Location: {$type}.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM species WHERE species_id = ?");
$stmt->execute([$id]);
$species = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$species) {
    echo "Species not found.";
    exit;
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $species['user_id']) {
    echo "You do not have permission to delete this species.";
    exit;
}

$delete_photos = $db->prepare("DELETE FROM photos WHERE species_id = ?");
$delete_photos->execute([$id]);

$stmt = $db->prepare("DELETE FROM species WHERE species_id = ?");
$stmt->execute([$id]);

header("Location: {$type}.php");
exit;
?>
