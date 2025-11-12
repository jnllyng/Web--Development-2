<?php
session_start();
require('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = intval($_GET['id']); 
$stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $password = $user['password'];
    }

    $update = $db->prepare("UPDATE users 
                            SET username = ?, email = ?, password = ?, role = ?
                            WHERE user_id = ?");
    $update->execute([$username, $email, $password, $role, $id]);

    $_SESSION['message'] = "User updated successfully.";
    header("Location: user_edit.php?id=$id");
    exit;
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <h1>Edit User</h1>
    <p class="back"><a href="user_list.php">← Back to Dashboard</a></p>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" required><br><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" placeholder="Leave blank to keep current"><br><br>

        <label>Role:</label>
        <select name="role" required>
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
        <br><br>

        <button type="submit">Update User</button>
    </form>
</body>
</html>
