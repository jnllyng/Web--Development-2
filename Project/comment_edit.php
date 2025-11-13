<?php
session_start();
require('includes/db_connect.php');

$user_id = $_SESSION['user_id'] ?? null;
$comment_id = $_GET['id'] ?? null;

if (!$user_id || !$comment_id) {
    header('Location: login.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM comments WHERE comment_id = ?");
$stmt->execute([$comment_id]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comment || $comment['user_id'] != $user_id) {
    echo "You cannot edit this comment.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);
    if ($content) {
        $stmt_update = $db->prepare("UPDATE comments SET content = ? WHERE comment_id = ?");
        $stmt_update->execute([$content, $comment_id]);
        header("Location: species_details.php?id=" . $comment['category_id']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Comment</title>
</head>
<body>
<h1>Edit Comment</h1>
<form method="POST">
    <textarea name="content" rows="4" required><?= htmlspecialchars($comment['content']) ?></textarea><br>
    <button type="submit">Update Comment</button>
</form>
<p><a href="species_details.php?id=<?= $comment['category_id'] ?>">Cancel</a></p>
</body>
</html>
