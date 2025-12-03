<?php
session_start();
require('../includes/db_connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$stmt = $db->prepare("
    SELECT c.comment_id, c.user_id, c.guest_name, c.content, c.created_at, c.visible, u.username, s.common_name
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.user_id
    LEFT JOIN species s ON c.species_id = s.species_id
    ORDER BY c.created_at DESC
");
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Comment List - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <h1>All Comments (Admin)</h1>
    <nav>
        <a href="dashboard.php">Back to Main Dashboard</a>
    </nav>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Species</th>
                <th>User / Guest</th>
                <th>Content</th>
                <th>Created At</th>
                <th>Visible</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($comments): ?>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?= $comment['comment_id'] ?></td>
                        <td><?= htmlspecialchars($comment['common_name']) ?></td>
                        <td>
                            <?= $comment['user_id'] ? htmlspecialchars($comment['username']) : htmlspecialchars($comment['guest_name']) ?>
                        </td>
                        <td><?= nl2br(htmlspecialchars($comment['content'])) ?></td>
                        <td><?= $comment['created_at'] ?></td>
                        <td><?= $comment['visible'] ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="comment_edit.php?id=<?= $comment['comment_id'] ?>">Edit</a> |
                            <a href="comment_delete.php?id=<?= $comment['comment_id'] ?>"
                                onclick="return confirm('Delete this comment?')">Delete</a> |
                            <?php if ($comment['visible']): ?>
                                <a href="comment_hide.php?id=<?= $comment['comment_id'] ?>&action=hide">Hide</a>
                            <?php else: ?>
                                <a href="comment_hide.php?id=<?= $comment['comment_id'] ?>&action=unhide">Unhide</a>
                            <?php endif; ?> |
                            <a href="comment_disemvowel.php?id=<?= $comment['comment_id'] ?>"
                                onclick="return confirm('Disemvowel this comment?')">Disemvowel</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No comments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>
