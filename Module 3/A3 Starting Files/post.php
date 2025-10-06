<?php

/*******w******** 

    Name: Jueun Yang
    Date: 2025-09-29
    Description:

****************/

require('connect.php');
require 'authenticate.php';

$header = "Stung Eye - ";
$home = 'Home';
$new_post = 'New Post';

if ($_POST) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $title_error = '';
    $content_error = '';
    if (strlen($title) < 1) {
        $title_error = "Title must be at least 1 character long.";
    } 
    if(strlen($content) < 1) {
        $content_error = "Content must be at least 1 character long.";
    }
    if(empty($title_error) && empty($content_error)) {
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        //  Build the parameterized SQL query and bind to the above sanitized values.
        $query = "INSERT INTO blog_posts (title, content) VALUES (:title, :content)";
        $statement = $db->prepare($query);

        //  Bind values to the parameters
        $statement->bindValue(':title', $title);
        $statement->bindValue(':content', $content);

        //  Execute the INSERT.
        //  execute() will check for possible SQL injection and remove if necessary
        if ($statement->execute()) {
           header("Location: index.php");
           exit;
        } 
    }
}


$blog = 'New Blog Post';
$title = 'Title';
$content = 'Content';
$footer = "Copywrong 2025 - No Rights Reserved";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>My Blog - Post a New Blog</title>
</head>

<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <div id="wrapper">
        <div id="header">
            <h1><a href="index.php"><?= $header . $new_post ?></a></h1>
        </div>
        <ul id="menu">
            <li><a href="index.php"><?= $home ?></a></li>
            <li><a href="post.php" class='active'><?= $new_post ?></a></li>
        </ul>
        <div id="all_blogs">
            <form action="post.php" method="post">
                <fieldset>
                    <legend><?= $blog ?></legend>
                    <p>
                        <label for="title"><?= $title ?></label>
                        <input name="title" id="title" />
                        <?php if (!empty($title_error)): ?>
                        <p class="error"><?= $title_error ?></p>
                        <?php endif ?>
                    </p>
                    <p>
                        <label for="content"><?= $content ?></label>
                        <textarea name="content" id="content"></textarea>
                        <?php if(!empty($content_error)): ?>
                        <p class="error"><?= $content_error ?></p>
                        <?php endif ?>
                    </p>
                    <p>
                        <input type="submit" name="command" value="Create" />
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