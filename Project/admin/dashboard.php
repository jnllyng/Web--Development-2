<?php
session_start();
require('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
$category = isset($_GET['category']) ? $_GET['category'] : 'Animal';

$stmt = $db->prepare("SELECT * FROM species WHERE category = ? ORDER BY species_id ASC");
$stmt->execute([$category]);
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
    <a href="dashboard.php?category=Animal">Animal</a> |
    <a href="dashboard.php?category=Plant">Plant</a> |
    <a href="species_create.php">+ Add New Species</a>
    <a href="user_list.php">User</a>
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
        foreach($species_list as $sp):
        ?>
        <tr>
            <td><?= $i++; ?></td>
            <td><?= $sp['category']; ?></td>
            <td><?= $sp['family']; ?></td>
            <td><?= $sp['taxonomy']; ?></td>
            <td><?= $sp['scientific_name']; ?></td>
            <td><?= $sp['common_name']; ?></td>
            <td><?= $sp['status']; ?></td>
            <td>
                <a href="species_edit.php?id=<?= $sp['species_id']; ?>">Edit</a> |
                <a href="species_delete.php?id=<?= $sp['species_id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</body>


