<?php

/*******w******** 
    
    Name:Princepal Singh
    Date:06-10-2025
    Description:Lets authenticated users update the title or content of an existing post.

****************/

require('connect.php');
require('authenticate.php');

require ('functions.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { redirect('index.php'); }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');
  if ($title === '')   { $errors[] = 'Title is required.'; }
  if ($content === '') { $errors[] = 'Content is required.'; }

  if (!$errors) {
    $stmt = $db->prepare("UPDATE posts SET title = :t, content = :c WHERE id = :id");
    $stmt->execute([':t' => $title, ':c' => $content, ':id' => $id]);
    redirect("post.php?id=$id");
  }
}

// Load current row for GET or to re-show form on errors
$stmt = $db->prepare("SELECT id, title, content FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();
if (!$post) { redirect('index.php'); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Edit this Post!</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
      <h1>Edit Post</h1>
  <nav>
    <a href="index.php">Home</a> |
    <a href="post.php?id=<?= (int)$post['id'] ?>">Cancel</a>
  </nav>

  <?php if ($errors): ?>
    <div class="errors">
      <ul><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post" action="edit.php">
    <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
    <label>Title<br>
      <input type="text" name="title" value="<?= e($_POST['title'] ?? $post['title']) ?>" required>
    </label><br><br>

    <label>Content<br>
      <textarea name="content" rows="10" cols="60" required><?= e($_POST['content'] ?? $post['content']) ?></textarea>
    </label><br><br>

    <button type="submit">Save Changes</button>
  </form>
    
</body>
</html>