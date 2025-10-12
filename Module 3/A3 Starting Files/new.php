<?php
require 'authenticate.php';
require 'connect.php';
require 'functions.php';

$errors = [];
$title = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');

  if ($title === '')   { $errors[] = 'Title is required.'; }
  if ($content === '') { $errors[] = 'Content is required.'; }

  if (!$errors) {
    $stmt = $db->prepare("INSERT INTO posts (title, content) VALUES (:t, :c)");
    $stmt->execute([':t' => $title, ':c' => $content]);
    redirect('index.php');
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>New Post</title>
  <link rel="stylesheet" href="main.css">
</head>
<body>
  <h1>New Post</h1>
  <nav><a href="index.php">Back to Home</a></nav>
  <?php if ($errors): ?>
    <div class="errors">
      <ul><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post" action="new.php">
    <label>Title<br>
      <input type="text" name="title" value="<?= e($title) ?>" required>
    </label><br><br>

    <label>Content<br>
      <textarea name="content" rows="10" cols="60" required><?= e($content) ?></textarea>
    </label><br><br>

    <button type="submit">Publish</button>
  </form>
</body>
</html>
