<?php
session_start();
require('includes/db_connect.php');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$category_id = $_GET['category_id'] ?? null;
if (!$category_id) {
    echo "Invalid species ID.";
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photos'])) {
    $upload_dir = 'uploads/species/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $uploaded_count = 0;

    foreach ($_FILES['photos']['tmp_name'] as $index => $tmp_name) {
        $file_name = basename($_FILES['photos']['name'][$index]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed)) continue;

        $new_file_name = uniqid('species_', true) . '.' . $file_ext;
        $target_file = $upload_dir . $new_file_name;

        if (move_uploaded_file($tmp_name, $target_file)) {
            $stmt = $db->prepare("INSERT INTO photos (category_id, user_id, photo_url, upload_date) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$category_id, $user_id, $target_file]);
            $uploaded_count++;
        }
    }

    $message = $uploaded_count > 0 ? "$uploaded_count photos uploaded successfully!" : "No valid photos were uploaded.";
    header("Location: species_details.php?id=" . $category_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Observation Photos</title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include('includes/header.php'); ?>

<main class="main-content">
    <h1>Upload Observation Photos</h1>

    <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Select Photos (you can select multiple):</label><br>
        <input type="file" name="photos[]" multiple required><br><br>

        <button type="submit">Upload</button>
    </form>

    <p><a href="species_details.php?id=<?= $category_id ?>">← Back to Species Details</a></p>
</main>

<?php include('includes/footer.php'); ?>
</body>
</html>
