<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manitoba Nature Archive</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <header class="main-header">
        <div class="grid-container">
            <a href="index.php">Manitoba Nature Archive</a>
        </div>
        <nav class="main-menu">
            <ul class="main-menu-list">
                <li class="main-menu-item"><a href="animal.php">Animal</a></li>
                <li class="main-menu-item"><a href="plant.php">Plant</a></li>
                <li class="main-menu-item"><a href="insect.php">Insect</a></li>
                <li class="main-menu-item">
                    <form method="get" action="search.php" class="sitesearch-form">
                        <label class="sr-only" for="sitesearch">Search</label>
                        <input id="sitesearch" class="sitesearch-input" type="text" name="q" placeholder="Search">
                    </form>
                </li>
            </ul>
        </nav>
    </header>
</body>

</html>