<?php
session_start();
require('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$category_id = intval($_GET['id']);

$stmt = $db->prepare("SELECT * FROM category WHERE category_id = ?");
$stmt->execute([$category_id]);
$categories = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categories) {
    echo "Species not found.";
    exit;
}

if (isset($_POST['confirm_delete'])) {

    $delete_photos = $db->prepare("DELETE FROM photos WHERE category_id = ?");
    $delete_photos->execute([$category_id]);

    $delete_stmt = $db->prepare("DELETE FROM categories WHERE category_id = ?");
    $delete_stmt->execute([$category_id]);

    $_SESSION['message'] = "Species deleted successfully.";
    header("Location: dashboard.php?category=" . urlencode($categories['type']));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Species</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <h1>Admin Dashboard - Delete Species</h1>
    <p>Are you sure you want to delete the species: <?= htmlspecialchars($categories['scientific_name']); ?>?</p>

    <form method="POST">
        <button type="submit" name="confirm_delete">Yes, Delete</button>
        <button type="button" onclick="window.location.href='dashboard.php?category=<?= urlencode($categories['category']); ?>'">Cancel</button>
    </form>
</body>
</html>
