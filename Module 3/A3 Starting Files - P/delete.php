<?php
require 'authenticate.php';
require 'connect.php';
require 'functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { redirect('index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
  $stmt = $db->prepare("DELETE FROM posts WHERE id = :id");
  $stmt->execute([':id' => $id]);
  redirect('index.php');
}

// fetch for title display
$stmt = $db->prepare("SELECT id, title FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();
if (!$post) { redirect('index.php'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Delete Post</title>
  <link rel="stylesheet" href="main.css">
</head>
<body>
  <h1>Delete Post</h1>
  <p>Are you sure you want to delete: <strong><?= e($post['title']) ?></strong>?</p>
  <form method="post" action="delete.php">
    <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
    <button type="submit" name="confirm" value="yes">Yes, delete it</button>
    <a href="post.php?id=<?= (int)$post['id'] ?>">Cancel</a>
  </form>
</body>
</html>
