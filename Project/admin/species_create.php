<?php
session_start();
require('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $family = trim($_POST['family']);
    $taxonomy = trim($_POST['taxonomy']);
    $scientific_name = trim($_POST['scientific_name']);
    $common_name = trim($_POST['common_name']);
    $status = $_POST['status'];
    $stmt = $db->prepare("INSERT INTO category (type, family, taxonomy, scientific_name, common_name, status)
                          VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$type, $family, $taxonomy, $scientific_name, $common_name, $status])) {
        $category_id = $db->lastInsertId();

        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/species/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $filename = basename($_FILES['image']['name']);
            $target_file = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $stmt_img = $db->prepare("INSERT INTO photos (category_id, photo_url) VALUES (?, ?)");
                $stmt_img->execute([$category_id, $filename]);
            }
        }

        $_SESSION['message'] = "New species added successfully.";
        header("Location: species_create.php");
        exit;
    } else {
        $message = "Failed to add species.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Species</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <h1>Add New Species</h1>
    <p class="back"><a href="dashboard.php">← Back to Dashboard</a></p>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Category:</label>
        <select name="category" required>
            <option value="Animal">Animal</option>
            <option value="Plant">Plant</option>
        </select><br><br>

        <label>Family:</label>
        <input type="text" name="family" required><br><br>

        <label>Taxonomy:</label>
        <input type="text" name="taxonomy" required><br><br>

        <label>Scientific Name:</label>
        <input type="text" name="scientific_name" required><br><br>

        <label>Common Name:</label>
        <input type="text" name="common_name" required><br><br>

        <label>Status:</label>
        <select name="status" required>
            <option value="Not Listed">Not Listed</option>
            <option value="Special Concern">Special Concern</option>
            <option value="Threatened">Threatened</option>
            <option value="Endangered">Endangered</option>
            <option value="Extirpated">Extirpated</option>
        </select><br><br>

        <label>Image:</label>
        <input type="file" name="image" accept="image/*"><br><br>

        <button type="submit">Add Species</button>
    </form>
</body>
</html>
