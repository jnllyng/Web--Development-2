<?php
require('../includes/db_connect.php');

$username = 'ellyang';
$email = 'jyang37@Academic.rrc.ca';
$password = 'maytheoddsbeeverinyourfavor!';

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'admin')");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->execute();
    echo "Admin Registered.";
} catch (PDOException $e) {
    echo "Failed: " . $e->getMessage();
}
?>
