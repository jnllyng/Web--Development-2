<?php

/*******w******** 
    
    Name:Princepal Singh
    Date:06-10-2025
    Description:Shows the full content of a single blog post when the user clicks on a title.

****************/

require('connect.php');

require ('functions.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { redirect('index.php'); }

$stmt = $db->prepare("SELECT id, title, content, created_at, updated_at
                      FROM posts WHERE id = :id");
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
    <title>My Blog Post!</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
     
  <header>
    <h1><?= e($post['title']) ?></h1>
    <small>Posted on <?= e($post['created_at']) ?>
      <?php if ($post['updated_at']): ?>
        • Updated <?= e($post['updated_at']) ?>
      <?php endif; ?>
    </small>
    <nav>
      <a href="index.php">Back to Home</a> |
      <a href="edit.php?id=<?= (int)$post['id'] ?>">Edit (admin)</a> |
      <a href="delete.php?id=<?= (int)$post['id'] ?>">Delete (admin)</a>
    </nav>
    <hr>
  </header>

  <main>
    <div><?= nl2br(e($post['content'])) ?></div>
  </main>
    
</body>
</html>