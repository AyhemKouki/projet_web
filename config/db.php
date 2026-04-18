<?php
// PDO est une classe PHP pour accéder aux bases de données
$pdo = new PDO("sqlite:" . __DIR__ . "/../database.sqlite");
// gestion des erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Enable foreign keys
$pdo->exec("PRAGMA foreign_keys = ON;");
?>