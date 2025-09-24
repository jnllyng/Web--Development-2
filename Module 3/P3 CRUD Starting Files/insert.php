<?php
    //Run database firstly 
    require('db_connect.php');
    
    if ($_POST && !empty($_POST['author']) && !empty($_POST['content'])) {
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        //  Build the parameterized SQL query and bind to the above sanitized values.
        $query = "INSERT INTO quotes (author, content) VALUES (:author, :content)";
        $statement = $db->prepare($query);
        
        //  Bind values to the parameters
        $statement->bindValue(':author', $author);
        $statement->bindValue(':content', $content);
        
        //  Execute the INSERT.
        //  execute() will check for possible SQL injection and remove if necessary
        if($statement->execute()){
            echo "Success";
        }

    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>PDO Insert</title>
    <link rel="stylesheet" type="text/css" href="styles.css" />
</head>
<body>
    <?php include('nav.php'); ?>
    <form method="post" action="insert.php">
        <label for="author">Author</label>
        <input id="author" name="author">
        <label for="content">Content</label>
        <input id="content" name="content">
        <input type="submit">
    </form>
</body>
</html>