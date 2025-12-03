<?php
session_start();
require('../includes/db_connect.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
$query = "SELECT * FROM users";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - User Management</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <h1>Admin Dashboard - User Management</h1>
    <nav>
        <a href="user_create.php">+ Add New User</a> |
        <a href="dashboard.php">Back to Main Dashboard</a>
    </nav>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            foreach ($users as $user): ?>
                <tr>
                    <td><?= $i++; ?></td>
                    <td><?= $user['username']; ?></td>
                    <td><?= $user['email']; ?></td>
                    <td>
                        <a href="user_edit.php?id=<?= $user['user_id']; ?>">Edit</a> |
                        <a href="user_delete.php?id=<?= $user['user_id']; ?>"
                            onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</body>

</html>