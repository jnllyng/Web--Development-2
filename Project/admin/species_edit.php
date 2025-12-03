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
if (!ctype_digit((string)$id)) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM species WHERE species_id = ?");
$stmt->execute([$id]);
$species = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$species) {
    echo "Species not found.";
    exit;
}

$stmt_cat = $db->prepare("SELECT category_id, name FROM categories ORDER BY name ASC");
$stmt_cat->execute();
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type = $_POST['type'] ?? '';
    $family = trim($_POST['family'] ?? '');
    $scientific_name = trim($_POST['scientific_name'] ?? '');
    $common_name = trim($_POST['common_name'] ?? '');
    $status = $_POST['status'] ?? '';
    $category_id = $_POST['category_id'] !== "" ? $_POST['category_id'] : null;
    $delete_image = isset($_POST['delete_image']);

    $errors = [];
    $valid_types = ['Animal','Plant'];
    $valid_status = ['Not Listed','Special Concern','Threatened','Endangered','Extirpated'];

    if (!in_array($type, $valid_types, true)) $errors[] = 'Invalid type';
    if ($family === '') $errors[] = 'Family is required';
    if ($scientific_name === '') $errors[] = 'Scientific name is required';
    if ($common_name === '') $errors[] = 'Common name is required';
    if (!in_array($status, $valid_status, true)) $errors[] = 'Invalid status';
    if ($category_id !== null && !ctype_digit((string)$category_id)) $errors[] = 'Invalid category';

    if (!$errors) {
        $update = $db->prepare("
            UPDATE species
            SET type = ?, family = ?, scientific_name = ?, common_name = ?, status = ?, category_id = ?
            WHERE species_id = ?
        ");
        $update->execute([
            $type,
            $family,
            $scientific_name,
            $common_name,
            $status,
            $category_id,
            $id
        ]);

        if ($delete_image && $image) {
            @unlink('../uploads/species/' . $image);
            $db->prepare("DELETE FROM photos WHERE species_id = ? AND photo_url = ?")->execute([$id, $image]);
            $image = null;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $result = process_uploaded_image($_FILES['image'], '../uploads/species/');
            if (!isset($result['error'])) {
                $db->prepare("INSERT INTO photos (species_id, user_id, photo_url, upload_date) VALUES (?, ?, ?, NOW())")
                    ->execute([$id, $_SESSION['user_id'], $result['filename']]);
                $image = $result['filename'];
            } else {
                $message = $result['error'];
            }
        }

        if (empty($message)) {
            $_SESSION['message'] = "Species updated successfully.";
            header("Location: species_edit.php?id=$id");
            exit;
        }
    } else {
        $message = implode(', ', $errors);
    }
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

    <form method="POST" enctype="multipart/form-data" class="comment-edit-form">

        <label>Species Type:</label>
        <select name="type" required>
            <option value="Animal" <?= $species['type'] == 'Animal' ? 'selected' : '' ?>>Animal</option>
            <option value="Plant" <?= $species['type'] == 'Plant' ? 'selected' : '' ?>>Plant</option>
        </select>
        <br><br>

        <label>Family:</label>
        <input type="text" name="family" value="<?= htmlspecialchars($species['family']); ?>" required><br><br>

        <label>Scientific Name:</label>
        <input type="text" name="scientific_name" value="<?= htmlspecialchars($species['scientific_name']); ?>" required><br><br>

        <label>Common Name:</label>
        <input type="text" name="common_name" value="<?= htmlspecialchars($species['common_name']); ?>" required><br><br>

        <label>Status:</label>
        <select name="status" required>
            <option value="Not Listed" <?= $species['status'] == 'Not Listed' ? 'selected' : '' ?>>Not Listed</option>
            <option value="Special Concern" <?= $species['status'] == 'Special Concern' ? 'selected' : '' ?>>Special Concern</option>
            <option value="Threatened" <?= $species['status'] == 'Threatened' ? 'selected' : '' ?>>Threatened</option>
            <option value="Endangered" <?= $species['status'] == 'Endangered' ? 'selected' : '' ?>>Endangered</option>
            <option value="Extirpated" <?= $species['status'] == 'Extirpated' ? 'selected' : '' ?>>Extirpated</option>
        </select>
        <br><br>

        <label>Category:</label>
        <select name="category_id">
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>"
                    <?= ($species['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Current Image:</label><br>
        <?php if ($image): ?>
            <img src="../uploads/species/<?= $image; ?>" alt="Species Image" width="150"><br>
            <label><input type="checkbox" name="delete_image" value="1"> Delete current image</label><br><br>
        <?php else: ?>
            <p>No image uploaded</p><br>
        <?php endif; ?>

        <label>Upload New Image:</label>
        <input type="file" name="image" accept="image/*"><br><br>

        <div class="actions">
            <button type="submit">Update</button>
            <a href="dashboard.php" class="back">Cancel</a>
        </div>

    </form>

</body>

</html>
