<?php
require '../config/db.php';

$stmt = $pdo->query("SELECT * FROM rides");
$rides = $stmt->fetchAll();

foreach ($rides as $ride) {
    echo $ride['departure'] . " → " . $ride['destination'] . "<br>";
}
?>