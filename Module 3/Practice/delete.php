<?php 
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $query = "DELETE FROM quotes WHERE id=:id";
    $statement = $db -> prepare($query);
    $statement -> bindValue(':id', $id, PDO::PARAM_INT);
    $stataement -> execute();
?>