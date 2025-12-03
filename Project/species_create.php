<?php
session_start();
require('includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$type = $_GET['type'] ?? 'Animal';

$category_stmt = $db->prepare("SELECT * FROM categories ORDER BY name ASC");
$category_stmt->execute();
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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $family = trim($_POST['family'] ?? '');
    $scientific_name = trim($_POST['scientific_name'] ?? '');
    $common_name = trim($_POST['common_name'] ?? '');
    $status = $_POST['status'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $user_id = $_SESSION['user_id'];

    $errors = [];
    $valid_types = ['Animal','Plant'];
    $valid_status = ['Not Listed','Special Concern','Threatened','Endangered','Extirpated'];

    if (!in_array($type, $valid_types, true)) $errors[] = 'Invalid type';
    if ($family === '') $errors[] = 'Family is required';
    if ($scientific_name === '') $errors[] = 'Scientific name is required';
    if ($common_name === '') $errors[] = 'Common name is required';
    if (!in_array($status, $valid_status, true)) $errors[] = 'Invalid status';
    if (!$category_id || !ctype_digit((string)$category_id)) $errors[] = 'Invalid category';

    if (!$errors) {
        $stmt = $db->prepare("
            INSERT INTO species (type, category_id, family, scientific_name, common_name, status, user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        if ($stmt->execute([$type, $category_id, $family, $scientific_name, $common_name, $status, $user_id])) {
            $species_id = $db->lastInsertId();
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $result = process_uploaded_image($_FILES['image'], 'uploads/species/');
                if (!isset($result['error'])) {
                    $db->prepare("INSERT INTO photos (species_id, user_id, photo_url, upload_date) VALUES (?, ?, ?, NOW())")
                       ->execute([$species_id, $user_id, $result['filename']]);
                } else {
                    $error = $result['error'];
                }
            }
            if (empty($error)) {
                header("Location: {$type}.php");
                exit;
            }
        } else {
            $error = 'Failed to create species.';
        }
    } else {
        $error = implode(', ', $errors);
    }
}
$category_id = $_GET['category_id'] ?? null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New <?= htmlspecialchars($type) ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
<?php include('includes/header.php'); ?>

<main id="main" class="main-content">

<form method="POST" enctype="multipart/form-data" class="add-species-form">

    <h1>Add New <?= htmlspecialchars($type) ?></h1>
    <?php if (!empty($error)): ?>
        <p class="form-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <label>Type:</label>
    <select name="type" required>
        <option value="Animal" <?= $type==='Animal'?'selected':'' ?>>Animal</option>
        <option value="Plant" <?= $type==='Plant'?'selected':'' ?>>Plant</option>
    </select>

    <label>Category:</label>
    <select name="category_id" required>
        <?php foreach ($category_list as $cat): ?>
            <option value="<?= $cat['category_id'] ?>">
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Family:</label>
    <input type="text" name="family" required>

    <label>Scientific Name:</label>
    <input type="text" name="scientific_name" required>

    <label>Common Name:</label>
    <input type="text" name="common_name" required>

    <label>Status:</label>
    <select name="status" required>
        <option value="Not Listed">Not Listed</option>
        <option value="Special Concern">Special Concern</option>
        <option value="Threatened">Threatened</option>
        <option value="Endangered">Endangered</option>
        <option value="Extirpated">Extirpated</option>
    </select>

    <label>Image (optional):</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit">Add</button>
    <a href="category_detail.php?id=<?= $category_id ?>" class="back">Cancel</a>
</form>

</main>

<?php include('includes/footer.php'); ?>
</body>
</html>
