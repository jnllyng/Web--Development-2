<?php
session_start();
require('../includes/authenticate.php');
require('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$species = isset($_GET['species']) ? $_GET['species'] : 'Animal';

$cat_stmt = $db->prepare("SELECT category_id, name FROM categories ORDER BY name ASC");
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("
    SELECT s.*, c.name AS category_name
    FROM species s
    LEFT JOIN categories c ON s.category_id = c.category_id
    WHERE s.type = ?
    ORDER BY s.species_id ASC
");
$stmt->execute([$species]);
$species_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>

<h1>Admin Dashboard - Species List</h1>

<nav>
    <a href="dashboard.php?species=Animal">Animal</a> |
    <a href="dashboard.php?species=Plant">Plant</a> |
    <a href="category_list.php">Category</a> |
    <a href="user_list.php">User</a> |
    <a href="comment_list.php">Comment</a> |
    <a href="species_create.php">+ Add New Species</a> |
    <a href="../index.php">Go Back to Main Page</a>
</nav>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>#</th>
            <th>Species</th>
            <th>Family</th>
            <th>Scientific Name</th>
            <th>Common Name</th>
            <th>Status</th>
            <th>Category</th> 
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php $i = 1; foreach ($species_list as $sp): ?>
        <tr>
            <td><?= $i++; ?></td>
            <td><?= htmlspecialchars($sp['type']); ?></td>
            <td><?= htmlspecialchars($sp['family']); ?></td>
            <td><?= htmlspecialchars($sp['scientific_name']); ?></td>
            <td><?= htmlspecialchars($sp['common_name']); ?></td>
            <td><?= htmlspecialchars($sp['status']); ?></td>
            <td>
                <?= $sp['category_name'] 
                    ? htmlspecialchars($sp['category_name']) 
                    : "<span style='color:gray'>None</span>" ?>
            </td>

            <td>
                <a href="species_edit.php?id=<?= $sp['species_id']; ?>">Edit</a> |
                <a href="species_delete.php?id=<?= $sp['species_id']; ?>"
                   onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
