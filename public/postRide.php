<?php
session_start();
require '../config/db.php';
require '../models/User.php';
require '../models/Ride.php';

$userModel = new User($pdo);
$rideModel = new Ride($pdo);

$user = $userModel->findById($_SESSION['user_id']);
if (!$user) {
    header("Location: login.php");
    exit();
}

$success = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $departure = $_POST['departure'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];
    $departure_time = $_POST['departure_time'];
    $seats = $_POST['seats'];
    $price = $_POST['price'];
    $notes = $_POST['notes'];

    if ($rideModel->create($_SESSION['user_id'], $departure, $destination, $date, $departure_time, $seats, $price, $notes)) {
        $success = true;
    }
    else {
        $error = "Failed to create ride. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Wayshare - post ride</title>
    <link rel="stylesheet" href="../assets/style.css?v=1">
</head>

<body>
    <nav>
        <a class="nav-logo" href="findRide.php">
        <div class="logo-icon"><svg viewBox="0 0 24 24"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3m-4 12H9m10 0h-2m2 0a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2m10 0h-2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div>
        <span>Wayshare</span>
        </a>
        <div class="nav-center"></div>
        <div class="nav-right auth-footer" id="searchNavRight">
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>
    
    <!-- ── MAIN LAYOUT: sidebar + content ── -->
    <div class="app-layout">

    <!-- ═══ LEFT SIDEBAR (Driver) ═══ -->
    <aside class="driver-sidebar">
      <div class="sidebar-header">
        <div class="sidebar-avatar">
            <?= strtoupper(substr($user['name'], 0, 2)) ?>
        </div>
        <div>
          <div class="sidebar-name">
            <?= $user['name'] ?>
          </div>
          <div class="sidebar-role">Driver &amp; Passenger</div>
        </div>
      </div>

      <div class="sidebar-section-label"> Panel</div>
      <nav class="sidebar-nav">
        <div class="auth-footer">
            <a class="snav-item " href="findRide.php">
            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            Find a ride
            </a>
        </div>
        <div class="auth-footer">
            <a class="snav-item active" href="postRide.php">
            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Post a trip
            </a>
        </div>
        <div class="auth-footer">
            <a class="snav-item" href="myTrip.php">
            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit my trip
            </a>
        </div>
        <div class="auth-footer">
            <a class="snav-item" href="viewbookings.php">
            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            View bookings
            
            </a>
        </div>
        <div class="auth-footer">
            <a class="snav-item" href="myBookings.php">
            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            My bookings
            </a>
        </div>
    </aside>
    
    <main class="panel-area">
        <div id="panel-post" >
            <div class="panel-inner">
                <div class="panel-title-row">
                    <div class="panel-icon" style="background:#e8f7f7">
                        <svg width="22" height="22" viewBox="0 0 24 24" stroke="#1a9fa0" stroke-width="2" fill="none" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    </div>
                    <div>
                        <h2>Post a new trip</h2>
                        <p>Fill in the details below to offer your ride</p>
                    </div>
                </div>
                <form method="post">
                    <div class="form-grid">
                        <div class="field-group"><label>Departure city</label><input type="text" name="departure" placeholder="e.g. San Francisco, CA" required></div>
                        <div class="field-group"><label>Destination</label><input type="text" name="destination" placeholder="e.g. Los Angeles, CA" required></div>
                        <div class="field-group"><label>Date</label><input type="date" name="date" min="<?= date('Y-m-d') ?>" required></div>
                        <div class="field-group"><label>Departure time</label><input type="time" name="departure_time"  value="08:00" required></div>
                        <div class="field-group"><label>Available seats</label><input type="number" name="seats" min="1" max="7" value="3" required></div>
                        <div class="field-group"><label>Price per seat ($)</label><input type="number" name="price" min="1" value="25" required></div>
                    </div>
                    <div class="field-group" style="margin-top:4px"><label>Notes for passengers (optional)</label><input type="text" name="notes" placeholder="e.g. No smoking, luggage OK, pet-friendly..."></div>
                    <div class="form-actions">
                        <button type="submit" class="btn-full" style="max-width:220px">Publish trip</button>
                    </div>
                </form>
                <?php if ($success): ?>
                <div class="success-msg">
                    Trip published successfully 
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    </div>
</body>