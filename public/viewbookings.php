<?php
session_start();
require '../config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ─── RÉCUPÉRER LES RÉSERVATIONS POUR LES TRAJETS DE L'UTILISATEUR ──────────────────────────────────
$bookings = [];
$totalEarnings = 0;

try {
    // Récupérer tous les trajets de l'utilisateur avec leurs réservations
    $stmt = $pdo->prepare("
        SELECT
            r.id as ride_id,
            r.departure,
            r.destination,
            r.date,
            r.price,
            r.seats,
            b.id as booking_id,
            u.name as passenger_name,
            u.email as passenger_email,
            b.created_at as booking_date
        FROM rides r
        JOIN bookings b ON b.ride_id = r.id
        JOIN users u ON u.id = b.user_id
        WHERE r.driver_id = ?
        ORDER BY r.date DESC, b.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll();

    // Calculer les revenus totaux
    foreach ($bookings as $booking) {
        if ($booking['price']) {
            $totalEarnings += $booking['price'];
        }
    }
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des réservations: " . $e->getMessage();
}

// ─── INFO UTILISATEUR ─────────────────────────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Wayshare - View Bookings</title>
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
            <a class=
            "snav-item active" href="viewbookings.php">
            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            View bookings
            </a>
        </div>
        
      </nav>
    </aside>

    <!-- ═══ CONTENT ═══ -->
    <main class="panel-area">
      <div class="panel active">
        <div class="panel-inner">
          <div class="panel-title-row">
            <div class="panel-icon" style="background:#e8f7f7"><svg width="22" height="22" viewBox="0 0 24 24" stroke="#1a9fa0" stroke-width="2" fill="none" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></div>
            <div>
              <h2>Reservations on my trips</h2>
              <p>All your ride bookings and earnings</p>
            </div>
          </div>

          <?php if (isset($error)): ?>
            <div style="background:#fff0f0;border:1.5px solid #e05c5c;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#a83232;display:flex;align-items:center;gap:10px;">
              <svg width="18" height="18" viewBox="0 0 24 24" stroke="#e05c5c" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <?php if (empty($bookings)): ?>
            <div style="text-align:center;padding:60px 20px;color:var(--text-light);">
              <svg width="48" height="48" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none" style="margin-bottom:12px;opacity:0.4"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
              <p>No bookings yet.<br>Create a trip to start receiving reservations.</p>
            </div>
          <?php else: ?>
            <div class="booking-list">
              <?php
              $currentRideId = null;
              foreach ($bookings as $booking):
                // Group bookings by ride
                if ($currentRideId !== $booking['ride_id']):
                  if ($currentRideId !== null): ?>
                    </div>
                  <?php endif; ?>
                  <div class="ride-bookings-group">
                    <div class="ride-header-info" style="padding:16px 0;border-bottom:1px solid var(--border);margin-bottom:12px;">
                      <div style="font-weight:600;color:var(--text);font-size:1.1rem;">
                        <?= htmlspecialchars($booking['departure']) ?> → <?= htmlspecialchars($booking['destination']) ?>
                      </div>
                      <div style="color:var(--text-light);font-size:0.9rem;">
                        <?= htmlspecialchars($booking['date']) ?> · $<?= htmlspecialchars($booking['price']) ?> per seat
                      </div>
                    </div>
                  <?php
                  $currentRideId = $booking['ride_id'];
                endif;
              ?>
              <div class="booking-row">
                <div class="bk-avatar" style="background:<?= ['#7c6fcd','#1a9fa0','#e07a5f','#3d405b','#81b29a'][crc32($booking['passenger_name']) % 5] ?>">
                  <?= strtoupper(substr($booking['passenger_name'], 0, 2)) ?>
                </div>
                <div class="bk-info">
                  <div class="bk-name"><?= htmlspecialchars($booking['passenger_name']) ?></div>
                  <div class="bk-meta">1 seat &nbsp;·&nbsp; Booked <?= date('M j', strtotime($booking['booking_date'])) ?></div>
                </div>
                <div class="bk-status confirmed">Confirmed</div>
                <div class="bk-amount">$<?= htmlspecialchars($booking['price']) ?></div>
              </div>
              <?php endforeach; ?>
            </div>

            <div class="bk-total-row">
              <span>Total earnings</span>
              <strong style="color:var(--teal);font-size:1.1rem">$<?= number_format($totalEarnings, 2) ?></strong>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>

  <script>
    function confirmDelete() {
      if (confirm('Are you sure you want to delete your trip? This action cannot be undone and will cancel all existing reservations.')) {
        // Redirect to delete trip functionality (you can implement this later)
        alert('Trip deletion not implemented yet.');
      }
    }
  </script>
</body>
</html>