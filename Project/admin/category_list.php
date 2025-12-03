<?php
session_start();
require('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$stmt = $db->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Category List</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <h1>Categories</h1>

    <div class="category-top-links">
        <a href="category_create.php">+ Add New Category</a>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>


    <?php if ($categories): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= htmlspecialchars($cat['category_id']) ?></td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td>
                            <a href="category_edit.php?id=<?= $cat['category_id'] ?>">Edit</a> |
                            <a href="category_delete.php?id=<?= $cat['category_id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No categories yet.</p>
    <?php endif; ?>
</body>

</html>