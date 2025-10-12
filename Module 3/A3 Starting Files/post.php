<?php

/*******w******** 

    Name: Jueun Yang
    Date: 2025-09-29
    Description: Display the form to create a new blog post.

****************/

require('connect.php');
require 'authenticate.php';

$header = "My Blog - ";
$home = 'Home';
$new_post = 'New Post';
$blog = 'New Blog Post';
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
    <title>My Blog - Post a New Blog</title>
</head>

<body>
    <div id="wrapper">
        <div id="header">
            <h1><a href="index.php"><?= $header . $new_post ?></a></h1>
        </div>
        <ul id="menu">
            <li><a href="index.php"><?= $home ?></a></li>
            <li><a href="post.php" class='active'><?= $new_post ?></a></li>
        </ul>
        <div id="all_blogs">
            <form action="process_post.php" method="post">
                <fieldset>
                    <legend><?= $blog ?></legend>
                    <p>
                        <label for="title"><?= $title_label ?></label>
                        <input name="title" id="title" value="" />
                    </p>
                    <p>
                        <label for="content"><?= $content_label ?></label>
                        <textarea name="content" id="content"></textarea>
                    </p>
                    <p><input type="submit" name="command" value="Create" /></p>
                </fieldset>
            </form>
        </div>
        <div id="footer"><?= $footer ?></div>
    </div>
</body>

</html>