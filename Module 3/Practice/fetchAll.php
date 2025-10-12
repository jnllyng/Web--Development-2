<?php 
    $query = "SELECT * FROM quotes";
    $statement = $db ->prepare($query);
    $statement -> execute();
    $quotes = $statement -> fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <ul>
        <?php foreach ($quotes as $quote): ?>
            <li><?= $quote['author'] ?> said <?= $quote['content']?></li>
        <?php endforeach ?>
    </ul>
</body>
</html>