<?php 
    $query = "INSERT INTO quotes (author, content) VALUES (:author, :content)";
    $statement = $db -> prepare($query);
    $statement -> execute ();
?>
