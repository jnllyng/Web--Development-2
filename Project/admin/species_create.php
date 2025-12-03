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
        $stmt = $db->prepare("
            INSERT INTO species (type, family, scientific_name, common_name, status, category_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if ($stmt->execute([$type, $family, $scientific_name, $common_name, $status, $category_id])) {
            
            $species_id = $db->lastInsertId();

            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $result = process_uploaded_image($_FILES['image'], '../uploads/species/');
                if (!isset($result['error'])) {
                    $stmt_img = $db->prepare("
                        INSERT INTO photos (species_id, photo_url) 
                        VALUES (?, ?)
                    ");
                    $stmt_img->execute([$species_id, $result['filename']]);
                } else {
                    $message = $result['error'];
                }
            }

            if (empty($message)) {
                $_SESSION['message'] = "New species added successfully.";
                header("Location: species_create.php");
                exit;
            }

        } else {
            $message = "Failed to add species.";
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

        <label>Species Type:</label>
        <select name="type" required>
            <option value="Animal">Animal</option>
            <option value="Plant">Plant</option>
        </select><br><br>

        <label>Family:</label>
        <input type="text" name="family" required><br><br>

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

        <label>Category:</label>
        <select name="category_id">
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Image:</label>
        <input type="file" name="image" accept="image/*"><br><br>

        <button type="submit">Add Species</button>
    </form>
</body>
</html>
