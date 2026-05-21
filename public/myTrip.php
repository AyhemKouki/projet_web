<?php
session_start();

require '../config/db.php';
require '../models/User.php';
require '../models/Ride.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userModel = new User($pdo);
$rideModel = new Ride($pdo);

// Get user info
$user = $userModel->findById($_SESSION['user_id']);
if (!$user) {
    header("Location: login.php");
    exit();
}

// Get user's rides
$rides = $rideModel->getByDriver($_SESSION['user_id']);

// Handle form submission for update
$success = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $ride_id = $_POST['ride_id'];
    $departure = $_POST['departure'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];
    $departure_time = $_POST['departure_time'];
    $seats = $_POST['seats'];
    $price = $_POST['price'];

    $success = $rideModel->update(
        $ride_id,
        $_SESSION['user_id'],
        $departure,
        $destination,
        $date,
        $departure_time,
        $seats,
        $price
    );

    // Refresh rides
    $rides = $rideModel->getByDriver($_SESSION['user_id']);
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $ride_id = $_POST['delete'];
    $rideModel->delete($ride_id, $_SESSION['user_id']);

    // Refresh rides
    $rides = $rideModel->getByDriver($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Wayshare - edit trip</title>
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
        <div class="sidebar-avatar" onclick="window.location.href='editProfile.php'">
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
            <a class="snav-item" href="postRide.php">
            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Post a trip
            </a>
        </div>
        <div class="auth-footer">
            <a class="snav-item active" href="myTrip.php">
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
        <!-- PANEL 3: Edit my trip -->
        <div id="panel-edit" class="panel active">
          <div class="panel-inner">
            <div class="panel-title-row">
              <div class="panel-icon" style="background:#e8f7f7"><svg width="22" height="22" viewBox="0 0 24 24" stroke="#1a9fa0" stroke-width="2" fill="none" stroke-linecap="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></div>
              <div>
                <h2>Edit my trip</h2>
                <p>Update the details of your current trip</p>
              </div>
            </div>
            <?php if (empty($rides)): ?>
              <p>No trips posted yet.</p>
            <?php else: ?>
              <h3>Your Trips</h3>
              <?php foreach ($rides as $ride): ?>
                <?php
                $trip_datetime = strtotime($ride['date'] . ' ' . $ride['departure_time']);
                $is_past = $trip_datetime < time();
                ?>
                <div class="current-trip-card">
                  <div class="ct-label"> <?php if ($is_past): ?><span style="color:red;">(Ended)</span><?php endif; ?></div>
                  <div class="ct-route"><?= htmlspecialchars($ride['departure']) ?> &nbsp;→&nbsp; <?= htmlspecialchars($ride['destination']) ?></div>
                  <div class="ct-meta"><?= date('D, M j', strtotime($ride['date'])) ?> · <?= $ride['departure_time'] ?> &nbsp;·&nbsp; <?= $ride['seats'] ?> seats &nbsp;·&nbsp; $<?= $ride['price'] ?>/seat</div>
                  <div class="form-actions" style="margin-top:10px;">
                    <?php if (!$is_past): ?>
                      <button type="button" class="btn-full" style="max-width:100px; margin-right:10px;" onclick="toggleEdit(<?= $ride['id'] ?>)">Edit</button>
                      <form method="post" style="display:inline;">
                        <button type="submit" name="delete" value="<?= $ride['id'] ?>" class="btn-danger" style="max-width:100px;" onclick="return confirm('Are you sure you want to delete this trip?')">Delete</button>
                      </form>
                    <?php else: ?>
                      <span style="color:gray;">Trip has ended, cannot modify.</span>
                    <?php endif; ?>
                  </div>
                  <?php if (!$is_past): ?>
                  <div id="edit-form-<?= $ride['id'] ?>" style="display:none; margin-top:10px;">
                    <form method="post">
                      <input type="hidden" name="ride_id" value="<?= $ride['id'] ?>">
                      <div class="form-grid">
                        <div class="field-group"><label>Departure city</label><input type="text" name="departure" value="<?= htmlspecialchars($ride['departure']) ?>" required></div>
                        <div class="field-group"><label>Destination</label><input type="text" name="destination" value="<?= htmlspecialchars($ride['destination']) ?>" required></div>
                        <div class="field-group"><label>Date</label><input type="date" name="date" min="<?= date('Y-m-d') ?>" value="<?= $ride['date'] ?>" required></div>
                        <div class="field-group"><label>Departure time</label><input type="time" name="departure_time" min="<?= date('H:i', strtotime($ride['departure_time'])) ?>" value="<?= $ride['departure_time'] ?>" required></div>
                        <div class="field-group"><label>Available seats</label><input type="number" name="seats" min="1" max="7" value="<?= $ride['seats'] ?>" required></div>
                        <div class="field-group"><label>Price per seat ($)</label><input type="number" name="price" min="1" value="<?= $ride['price'] ?>" required></div>
                      </div>
                      <div class="form-actions">
                        <button type="submit" name="update" class="btn-full" style="max-width:120px">Save</button>
                        
                      </div>
                    </form>
                  </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($success): ?>
            <div class="success-msg">
                Trip updated successfully 
            </div>
            <?php endif; ?>
          </div>
        </div>
    </main>
    </div>
<script>
function toggleEdit(rideId) {
    var form = document.getElementById('edit-form-' + rideId);
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>
</body>
</html>