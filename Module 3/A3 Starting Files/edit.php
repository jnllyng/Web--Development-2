<?php

/*******w******** 

    Name: Jueun Yang
    Date: 2025-09-29
    Description: Edit an existing blog post

****************/
require('connect.php');
require 'authenticate.php';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$query = "SELECT * FROM blog_posts WHERE id = :id";
$statement = $db->prepare($query);
$statement->bindValue(':id', $id, PDO::PARAM_INT);
$statement->execute();
$post = $statement->fetch();

if (!$post) {
    header("Location: index.php");
    exit;
}

$header = "My Blog - " . $post['title'];
$home = 'Home';
$new_post = 'New Post';
$edit_blog = 'Edit Blog Post';
$title_label = 'Title';
$content_label = 'Content';
$footer = "Copywrong 2025 - No Rights Reserved";
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
    <div id="wrapper">
        <div id="header">
            <h1><a href="index.php"><?= $header ?></a></h1>
        </div>

        <ul id="menu">
            <li><a href="index.php"><?= $home ?></a></li>
            <li><a href="post.php"><?= $new_post ?></a></li>
        </ul>
        <div id="all_blogs">
            <form action="process_post.php" method="post">
                <fieldset>
                    <legend><?= $edit_blog ?></legend>
                    <p>
                        <label for="title"><?= $title_label ?></label>
                        <input name="title" id="title"
                            value="<?= $post['title'] ?>" />
                    </p>
                    <p>
                        <label for="content"><?= $content_label ?></label>
                        <textarea name="content" id="content"><?= $post['content'] ?></textarea>
                    </p>
                    <p>
                        <input type="hidden" name="id" value="<?= $post['id'] ?>" />
                        <input type="submit" name="command" value="Update" />
                        <input type="submit" name="command" value="Delete"
                            onclick="return confirm('Are you sure you wish to delete this post?')" />
                    </p>
                </fieldset>
            </form>
        </div>
        <div id="footer">
            <?= $footer ?>
        </div>
    </div>
</body>

</html>