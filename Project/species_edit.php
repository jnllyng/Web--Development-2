<?php
session_start();
require('includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: {$type}.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->execute([$id]);
$species = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$species) {
    echo "Species not found.";
    exit;
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $species['user_id']) {
    echo "You cannot edit this species.";
    exit;
}

$stmt = $db->prepare("SELECT * FROM categories WHERE type = ?");
$stmt->execute([$species['type']]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $family = $_POST['family'];
    $taxonomy = $_POST['taxonomy'];
    $scientific_name = $_POST['scientific_name'];
    $common_name = $_POST['common_name'];
    $status = $_POST['status'];
    $category_id = $_POST['category_id'];

    $stmt = $db->prepare("UPDATE categories SET family=?, taxonomy=?, scientific_name=?, common_name=?, status=?, category_id=? WHERE category_id=?");
    $stmt->execute([$family, $taxonomy, $scientific_name, $common_name, $status, $category_id, $id]);

    header("Location: species_details.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit <?= htmlspecialchars($species['common_name']) ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <form method="POST" class="species-edit-form">
        <h1>Edit <?= htmlspecialchars($species['common_name']) ?></h1>
        <label>Family:</label>
        <input type="text" name="family" value="<?= htmlspecialchars($species['family']) ?>" required>

        <label>Taxonomy:</label>
        <input type="text" name="taxonomy" value="<?= htmlspecialchars($species['taxonomy']) ?>" required>

        <label>Scientific Name:</label>
        <input type="text" name="scientific_name" value="<?= htmlspecialchars($species['scientific_name']) ?>" required>

        <label>Common Name:</label>
        <input type="text" name="common_name" value="<?= htmlspecialchars($species['common_name']) ?>" required>

        <label>Status:</label>
        <input type="text" name="status" value="<?= htmlspecialchars($species['status']) ?>">

        <label>Category:</label>
        <select name="category_id" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>" <?= $cat['category_id'] == $species['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['common_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Update</button>
        <a href="species_delete.php?id=<?= $species['category_id'] ?>" class="delete-btn"
            onclick="return confirm('Are you sure you want to delete this species?');">
            Delete
        </a>

    </form>
</body>

</html>