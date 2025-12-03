<?php
session_start();
require('includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if ($name !== '') {
        $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);

        header("Location: category_list_user.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Category</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php include('includes/header.php'); ?>

<main id="main" class="main-content">
<div class="grid-container">

<form method="POST" class="add-species-form">
    <h1>Add New Category</h1>

    <label>Name:</label>
    <input type="text" name="name" required>

    <button type="submit">Add</button>
    <a href="category_list_user.php" class="back">Cancel</a>
</form>

</div>
</main>

<?php include('includes/footer.php'); ?>

</body>
</html>
