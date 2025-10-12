<?php

/*******w******** 

    Name: Jueun Yang
    Date: 2025-09-29
    Description: Display a full blog post page

****************/

require('connect.php');

$header = "My Blog - ";
$home = 'Home';
$new_post = 'New Post';

$query = "SELECT * FROM blog_posts WHERE id = :id LIMIT 1";
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$statement = $db->prepare($query);
$statement->bindValue(':id', $id, PDO::PARAM_INT);
$statement->execute();
$row = $statement->fetch();
if (!$row) {
    header("Location: index.php");
    exit;
}
$edit = 'edit';
$footer = "Copywrong 2025 - No Rights Reserved";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title><?= $header . $row['title'] ?></title>
</head>

<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <div id="wrapper">
        <div id="header">
            <h1><a href="index.php"><?= $header . $row['title'] ?></a></h1>
        </div>
        <ul id="menu">
            <li><a href="index.php"><?= $home ?></a></li>
            <li><a href="post.php"><?= $new_post ?></a></li>
        </ul>
        <div id="all_blogs">
            <div class="blog_post">
                <h2><?= $row['title'] ?></h2>
                <p>
                    <small>
                        <?= date('F j, Y, g:i a - ', strtotime($row['created_at'])) ?>
                        <a href="edit.php?id=<?= $row['id'] ?>"><?= $edit ?></a>
                    </small>
                </p>
                <div class="blog_content">
                    <?= $row['content'] ?>
                </div>
            </div>
        </div>
        <div id="footer">
            <?= $footer ?>
        </div>
    </div>
</body>

</html>