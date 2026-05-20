<?php
session_start();
require '../config/db.php';
require '../models/Booking.php';
require '../models/User.php';
require '../models/Avis.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$bookingModel = new Booking($pdo);
$userModel = new User($pdo);
$success = "";
$error = "";


// Handle actions via Booking model
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['cancel_booking_id'])) {
    $res = $bookingModel->cancel((int) $_POST['cancel_booking_id'], $user_id);
    if (!empty($res['success'])) {
      $success = $res['message'] ?? "Réservation annulée avec succès.";
    } else {
      $error = $res['error'] ?? "Impossible d'annuler cette réservation.";
    }
  } elseif (!empty($_POST['rate_booking_id'])) {
    $rating = (int) ($_POST['rating'] ?? 0);
    $res = $bookingModel->rate((int) $_POST['rate_booking_id'], $rating, $user_id);
    if (!empty($res['success'])) {
      $success = $res['message'] ?? "Merci pour votre note !";
    } else {
      $error = $res['error'] ?? "Impossible de noter cette réservation.";
    }
  }
}

// Load data for the view
try {
  $bookings = $bookingModel->getUserBookings($user_id);
} catch (Exception $e) {
  $bookings = [];
  $error = "Erreur lors de la récupération des réservations: " . $e->getMessage();
}

$user = $userModel->findById($user_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Wayshare - My Bookings</title>
    <link rel="stylesheet" href="../assets/style.css">
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

  <div class="app-layout">

    <!-- ═══ SIDEBAR ═══ -->
    <aside class="driver-sidebar">
      <div class="sidebar-header">
        <div class="sidebar-avatar">
            <?= strtoupper(substr($user['name'], 0, 2)) ?>
        </div>
        <div>
          <div class="sidebar-name"><?= htmlspecialchars($user['name']) ?></div>
          <div class="sidebar-role">Driver &amp; Passenger</div>
        </div>
      </div>

      <div class="sidebar-section-label">Panel</div>
      <nav class="sidebar-nav">
        <div class="auth-footer">
            <a class="snav-item" href="findRide.php">
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
            <a class="snav-item active" href="myBookings.php">
            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            My bookings
            </a>
        </div>
      </nav>
    </aside>

    <!-- ═══ CONTENT ═══ -->
    <main class="panel-area">
      <div class="panel active">
        <div class="panel-inner">
          <div class="panel-title-row">
            <div class="panel-icon" style="background:#e8f7f7"><svg width="22" height="22" viewBox="0 0 24 24" stroke="#1a9fa0" stroke-width="2" fill="none" stroke-linecap="round"><path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
            <div>
              <h2>My Bookings</h2>
              <p>All your ride reservations</p>
            </div>
          </div>

          <?php if ($success): ?>
            <div style="background:#e8f7f7;border:1.5px solid var(--teal);border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#1a5050;display:flex;align-items:center;gap:10px;">
              <svg width="18" height="18" viewBox="0 0 24 24" stroke="#1a9fa0" stroke-width="2" fill="none"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <?= htmlspecialchars($success) ?>
            </div>
          <?php endif; ?>
          <?php if ($error): ?>
            <div style="background:#fff0f0;border:1.5px solid #e05c5c;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#a83232;display:flex;align-items:center;gap:10px;">
              <svg width="18" height="18" viewBox="0 0 24 24" stroke="#e05c5c" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <?php if (empty($bookings)): ?>
            <div style="text-align:center;padding:60px 20px;color:var(--text-light);">
              <svg width="48" height="48" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none" style="margin-bottom:12px;opacity:0.4"><path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              <p>No bookings yet.<br>Find a ride and make your first reservation.</p>
            </div>
          <?php else: ?>
            <div class="booking-list">
              <?php foreach ($bookings as $booking): ?>
              <div class="booking-row">
                <div class="bk-avatar" style="background:<?= ['#7c6fcd','#1a9fa0','#e07a5f','#3d405b','#81b29a'][crc32($booking['driver_name']) % 5] ?>">
                  <?= strtoupper(substr($booking['driver_name'], 0, 2)) ?>
                </div>
                <div class="bk-info">
                  <div class="bk-name">
                    <?= htmlspecialchars($booking['departure']) ?> → <?= htmlspecialchars($booking['destination']) ?>
                  </div>
                  <div class="bk-meta">
                    Driver: <?= htmlspecialchars($booking['driver_name']) ?> &nbsp;·&nbsp;
                    <?= htmlspecialchars($booking['date']) ?> at <?= htmlspecialchars($booking['departure_time']) ?> &nbsp;·&nbsp;
                    Booked <?= date('M j', strtotime($booking['booking_date'])) ?>
                  </div>
                  <div class="bk-extra" style="margin-top:8px;font-size:0.9rem;color:var(--text-light);">
                    Places réservées: <?= htmlspecialchars($booking['booked_seats']) ?>
                    <div class="rating-stars" style="margin-top:6px;">
                      <?= ($booking['booking_rating'] > 0)
                          ? str_repeat('★', (int)$booking['booking_rating']) . str_repeat('☆', 5 - (int)$booking['booking_rating'])
                          : '☆☆☆☆☆' ?>
                      <span class="rating-label" style="margin-left:8px;color:var(--text-light);font-size:0.85rem;">
                        <?= ($booking['booking_rating'] > 0) ? htmlspecialchars($booking['booking_rating']) . ' / 5' : 'Pas encore noté' ?>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="bk-status confirmed">Confirmed</div>
                <div class="bk-amount">$<?= htmlspecialchars($booking['price']) ?></div>
              </div>
              <div class="booking-actions" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
                <form method="POST" style="margin:0;">
                  <input type="hidden" name="cancel_booking_id" value="<?= (int)$booking['booking_id'] ?>">
                  <button type="submit" class="btn-book" style="background:#e05c5c;">Annuler</button>
                </form>
                <form method="POST" style="margin:0;display:flex;align-items:center;gap:10px;">
                  <input type="hidden" name="rate_booking_id" value="<?= (int)$booking['booking_id'] ?>">
                  <select name="rating" style="padding:8px 10px;border-radius:10px;border:1px solid #d9e6e9;font-size:0.95rem;">
                    <option value="">Note</option>
                    <?php for ($star = 1; $star <= 5; $star++): ?>
                      <option value="<?= $star ?>" <?= ($booking['booking_rating'] == $star) ? 'selected' : '' ?>><?= $star ?> étoile<?= $star > 1 ? 's' : '' ?></option>
                    <?php endfor; ?>
                  </select>
                  <button type="submit" class="btn-book" style="background:#1a9fa0;">Noter</button>
                </form>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
</body>
</html>