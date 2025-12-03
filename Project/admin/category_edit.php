<?php
session_start();
require('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !ctype_digit($id)) {
    header('Location: category_list.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header('Location: category_list.php');
    exit;
}

$errors = [];
$name = $category['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $errors[] = 'Category name is required.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE categories SET name = ? WHERE category_id = ?");
        $stmt->execute([$name, $id]);
        header('Location: category_list.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <h1>Edit Category</h1>

    <p><a href="category_list.php">← Back to Category List</a></p>

    <?php if ($errors): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <label>Category Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
        <button type="submit">Update</button>
    </form>
</body>
</html>
