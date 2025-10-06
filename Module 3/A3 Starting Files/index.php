<?php

/*******w******** 

    Name: Jueun Yang
    Date: 2025-09-29
    Description:

****************/

require('connect.php');

$header = "Stung Eye - Index";
$home = 'Home';
$new_post = 'New Post';
$query = "SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 5";
$statement = $db->prepare($query);
$statement->execute();
$edit = 'edit';
$excerpt = 'Read more';
$footer = "Copywrong 2025 - No Rights Reserved";
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
    <div id="wrapper">
        <div id="header">
            <h1><a href="index.php"><?= $header ?></a></h1>
        </div>
        <ul id="menu">
            <li><a href="index.php" class='active'><?= $home ?></a></li>
            <li><a href="post.php"><?= $new_post ?></a></li>
        </ul>
        <div id="all_blogs">
            <?php while ($row = $statement->fetch()): ?>
                <div class="blog_post">
                    <h2><a href="show.php?id=<?= $row['id'] ?>"><?= $row['title'] ?></a></h2>
                    <p>
                        <small>
                            <?= date('F j, Y, g:i a - ', strtotime($row['created_at'])) ?>
                            <a href="edit.php?id=<?= $row['id'] ?>"><?= $edit ?></a>
                        </small>
                    </p>
                    <div class="blog_content">
                        <?php if (strlen($row['content']) > 200): ?>
                            <?= substr($row['content'], 0, 200) . " ..." ?>
                            <a href="show.php?id=<?= $row['id'] ?>"><?= $excerpt ?></a>
                        <?php else: ?>
                            <?= $row['content'] ?>
                        <?php endif ?>
                    </div>
                </div>
            <?php endwhile ?>
        </div>
    </div>
    <div id="footer">
        <?= $footer ?>
    </div>

</body>

</html>