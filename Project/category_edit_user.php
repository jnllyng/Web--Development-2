<?php
session_start();
require('includes/db_connect.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$id = $_GET['id'] ?? null;
if (!$id || !ctype_digit((string)$id)) {
    header("Location: category_list_user.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    echo "Category not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if ($name !== '') {
        $stmt2 = $db->prepare("UPDATE categories SET name=? WHERE category_id=?");
        $stmt2->execute([$name, $id]);

        header("Location: category_list_user.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <?php include('includes/header.php'); ?>

    <main id="main" class="main-content">
        <div class="grid-container">

            <form method="POST" class="add-species-form">
                <h1>Edit Category</h1>
                <a href="category_list_user.php"></a>
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>

                <button type="submit">Update</button>
                <a href="category_delete_user.php?id=<?= $id ?>" class="delete-btn"
                    onclick="return confirm('Are you sure you want to delete this category?')">
                    Delete
                </a>
                <div>
                    <a href="category_list_user.php"> Back</a>
                </div>
            </form>

        </div>
    </main>

    <?php include('includes/footer.php'); ?>

</body>

</html>
