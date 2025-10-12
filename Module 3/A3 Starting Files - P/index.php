<?php

/*******w******** 
    
    Name:Princepal Singh
    Date:06-10-2025
    Description:Displays the 5 most recent blog posts with title, date, and short excerpts.

****************/

require('connect.php');

require ('functions.php');

$stmt = $db->query("SELECT id, title, content, created_at
                    FROM posts
                    ORDER BY created_at DESC
                    LIMIT 5");
$posts = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Welcome to my Blog!</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
      <header>
    <h1>My Blog</h1>
    <nav>
      <!-- Only authenticated users can access new/edit/delete pages;
           the page itself will prompt for credentials. -->
      <a href="new.php">New Post (admin)</a>
    </nav>
    <hr>
  </header>

  <?php if (!$posts): ?>
    <p>No posts yet. <a href="new.php">Create the first one</a>.</p>
  <?php else: ?>
    <?php foreach ($posts as $p): ?>
      <article>
        <h2><a href="post.php?id=<?= (int)$p['id'] ?>"><?= e($p['title']) ?></a></h2>
        <small>Posted on <?= e($p['created_at']) ?></small>
        <p><?= e(excerpt($p['content'])) ?></p>
        <p><a href="post.php?id=<?= (int)$p['id'] ?>">Read Full Post</a></p>
        <hr>
      </article>
    <?php endforeach; ?>
  <?php endif; ?>
    
</body>
</html>