<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../models/Admin.php';

$adminModel = new Admin($pdo);
$adminModel->logout();

header('Location: login.php');
exit();
?>