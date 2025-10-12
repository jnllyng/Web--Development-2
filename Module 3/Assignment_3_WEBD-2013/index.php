<?php

/*******w******** 
    
    Name: Gurshan Singh Sekhon
    Date: October 01, 2025
    Description: Home page displaying 5 most recent blog posts

****************/

require('connect.php');

// Query to get 5 most recent posts in reverse chronological order
$query = "SELECT id, title, content, timestamp FROM posts ORDER BY timestamp DESC LIMIT 5";
$statement = $db->prepare($query);
$statement->execute();
$posts = $statement->fetchAll(PDO::FETCH_ASSOC);

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
    <header class="nav-head">
        <a href="index.php"><img src="blog_logo.png" alt="logo"></a>
        <h1><a href="index.php">My Blog</a></h1>
    </header>
    
    <main>
        <div class="posts-container">
            <div class="post-actions">
                <a href="index.php" class="new-post-link">Home</a>
                <a href="post.php" class="new-post-link">+ New Post</a>
            </div>
            
            <?php if (empty($posts)): ?>
                <p>No blog posts yet. Be the first to create one!</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post-preview">
                        <h2>
                            <a href="post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a>
                        </h2>
                        <p class="post-date">
                            <?= date('F d, Y, h:i a', strtotime($post['timestamp'])) ?>
                        </p>
                        <div class="post-content">
                            <?php
                            $content = htmlspecialchars($post['content']);
                            if (strlen($content) > 200) {
                                echo substr($content, 0, 200) . '...';
                                echo '<br><a href="post.php?id=' . $post['id'] . '" class="read-more">Read Full Post</a>';
                            } else {
                                echo $content;
                            }
                            ?>
                        </div>
                        <div class="post-actions">
                            <a href="edit.php?id=<?= $post['id'] ?>" class="edit-link">Edit</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>