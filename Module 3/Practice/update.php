<?php 
    $author = filter_input(INPUT_POST,'author', FILTER_SANITIZE_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "UPDATE quotes SET author = :author, content= :content WHERE id= :id";
    $statement = $db -> prepare($query);

    $statement ->bindValue(':author', $author);
    $statement ->bindValue(':content', $content);
    $statement ->bindValue(':id', $id, PDO::PARAM_INT);
    
    $statement ->execute();
?>