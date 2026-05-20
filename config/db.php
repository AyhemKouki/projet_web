<?php
$host = 'localhost';
$dbname = 'projet';
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>