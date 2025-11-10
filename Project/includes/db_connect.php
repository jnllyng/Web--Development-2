<?php
define('DB_DSN', 'mysql:host=localhost;dbname=manitoba-nature-archive;charset=utf8');
define('DB_USER', 'ellyang');
define('DB_PASS', 'maytheoddsbeeverinyourfavor!');

try {
    $db = new PDO(DB_DSN, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
?>
