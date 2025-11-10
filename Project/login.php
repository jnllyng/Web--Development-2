<?php
session_start();
require('includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: /Web-Development-2/Project/admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Manitoba Nature Archive</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <?php include('includes/header.php'); ?>

    <main class="main-content">
        <div class="grid-container">
            <div class="form-header">
                <h1>Login</h1>
            </div>
            <?php if (!empty($error)): ?>
                 <p class="form-error"><?= htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST" action="login.php" class="login-form">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Login</button>
            </form>

            <div class="form-footer">
                <p>Don’t have an account? <a href="register.php">Register here</a>.</p>
            </div>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>
</body>

</html>