<?php
session_start();
require('includes/db_connect.php');

$user_id = $_SESSION['user_id'] ?? null;
$photo_id = $_GET['id'] ?? null;

if (!$user_id || !$photo_id) {
    header("Location: gallery.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM photos WHERE photo_id = ? AND user_id = ?");
$stmt->execute([$photo_id, $user_id]);
$photo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$photo) {
    header("Location: gallery.php");
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_url = trim($_POST['photo_url']);
    if (filter_var($new_url, FILTER_VALIDATE_URL)) {
        $stmt_update = $db->prepare("UPDATE photos SET photo_url = ? WHERE photo_id = ?");
        $stmt_update->execute([$new_url, $photo_id]);
        $message = "Photo updated successfully!";
        header("Location: species_details.php?id=" . $photo['species_id']);
        exit;
    } else {
        $message = "Invalid URL.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Photo</title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include('includes/header.php'); ?>

<main class="main-content">
    <h1>Edit Your Observation Photo</h1>
    <?php if ($message) echo "<p>$message</p>"; ?>

    <form method="POST">
        <label>Photo URL:</label><br>
        <input type="url" name="photo_url" value="<?= htmlspecialchars($photo['photo_url']) ?>" required><br><br>
        <button type="submit">Update</button>
    </form>

    <p><a href="species_details.php?id=<?= $photo['species_id'] ?>">← Back to Species Details</a></p>
</main>

<?php include('includes/footer.php'); ?>
</body>
</html>
