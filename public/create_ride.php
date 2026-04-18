<?php
session_start();
require '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $departure = $_POST['departure'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];
    $seats = $_POST['seats'];

    $stmt = $pdo->prepare("INSERT INTO rides (driver_id, departure, destination, date, seats) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $departure, $destination, $date, $seats]);

    echo "Ride created!";
}
?>

<form method="POST">
    <input name="departure" placeholder="From"><br>
    <input name="destination" placeholder="To"><br>
    <input type="datetime-local" name="date"><br>
    <input name="seats" type="number"><br>
    <button>Create Ride</button>
</form>