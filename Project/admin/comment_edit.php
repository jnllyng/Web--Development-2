<?php
session_start();
require('../includes/authenticate.php');
require('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$comment_id = $_GET['id'] ?? null;
if (!$comment_id) {
    header('Location: comment_list.php');
    exit;
}

$stmt = $db->prepare("SELECT c.*, u.username AS logged_username, s.common_name
                      FROM comments c
                      LEFT JOIN users u ON c.user_id = u.user_id
                      LEFT JOIN species s ON c.species_id = s.species_id
                      WHERE c.comment_id = ?");
$stmt->execute([$comment_id]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comment) {
    echo "Comment not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);
    if ($content) {
        $stmt_update = $db->prepare("UPDATE comments SET content = ? WHERE comment_id = ?");
        $stmt_update->execute([$content, $comment_id]);
        header("Location: comment_list.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Edit Comment - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <h1>Edit Comment - Admin</h1>
    <form method="POST" class="comment-edit-form">
        <div class="info">
            <p><strong>Species:</strong> <?= htmlspecialchars($comment['common_name']) ?></p>
            <p><strong>User/Guest:</strong>
                <?= $comment['user_id'] ? htmlspecialchars($comment['logged_username']) : htmlspecialchars($comment['guest_name']) ?>
            </p>
        </div>
        <label for="content">Edit Comment</label>
        <textarea name="content" id="content" rows="4" required><?= htmlspecialchars($comment['content']) ?></textarea>
        <div class="actions">
            <button type="submit">Update Comment</button>
            <a href="comment_list.php" class="back">Cancel</a>
        </div>
    </form>
</body>

</html>
