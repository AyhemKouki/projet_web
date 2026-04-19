<?php
require 'config/db.php';

$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    email TEXT UNIQUE,
    password TEXT
);

CREATE TABLE IF NOT EXISTS rides (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    driver_id INTEGER,
    departure TEXT,
    destination TEXT,
    date TEXT,
    departure_time TEXT,
    seats INTEGER,
    price REAL,
    notes TEXT,
    FOREIGN KEY(driver_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    ride_id INTEGER,
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(ride_id) REFERENCES rides(id)
);
");

echo "Database initialized!";