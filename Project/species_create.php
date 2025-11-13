<?php
session_start();
require('includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$type = $_GET['type'] ?? 'Animal';

$stmt = $db->prepare("SELECT * FROM categories WHERE type = ?");
$stmt->execute([$type]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $family = $_POST['family'];
    $taxonomy = $_POST['taxonomy'];
    $scientific_name = $_POST['scientific_name'];
    $common_name = $_POST['common_name'];
    $status = $_POST['status'];
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $db->prepare("INSERT INTO categories (type, family, taxonomy, scientific_name, common_name, status, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$type, $family, $taxonomy, $scientific_name, $common_name, $status, $user_id]);

    header("Location: {$type}.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New <?= htmlspecialchars($type) ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<form method="POST" class="add-species-form">
    <h1>Add New <?= htmlspecialchars($type) ?></h1>
    <label>Type:</label>
    <select name="type" required>
        <option value="Animal" <?= $type==='Animal'?'selected':'' ?>>Animal</option>
        <option value="Plant" <?= $type==='Plant'?'selected':'' ?>>Plant</option>
    </select>

    <label>Family:</label>
    <input type="text" name="family" required>

    <label>Taxonomy:</label>
    <input type="text" name="taxonomy" required>

    <label>Scientific Name:</label>
    <input type="text" name="scientific_name" required>

    <label>Common Name:</label>
    <input type="text" name="common_name" required>

    <label>Status:</label>
    <input type="text" name="status">

    <label>Category:</label>
    <select name="category_id" required>
        <?php foreach($categories as $cat): ?>
            <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['common_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Add <?= htmlspecialchars($type) ?></button>
    <a href="<?= $type ?>.php" class="back">Cancel</a>
</form>
</body>
</html>
