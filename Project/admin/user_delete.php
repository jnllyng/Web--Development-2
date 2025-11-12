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

$user_id = intval($_GET['id']);

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

if (isset($_POST['confirm_delete'])) {

    $delete_stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
    $delete_stmt->execute([$user_id]);

    $_SESSION['message'] = "User deleted successfully.";
    header("Location: user_list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delete User</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <h1>Admin Dashboard - Delete User</h1>
    <p>Are you sure you want to delete the user: <?= htmlspecialchars($user['username']); ?>?</p>

     <?php if ($message): ?>
        <p><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <button type="submit" name="confirm_delete">Yes, Delete</button>
        <button type="button" onclick="window.location.href='user_list.php'">Cancel</button>
    </form>
</body>

</html>