<?php
session_start();
require('includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !ctype_digit((string)$id)) {
    header("Location: Animal.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM species WHERE species_id = ?");
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

$category_stmt = $db->query("SELECT * FROM categories ORDER BY name ASC");
$category_list = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

function process_uploaded_image(array $file, string $upload_dir, int $max_dim = 1200)
{
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Upload failed.'];
    }

    $info = @getimagesize($file['tmp_name']);
    if ($info === false) {
        return ['error' => 'File is not a valid image.'];
    }

    $mime = $info['mime'] ?? '';
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    if (!isset($allowed[$mime])) {
        return ['error' => 'Unsupported image type.'];
    }

    $ext = $allowed[$mime];
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $src_w = $info[0];
    $src_h = $info[1];
    $ratio = min($max_dim / $src_w, $max_dim / $src_h, 1);
    $new_w = (int)($src_w * $ratio);
    $new_h = (int)($src_h * $ratio);

    switch ($mime) {
        case 'image/jpeg':
            $src = imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/png':
            $src = imagecreatefrompng($file['tmp_name']);
            break;
        case 'image/gif':
            $src = imagecreatefromgif($file['tmp_name']);
            break;
        case 'image/webp':
            $src = imagecreatefromwebp($file['tmp_name']);
            break;
        default:
            return ['error' => 'Unsupported image type.'];
    }

    if (!$src) {
        return ['error' => 'Could not process image.'];
    }

    $dst = imagecreatetruecolor($new_w, $new_h);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);

    $filename = uniqid('species_', true) . '.' . $ext;
    $target = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $filename;

    $saved = false;
    switch ($mime) {
        case 'image/jpeg':
            $saved = imagejpeg($dst, $target, 85);
            break;
        case 'image/png':
            $saved = imagepng($dst, $target);
            break;
        case 'image/gif':
            $saved = imagegif($dst, $target);
            break;
        case 'image/webp':
            $saved = imagewebp($dst, $target, 85);
            break;
    }

    imagedestroy($src);
    imagedestroy($dst);

    if (!$saved) {
        return ['error' => 'Failed to save image.'];
    }

    return ['filename' => $filename];
}

$img_stmt = $db->prepare("SELECT photo_url FROM photos WHERE species_id = ? ORDER BY upload_date ASC LIMIT 1");
$img_stmt->execute([$id]);
$current_image = $img_stmt->fetchColumn();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'] ?? null;
    $family = trim($_POST['family'] ?? '');
    $scientific_name = trim($_POST['scientific_name'] ?? '');
    $common_name = trim($_POST['common_name'] ?? '');
    $status = $_POST['status'] ?? '';
    $delete_image = isset($_POST['delete_image']);

    $errors = [];
    $valid_status = ['Not Listed','Special Concern','Threatened','Endangered','Extirpated'];

    if (!$category_id || !ctype_digit((string)$category_id)) $errors[] = 'Invalid category';
    if ($family === '') $errors[] = 'Family is required';
    if ($scientific_name === '') $errors[] = 'Scientific name is required';
    if ($common_name === '') $errors[] = 'Common name is required';
    if (!in_array($status, $valid_status, true)) $errors[] = 'Invalid status';

    if (!$errors) {
        $update = $db->prepare("
            UPDATE species
            SET category_id = ?, family = ?, scientific_name = ?, common_name = ?, status = ?
            WHERE species_id = ?
        ");
        $update->execute([$category_id, $family, $scientific_name, $common_name, $status, $id]);

        if ($delete_image && $current_image) {
            @unlink('uploads/species/' . $current_image);
            $db->prepare("DELETE FROM photos WHERE species_id = ? AND photo_url = ?")->execute([$id, $current_image]);
            $current_image = null;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $result = process_uploaded_image($_FILES['image'], 'uploads/species/');
            if (!isset($result['error'])) {
                $db->prepare("INSERT INTO photos (species_id, user_id, photo_url, upload_date) VALUES (?, ?, ?, NOW())")
                    ->execute([$id, $species['user_id'], $result['filename']]);
                $current_image = $result['filename'];
            } else {
                $message = $result['error'];
            }
        }

        if (empty($message)) {
            header("Location: species_details.php?id=$id");
            exit;
        }
    } else {
        $message = implode(', ', $errors);
    }
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
    <?php include('includes/header.php'); ?>
    <form method="POST" enctype="multipart/form-data" class="species-edit-form">
        <h1>Edit <?= htmlspecialchars($species['common_name']) ?></h1>
        <?php if (!empty($message)): ?>
            <p class="form-error"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <label>Type:</label>
        <input type="text" value="<?= htmlspecialchars($species['type']) ?>" disabled>

        <label>Category:</label>
        <select name="category_id" required>
            <?php foreach ($category_list as $cat): ?>
                <option value="<?= $cat['category_id'] ?>" <?= $species['category_id'] == $cat['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Family:</label>
        <input type="text" name="family" value="<?= htmlspecialchars($species['family']) ?>" required>

        <label>Scientific Name:</label>
        <input type="text" name="scientific_name" value="<?= htmlspecialchars($species['scientific_name']) ?>" required>

        <label>Common Name:</label>
        <input type="text" name="common_name" value="<?= htmlspecialchars($species['common_name']) ?>" required>

        <label>Status:</label>
        <select name="status" required>
            <option value="Not Listed">Not Listed</option>
            <option value="Special Concern">Special Concern</option>
            <option value="Threatened">Threatened</option>
            <option value="Endangered">Endangered</option>
            <option value="Extirpated">Extirpated</option>
        </select>

        <label>Current Image:</label><br>
        <?php if ($current_image): ?>
            <img src="uploads/species/<?= htmlspecialchars($current_image) ?>" alt="Species Image" width="150"><br>
            <label><input type="checkbox" name="delete_image" value="1"> Delete current image</label><br>
        <?php else: ?>
            <p>No image uploaded</p>
        <?php endif; ?>

        <label>Upload New Image (optional):</label>
        <input type="file" name="image" accept="image/*">

        <div class="actions" style="display:flex; gap:10px; align-items:center; justify-content:center; margin-top:20px;">
            <button type="submit">Update</button>

            <a href="species_delete.php?id=<?= $species['species_id'] ?>" class="delete-btn"
                onclick="return confirm('Are you sure you want to delete this species?');">
                Delete
            </a>
        </div>
    </form>
    <?php include('includes/footer.php'); ?>
</body>

</html>
