<?php
/*******w******** 

    Name: Jueun Yang
    Date: 2025-09-29
    Description: Handles form submissions,
                 Validates user input,
                 Performs the corresponding database operations


****************/
require('connect.php');
require 'authenticate.php';

if (empty($_POST)) {
    header("Location: index.php");
    exit;
}

$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$command = filter_input(INPUT_POST, 'command', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$errors = [];

if (strlen(trim($title)) < 1) {
    $errors[] = "Title must be at least 1 character long.";
}
if (strlen(trim($content)) < 1) {
    $errors[] = "Content must be at least 1 character long.";
}
if (!$errors) {
    if ($command == 'Update') {
        $query = "UPDATE blog_posts SET title = :title, content = :content WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':content', $content);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        header("Location: index.php");
        exit;
    } elseif ($command == 'Delete') {
        $query = "DELETE FROM blog_posts WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        header("Location: index.php");
        exit;
    } else {
        $query = "INSERT INTO blog_posts (title, content) VALUES (:title, :content)";
        $statement = $db->prepare($query);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':content', $content);
        $statement->execute();
        header("Location: index.php");
        exit;
    }
}

$error_message = "An error occured while processing your post.";
$return = "Return Home";
$footer = "Copywrong 2025 - No Rights Reserved";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Error Page</title>
</head>

<body>
    <div id="wrapper">
        <div id="header">
            <h1><a href="index.php">Error Page</a></h1>
        </div>
        <h1><?= $error_message ?></h1>
        <?php foreach ($errors as $error): ?>
            <p class="error"><?= $error ?></p>
        <?php endforeach ?>
        <a href="index.php"><?= $return ?></a>
        <div id="footer">
            <?= $footer ?>
        </div>
    </div>
</body>

</html>