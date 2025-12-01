<?php
session_start();
require('includes/db_connect.php');

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

$species_id = $_GET['id'] ?? null;
if (!$species_id || !ctype_digit((string)$species_id)) {
    echo "Invalid species ID.";
    exit;
}

$stmt = $db->prepare("\n    SELECT s.*, c.name AS category_name\n    FROM species s\n    LEFT JOIN categories c ON s.category_id = c.category_id\n    WHERE s.species_id = ?\n");
$stmt->execute([$species_id]);
$species = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$species) {
    echo "Species not found.";
    exit;
}

$search_query = $species['common_name'] ?: $species['scientific_name'];
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

$stmt_photos = $db->prepare("\n    SELECT p.*, u.username \n    FROM photos p \n    JOIN users u ON p.user_id = u.user_id \n    WHERE p.species_id = ? \n    ORDER BY p.upload_date DESC\n");
$stmt_photos->execute([$species_id]);
$photos = $stmt_photos->fetchAll(PDO::FETCH_ASSOC);

$stmt_comments = $db->prepare("\n    SELECT c.comment_id, c.user_id, c.guest_name, c.content, c.created_at, c.visible, u.username\n    FROM comments c\n    LEFT JOIN users u ON c.user_id = u.user_id\n    WHERE c.species_id = ? \n    ORDER BY c.created_at DESC\n");
$stmt_comments->execute([$species_id]);
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

$comment_error = $_SESSION['comment_error'] ?? '';
$comment_content_prefill = $_SESSION['comment_content'] ?? '';
$comment_guest_prefill = $_SESSION['comment_guest_name'] ?? '';
unset($_SESSION['comment_error'], $_SESSION['comment_content'], $_SESSION['comment_guest_name']);

function disemvowel($text)
{
    return preg_replace('/[aeiouAEIOU]/', '*', $text);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($species['common_name'] ?: $species['scientific_name']) ?> Details</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
<?php include('includes/header.php'); ?>

<main class="main-content">
    <div class="grid-container species-details">

        <h1><?= htmlspecialchars($species['common_name'] ?: $species['scientific_name']) ?></h1>
        <div class="species-meta">
            <p><strong>Scientific Name:</strong> <?= htmlspecialchars($species['scientific_name']) ?></p>
            <p><strong>Family:</strong> <?= htmlspecialchars($species['family']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($species['status']) ?></p>
            <p><strong>Category:</strong> <?= htmlspecialchars($species['category_name'] ?? 'None') ?></p>
        </div>

        <?php if ($user_id == $species['user_id'] || $role === 'admin'): ?>
            <div class="action-links">
                <a href="species_edit.php?id=<?= $species['species_id'] ?>" class="edit-btn">Edit</a>
                <a href="species_delete.php?id=<?= $species['species_id'] ?>" class="delete-btn"
                   onclick="return confirm('Delete this species?');">Delete</a>
            </div>
        <?php endif; ?>

        <h2>Observation Gallery</h2>
        <?php if ($user_id): ?>
            <p><a href="photo_create.php?species_id=<?= $species['species_id'] ?>" class="btn-action">Upload</a></p>
        <?php else: ?>
            <p><em>Login to upload photos.</em></p>
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
            <p><em>No Wikipedia page found.</em></p>
        <?php endif; ?>

        <h2>Comments</h2>
        <?php if ($comment_error): ?>
            <p class="form-error"><?= htmlspecialchars($comment_error) ?></p>
        <?php endif; ?>
        <form method="POST" action="comment_create.php" class="comment-form">
            <input type="hidden" name="species_id" value="<?= $species['species_id'] ?>">

            <?php if ($user_id): ?>
                <p>Commenting as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
            <?php else: ?>
                <input type="text" name="guest_name" placeholder="Your Name" value="<?= htmlspecialchars($comment_guest_prefill) ?>" required><br>
            <?php endif; ?>

            <textarea name="content" rows="3" placeholder="Add your comment..." required><?= htmlspecialchars($comment_content_prefill) ?></textarea><br>
            <div class="captcha-block">
                <img id="comment-captcha" src="captcha.php" alt="CAPTCHA" style="display:block; margin-bottom:8px;">
                <button type="button" onclick="document.getElementById('comment-captcha').src='captcha.php?ts=' + Date.now();">Refresh CAPTCHA</button>
            </div>
            <input type="text" name="captcha" placeholder="Enter the text from the image" required>
            <button type="submit" class="btn-action">Post Comment</button>
        </form>

        <div class="comments-list">
            <?php if ($comments): ?>
                <?php foreach ($comments as $comment): ?>
                    <?php
                    if ($role !== 'admin' && !$comment['visible'])
                        continue;

                    $comment_content = $comment['content'];
                    ?>
                    <div class="comment" style="<?= !$comment['visible'] ? 'opacity:0.5;' : '' ?>">
                        <p><strong>
                                <?= $comment['user_id']
                                    ? htmlspecialchars($comment['username'])
                                    : htmlspecialchars($comment['guest_name']) ?>
                            </strong>
                            <em><?= htmlspecialchars($comment['created_at']) ?></em></p>

                        <p><?= nl2br(htmlspecialchars($comment_content)) ?></p>

                        <p>
                            <?php if ($user_id == $comment['user_id'] || $role === 'admin'): ?>
                                <a href="comment_edit.php?id=<?= $comment['comment_id'] ?>">Edit</a> |
                                <a href="comment_delete.php?id=<?= $comment['comment_id'] ?>"
                                   onclick="return confirm('Delete this comment?')">Delete</a>
                            <?php endif; ?>

                            <?php if ($role === 'admin'): ?>
                                <?php if ($comment['visible']): ?>
                                    | <a href="admin/comment_hide.php?id=<?= $comment['comment_id'] ?>&action=hide"
                                         onclick="return confirm('Hide this comment?')">Hide</a>
                                <?php else: ?>
                                    | <a href="admin/comment_hide.php?id=<?= $comment['comment_id'] ?>&action=unhide"
                                         onclick="return confirm('Unhide this comment?')">Unhide</a>
                                <?php endif; ?>

                                | <a href="admin/comment_disemvowel.php?id=<?= $comment['comment_id'] ?>"
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
