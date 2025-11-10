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
$stmt = $db->prepare("SELECT * FROM species WHERE species_id = ?");
$stmt->execute([$id]);
$species = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$species) {
    echo "Species not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $family = trim($_POST['family']);
    $taxonomy = trim($_POST['taxonomy']);
    $scientific_name = trim($_POST['scientific_name']);
    $common_name = trim($_POST['common_name']);
    $status = $_POST['status'];

    $update = $db->prepare("UPDATE species 
                            SET category = ?, family = ?, taxonomy = ?, scientific_name = ?, common_name = ?, status = ? 
                            WHERE species_id = ?");
    $update->execute([$category, $family, $taxonomy, $scientific_name, $common_name, $status, $id]);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/species/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $filename = basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $stmt_img = $db->prepare("INSERT INTO photos (species_id, user_id, photo_url, upload_date) VALUES (?, ?, ?, NOW())");
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

$stmt_img = $db->prepare("SELECT photo_url FROM photos WHERE species_id = ? ORDER BY upload_date ASC LIMIT 1");
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
    <p class="back"><a href="dashboard.php">← Back to Dashboard</a></p>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Category:</label>
        <select name="category" required>
            <option value="Animal" <?= $species['category'] == 'Animal' ? 'selected' : '' ?>>Animal</option>
            <option value="Plant" <?= $species['category'] == 'Plant' ? 'selected' : '' ?>>Plant</option>
        </select><br><br>

        <label>Family:</label>
        <input type="text" name="family" value="<?= htmlspecialchars($species['family']); ?>" required><br><br>

        <label>Taxonomy:</label>
        <input type="text" name="taxonomy" value="<?= $species['taxonomy']; ?>" required><br><br>

        <label>Scientific Name:</label>
        <input type="text" name="scientific_name" value="<?= $species['scientific_name']; ?>" required><br><br>

        <label>Common Name:</label>
        <input type="text" name="common_name" value="<?= $species['common_name']; ?>" required><br><br>

        <label>Status:</label>
        <select name="status" required>
            <option value="Not Listed" <?= $species['status'] == 'Not Listed' ? 'selected' : '' ?>>Not Listed</option>
            <option value="Special Concern" <?= $species['status'] == 'Special Concern' ? 'selected' : '' ?>>Special
                Concern</option>
            <option value="Threatened" <?= $species['status'] == 'Threatened' ? 'selected' : '' ?>>Threatened</option>
            <option value="Endangered" <?= $species['status'] == 'Endangered' ? 'selected' : '' ?>>Endangered</option>
            <option value="Extirpated" <?= $species['status'] == 'Extirpated' ? 'selected' : '' ?>>Extirpated</option>
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

        <button type="submit">Update Species</button>
    </form>
</body>

</html>