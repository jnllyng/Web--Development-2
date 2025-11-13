<?php
session_start();
require('../includes/authenticate.php');
require('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
$category = isset($_GET['category']) ? $_GET['category'] : 'Animal';

$stmt = $db->prepare("SELECT * FROM categories WHERE type = ? ORDER BY category_id ASC");
$stmt->execute([$category]);
$category_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user_stmt = $db->prepare("SELECT * FROM users ORDER BY user_id ASC");
$user_stmt->execute();
$user_list = $user_stmt->fetchAll(PDO::FETCH_ASSOC);

$comments_stmt = $db->prepare("
    SELECT c.comment_id, c.category_id, c.user_id, c.guest_name, c.content, c.created_at,
           u.username AS logged_username, cat.common_name
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.user_id
    LEFT JOIN categories cat ON c.category_id = cat.category_id
    ORDER BY c.created_at DESC
");
$comments_stmt->execute();
$all_comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <a href="dashboard.php?category=Animal">Animal</a> |
        <a href="dashboard.php?category=Plant">Plant</a> |
        <a href="user_list.php">User</a> |
        <a href="comment_list.php">Comment</a> |
        <a href="species_create.php">+ Add New Species</a> |
        <a href="../index.php">Go Back to Main Page</a>
    </nav>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>#</th>
                <th>Category</th>
                <th>Family</th>
                <th>Taxonomy</th>
                <th>Scientific Name</th>
                <th>Common Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($category_list as $sp):
                ?>
                <tr>
                    <td><?= $i++; ?></td>
                    <td><?= $sp['type']; ?></td>
                    <td><?= $sp['family']; ?></td>
                    <td><?= $sp['taxonomy']; ?></td>
                    <td><?= $sp['scientific_name']; ?></td>
                    <td><?= $sp['common_name']; ?></td>
                    <td><?= $sp['status']; ?></td>
                    <td>
                        <a href="species_edit.php?id=<?= $sp['category_id']; ?>">Edit</a> |
                        <a href="species_delete.php?id=<?= $sp['category_id']; ?>"
                            onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
</body>