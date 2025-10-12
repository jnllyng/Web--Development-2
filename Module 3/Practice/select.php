<?php 
    $query = "SELECT * FROM quotes";
    $statement = $db->prepare($query); 
    $statement->execute();
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Found <?= $statement -> rowCount() ?> Rows</h1>
    <ul>
        <?php while ($row = $statement -> fetch()): ?>
            <li><?= $row['author']?> said <?= $row['content']?></li>
        <?php endwhile?>
        </ul>
</body>
</html>