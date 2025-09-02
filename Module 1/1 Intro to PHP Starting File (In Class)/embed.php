<?php
$animals = ["cat", "dog", "human", "lion", "tiger", "bear"];


?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>EMbed PHP in HTML</title>
</head>
<body>
    <h1>Animals in a zone of danger</h1>
    <ol>
        <?php foreach($animals as $animal):?>
        <li><?= $animal ?></li> <!-- short echo -->
        <?php endforeach ?>
    </ol>
</body>
</html>