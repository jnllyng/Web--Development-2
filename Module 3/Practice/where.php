<?php
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
// Build a query using ":id" as a placeholder parameter
$query = "SELECT * FROM quotes WHERE id = :id";
// where clause can be added to UPDATE and DELETE statements
// user data should not be trusted. Do not put id = {$_GET['id']}. This might cause SQL Injection

//PDO::prepare function returns a PDOStatement object
$statement = $db->prepare($query);
// Bind the :id parameter in the query to the previously
//sanitized $id specitifying a type of INT
$statement -> bindValue(':id', PDO::PARAM_INT);
$statement->execute();

?>