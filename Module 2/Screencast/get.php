<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Working with $_GET</title>
</head>
<body>
    <h2>Contents of the $_GET Superglobal</h2>
    <!-- put each keys in own line -->
    <pre><?php print_r($_GET) ?></pre>
    <p>
        <a href="?happy=true&hidden_monkies=12">Link with Parameters</a>
    </p>
</body>
</html>