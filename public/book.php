<?php
session_start();
require '../config/db.php';

$ride_id = $_POST['ride_id'];

$stmt = $pdo->prepare("INSERT INTO bookings (user_id, ride_id) VALUES (?, ?)");
$stmt->execute([$_SESSION['user_id'], $ride_id]);

echo "Ride booked!";