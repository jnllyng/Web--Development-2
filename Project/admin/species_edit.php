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

$id = $_GET['id'];
$stmt = $db->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->execute([$id]);
$categories = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categories) {
    echo "Species not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $family = trim($_POST['family']);
    $taxonomy = trim($_POST['taxonomy']);
    $scientific_name = trim($_POST['scientific_name']);
    $common_name = trim($_POST['common_name']);
    $status = $_POST['status'];

    $update = $db->prepare("UPDATE categories 
                            SET type = ?, family = ?, taxonomy = ?, scientific_name = ?, common_name = ?, status = ? 
                            WHERE category_id = ?");
    $update->execute([$type, $family, $taxonomy, $scientific_name, $common_name, $status, $id]);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/species/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $filename = basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $stmt_img = $db->prepare("INSERT INTO photos (category_id, user_id, photo_url, upload_date) VALUES (?, ?, ?, NOW())");
            $stmt_img->execute([$id, $_SESSION['user_id'], $filename]);
        }
    }

    $_SESSION['message'] = "Species updated successfully.";
    header("Location: species_edit.php?id=$id");
    exit;
}
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$stmt_img = $db->prepare("SELECT photo_url FROM photos WHERE category_id = ? ORDER BY upload_date ASC LIMIT 1");
$stmt_img->execute([$id]);
$image = $stmt_img->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Species</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <h1>Edit Species</h1>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="comment-edit-form">
        <label>Category:</label>
        <select name="category" required>
            <option value="Animal" <?= $categories['type'] == 'Animal' ? 'selected' : '' ?>>Animal</option>
            <option value="Plant" <?= $categories['type'] == 'Plant' ? 'selected' : '' ?>>Plant</option>
        </select><br><br>

        <label>Family:</label>
        <input type="text" name="family" value="<?= htmlspecialchars($categories['family']); ?>" required><br><br>

        <label>Taxonomy:</label>
        <input type="text" name="taxonomy" value="<?= $categories['taxonomy']; ?>" required><br><br>

        <label>Scientific Name:</label>
        <input type="text" name="scientific_name" value="<?= $categories['scientific_name']; ?>" required><br><br>

        <label>Common Name:</label>
        <input type="text" name="common_name" value="<?= $categories['common_name']; ?>" required><br><br>

        <label>Status:</label>
        <select name="status" required>
            <option value="Not Listed" <?= $categories['status'] == 'Not Listed' ? 'selected' : '' ?>>Not Listed</option>
            <option value="Special Concern" <?= $categories['status'] == 'Special Concern' ? 'selected' : '' ?>>Special
                Concern</option>
            <option value="Threatened" <?= $categories['status'] == 'Threatened' ? 'selected' : '' ?>>Threatened</option>
            <option value="Endangered" <?= $categories['status'] == 'Endangered' ? 'selected' : '' ?>>Endangered</option>
            <option value="Extirpated" <?= $categories['status'] == 'Extirpated' ? 'selected' : '' ?>>Extirpated</option>
        </select>
        <br><br>

        <label>Current Image:</label><br>
        <?php if ($image): ?>
            <img src="../uploads/species/<?= $image; ?>" alt="Species Image" width="150"><br>
        <?php else: ?>
            <p>No image uploaded</p><br>
        <?php endif; ?>

        <label>Change / Upload New Image:</label>
        <input type="file" name="image" accept="image/*"><br><br>
        <div class="actions">
            <button type="submit">Update</button>
            <a href="user_list.php" class="back">Cancel</a>
        </div>

    </form>

</body>

</html>