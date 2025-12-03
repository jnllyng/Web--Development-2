<?php
session_start();
require('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Invalid category.";
    exit;
}

$category_id = intval($_GET['id']);

$stmt = $db->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    echo "Category not found.";
    exit;
}

if (isset($_POST['confirm_delete'])) {
    $remove_from_species = $db->prepare("UPDATE species SET category_id = NULL WHERE category_id = ?");
    $remove_from_species->execute([$category_id]);

    $delete_stmt = $db->prepare("DELETE FROM categories WHERE category_id = ?");
    $delete_stmt->execute([$category_id]);

    $_SESSION['message'] = "Category deleted successfully.";
    header("Location: category_list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Category</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <h1>Delete Category</h1>

    <p>Are you sure you want to delete category: <strong><?= htmlspecialchars($category['name']) ?></strong>?</p>

    <form method="POST">
        <button type="submit" name="confirm_delete">Yes, Delete</button>
        <a href="category_list.php" class="back">Cancel</a>
    </form>
</body>
</html>
