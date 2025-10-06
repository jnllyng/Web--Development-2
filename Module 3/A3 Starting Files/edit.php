<?php

/*******w******** 

    Name: Jueun Yang
    Date: 2025-09-29
    Description:

****************/

require('connect.php');
require('authenticate.php');
$header = "Stung Eye - Edit Post";
$home = 'Home';
$new_post = 'New Post';
$edit_blog = 'Edit Blog Post';
$title = 'Title';
$content = 'Content';

if ($_POST && isset($_POST['id'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $command = filter_input(INPUT_POST, 'command', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($command === 'Delete') {
        $query = "DELETE FROM blog_posts WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        header("Location: index.php");
        exit;
    }

    if ($command === 'Update') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (strlen($title) < 1) {
            $title_error = "Title must be at least 1 character long.";
        }
        if (strlen($content) < 1) {
            $content_error = "Content must be at least 1 character long.";
        }

        if (empty($title_error) && empty($content_error)) {
            $query = "UPDATE blog_posts SET title = :title, content = :content WHERE id = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(':title', $title);
            $statement->bindValue(':content', $content);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();

            header("Location: index.php");
            exit;
        } else {
            $query = "SELECT * FROM blog_posts WHERE id = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $blog_posts = $statement->fetch();
        }
    }
}

elseif (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM blog_posts WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $blog_posts = $statement->fetch();
}

if (!$blog_posts) {
    $error = 'Post Not Found.';
}

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
            <?php if ($id): ?>
                <form action="edit.php" method="post">
                    <fieldset>
                        <legend><?= $edit_blog ?></legend>
                        <p>
                            <label for="title"><?= $title ?></label>
                            <input name="title" id="title" value="<?= $blog_posts['title'] ?>" />
                            <?php if (!empty($title_error)): ?>
                            <p class="error"><?= $title_error ?></p>
                        <?php endif ?>
                        </p>
                        <p>
                            <label for="content"><?= $content ?></label>
                            <textarea name="content" id="content"><?= $blog_posts['content'] ?></textarea>
                            <?php if (!empty($content_error)): ?>
                            <p class="error"><?= $content_error ?></p>
                        <?php endif ?>
                        </p>
                        <p>
                            <input type="hidden" name="id" value="<?= $blog_posts['id'] ?>" />
                            <input type="submit" name="command" value="Update" />
                            <input type="submit" name="command" value="Delete"
                                onclick="return confirm('Are you sure you wish to delete this post?')" />
                        </p>
                    </fieldset>
                </form>
            <?php else: ?>
                <p><?= $error ?></p>
            <?php endif ?>
        </div>
        <div id="footer">
            <?= $footer ?>
        </div>
    </div>
</body>

</html>