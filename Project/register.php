<?php
require('includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $error = "Username or email already exists!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        if ($stmt->execute([$username, $email, $hashedPassword])) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Registration failed. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Manitoba Nature Archive</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <main class="main-content">
        <div class="grid-container">
            <div class="form-header">
                <h1>Register</h1>
            </div>

            <?php if (!empty($error)): ?>
                <p class="form-error"><?=$error; ?></p>
            <?php endif; ?>

            <form method="POST" action="register.php" class="register-form">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Register</button>
            </form>

            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Login here</a>.</p>
            </div>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>
</body>
</html>
