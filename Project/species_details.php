<?php
session_start();
require('includes/db_connect.php');

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

$category_id = $_GET['id'] ?? null;
if (!$category_id) {
    echo "Invalid category ID.";
    exit;
}

$stmt = $db->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$category) {
    echo "Species not found.";
    exit;
}

$search_query = $category['common_name'] ?: $category['scientific_name'];
$api_url = "https://en.wikipedia.org/w/api.php?action=query&list=search&srsearch=" . urlencode($search_query) . "&utf8=&format=json";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$response = curl_exec($ch);
curl_close($ch);

$wiki_page = null;
if ($response) {
    $data = json_decode($response, true);
    if (!empty($data['query']['search'][0]['title'])) {
        $wiki_page = $data['query']['search'][0]['title'];
    }
}
$wiki_url = $wiki_page ? "https://en.wikipedia.org/wiki/" . str_replace(' ', '_', $wiki_page) : null;

$stmt_photos = $db->prepare("
    SELECT p.*, u.username 
    FROM photos p 
    JOIN users u ON p.user_id = u.user_id 
    WHERE p.category_id = ? 
    ORDER BY p.upload_date DESC
");
$stmt_photos->execute([$category_id]);
$photos = $stmt_photos->fetchAll(PDO::FETCH_ASSOC);

$stmt_comments = $db->prepare("
    SELECT c.comment_id, c.user_id, c.guest_name, c.content, c.created_at, c.visible, u.username
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.user_id
    WHERE c.category_id = ? 
    ORDER BY c.created_at DESC
");
$stmt_comments->execute([$category_id]);
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

function disemvowel($text)
{
    return preg_replace('/[aeiouAEIOU]/', '*', $text);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($category['common_name'] ?: $category['scientific_name']) ?> Details</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <?php include('includes/header.php'); ?>

    <main class="main-content">
        <div class="grid-container">
            <h1><?= htmlspecialchars($category['common_name'] ?: $category['scientific_name']) ?></h1>

            <h2>Observation Gallery</h2>
            <?php if ($user_id): ?>
                <p><a href="photo_create.php?category_id=<?= $category['category_id'] ?>"><button>Upload</button></a></p>
            <?php else: ?>
                <p><em>Login to upload your observation photos.</em></p>
            <?php endif; ?>

            <div class="gallery">
                <?php if ($photos): ?>
                    <?php foreach ($photos as $photo): ?>
                        <div class="photo-card">
                            <img src="<?= htmlspecialchars($photo['photo_url']) ?>" alt="Observation Photo" width="200">
                            <p>Uploaded by: <?= htmlspecialchars($photo['username']) ?></p>
                            <p>Uploaded on: <?= htmlspecialchars($photo['upload_date']) ?></p>
                            <?php if ($user_id == $photo['user_id']): ?>
                                <p>
                                    <a href="photo_edit.php?id=<?= $photo['photo_id'] ?>">Edit</a> |
                                    <a href="photo_delete.php?id=<?= $photo['photo_id'] ?>"
                                        onclick="return confirm('Delete this photo?')">Delete</a>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No observation photos yet.</p>
                <?php endif; ?>
            </div>

            <h2>About this Species</h2>
            <?php if ($wiki_url): ?>
                <iframe src="<?= $wiki_url ?>" width="100%" height="600px" style="border:1px solid #ccc;"></iframe>
            <?php else: ?>
                <p><em>No Wikipedia page found for this species.</em></p>
            <?php endif; ?>

            <h2>Comments</h2>
            <form method="POST" action="comment_create.php">
                <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>">
                <?php if ($user_id): ?>
                    <p>Commenting as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
                <?php else: ?>
                    <input type="text" name="guest_name" placeholder="Your Name" required><br>
                <?php endif; ?>

                <textarea name="content" rows="3" placeholder="Add your comment..." required></textarea><br>
                <button type="submit">Post Comment</button>
            </form>

            <div class="comments-list">
                <?php if ($comments): ?>
                    <?php foreach ($comments as $comment): ?>
                        <?php
                        if ($role !== 'admin' && !$comment['visible'])
                            continue;

                        $comment_content = $comment['content'];

                        if ($role === 'admin' && isset($_GET['disemvowel']) && $_GET['disemvowel'] == $comment['comment_id']) {
                            $comment_content = disemvowel($comment_content);
                        }
                        ?>
                        <div class="comment" style="<?= !$comment['visible'] ? 'opacity:0.5;' : '' ?>">
                            <p><strong>
                                    <?= $comment['user_id'] ? htmlspecialchars($comment['username']) : htmlspecialchars($comment['guest_name']) ?>
                                </strong> <em><?= htmlspecialchars($comment['created_at']) ?></em></p>

                            <p><?= nl2br(htmlspecialchars($comment_content)) ?></p>

                            <p>
                                <?php if ($user_id == $comment['user_id'] || $role === 'admin'): ?>
                                    <a href="comment_edit.php?id=<?= $comment['comment_id'] ?>">Edit</a> |
                                    <a href="comment_delete.php?id=<?= $comment['comment_id'] ?>"
                                        onclick="return confirm('Delete this comment?')">Delete</a> 
                                <?php endif; ?>

                                <?php if ($role === 'admin'): ?>
                                    <?php if ($comment['visible']): ?>
                                        <a href="admin/comment_hide.php?id=<?= $comment['comment_id'] ?>&action=hide"
                                            onclick="return confirm('Hide this comment?')">| Hide</a> |
                                    <?php else: ?>
                                        <a href="admin/comment_hide.php?id=<?= $comment['comment_id'] ?>&action=unhide"
                                            onclick="return confirm('Unhide this comment?')">Unhide</a> |
                                    <?php endif; ?>
                                    <a href="admin/comment_disemvowel.php?id=<?= $comment['comment_id'] ?>"
                                        onclick="return confirm('Disemvowel this comment?')">Disemvowel</a>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No comments yet.</p>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <?php include('includes/footer.php'); ?>
</body>

</html>