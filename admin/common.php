<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../models/Admin.php';
require __DIR__ . '/../models/User.php';
require __DIR__ . '/../models/Ride.php';
require __DIR__ . '/../models/Booking.php';
require __DIR__ . '/../models/Avis.php';

$adminModel = new Admin($pdo);
if (!$adminModel->isLogged()) {
    header('Location: login.php');
    exit();
}

$adminName = htmlspecialchars($adminModel->getName(), ENT_QUOTES, 'UTF-8');
$adminEmail = htmlspecialchars($adminModel->getEmail(), ENT_QUOTES, 'UTF-8');
$adminPhone = htmlspecialchars($_SESSION['admin_phone'] ?? '', ENT_QUOTES, 'UTF-8');

