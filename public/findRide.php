<?php
session_start();
require '../config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";

// Ensure bookings table has rating column for driver reviews
try {
    $columns = [];
    $colStmt = $pdo->query("PRAGMA table_info(bookings)");
    foreach ($colStmt->fetchAll() as $column) {
        $columns[] = $column['name'];
    }
    if (!in_array('rating', $columns, true)) {
        $pdo->exec("ALTER TABLE bookings ADD COLUMN rating INTEGER DEFAULT NULL");
    }
} catch (Exception $e) {
    // ignore if schema update fails
}

// ─── RÉSERVATION (POST) ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_ride_id'])) {

    $ride_id = (int) $_POST['book_ride_id'];
    $requested_seats = max(1, (int) ($_POST['book_seat_count'] ?? 1));

    // Vérifier que le ride existe et a des places
    $stmt = $pdo->prepare("SELECT r.*, u.name AS driver_name FROM rides r JOIN users u ON u.id = r.driver_id WHERE r.id = ?");
    $stmt->execute([$ride_id]);
    $ride = $stmt->fetch();

    if (!$ride) {
        $error = "Ce trajet n'existe pas.";

    } elseif ($ride['driver_id'] == $user_id) {
        $error = "Vous ne pouvez pas réserver votre propre trajet.";

    } elseif ($requested_seats < 1) {
        $error = "Veuillez choisir au moins une place.";

    } elseif ($ride['seats'] <= 0) {
        $error = "Plus de places disponibles.";

    } elseif ($requested_seats > $ride['seats']) {
        $error = "Le nombre de places demandé est supérieur aux places disponibles.";

    } else {
        // Vérifier si déjà réservé
        $stmt = $pdo->prepare("SELECT id FROM bookings WHERE user_id = ? AND ride_id = ?");
        $stmt->execute([$user_id, $ride_id]);

        if ($stmt->rowCount() > 0) {
            $error = "Vous avez déjà réservé ce trajet.";
        } else {
            // Ajouter la colonne seats si elle n'existe pas encore
            $columns = [];
            $colStmt = $pdo->query("PRAGMA table_info(bookings)");
            foreach ($colStmt->fetchAll() as $column) {
                $columns[] = $column['name'];
            }
            if (!in_array('seats', $columns, true)) {
                $pdo->exec("ALTER TABLE bookings ADD COLUMN seats INTEGER DEFAULT 1;");
            }

            // Insérer la réservation avec le nombre de places réservées
            $stmt = $pdo->prepare("INSERT INTO bookings (user_id, ride_id, seats) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $ride_id, $requested_seats]);

            // Décrémenter le nombre de places
            $pdo->prepare("UPDATE rides SET seats = seats - ? WHERE id = ?")->execute([$requested_seats, $ride_id]);

            $success = "Réservation confirmée pour " . $requested_seats . " place" . ($requested_seats > 1 ? 's' : '') . " sur le trajet " . htmlspecialchars($ride['departure']) . " → " . htmlspecialchars($ride['destination']) . " !";
        }
    }
}

// ─── RECHERCHE (GET) ──────────────────────────────────────────────────────────
$rides       = [];
$searched    = false;
$from        = trim($_GET['from']  ?? '');
$to          = trim($_GET['to']    ?? '');
$date        = trim($_GET['date']  ?? '');

$searched = ($from !== '' || $to !== '' || $date !== '');

$sql = "
    SELECT
        r.*,
        u.name AS driver_name,
        COALESCE(
            (
                SELECT ROUND(AVG(b.rating), 1)
                FROM bookings b
                JOIN rides r2 ON b.ride_id = r2.id
                WHERE b.rating IS NOT NULL
                  AND r2.driver_id = r.driver_id
            ), 0
        ) AS driver_rating,
        COALESCE(
            (
                SELECT COUNT(b.rating)
                FROM bookings b
                JOIN rides r2 ON b.ride_id = r2.id
                WHERE b.rating IS NOT NULL
                  AND r2.driver_id = r.driver_id
            ), 0
        ) AS driver_rating_count
    FROM rides r
    JOIN users u ON u.id = r.driver_id
    WHERE 1=1
";
$params = [];

if ($from !== '') {
    $sql .= " AND r.departure LIKE ?";
    $params[] = "%$from%";
}
if ($to !== '') {
    $sql .= " AND r.destination LIKE ?";
    $params[] = "%$to%";
}
if ($date !== '') {
    $sql .= " AND r.date = ?";
    $params[] = $date;
}

$sql .= " AND r.seats > 0 ORDER BY r.date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rides = $stmt->fetchAll();

// ─── INFO UTILISATEUR ─────────────────────────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Wayshare - Find ride</title>
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
            <a class="snav-item active" href="findRide.php">
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
      </nav>
    </aside>

    <!-- ═══ CONTENT ═══ -->
    <main class="panel-area">
      <div id="panel-search" class="panel active">

        <!-- Messages -->
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

        <!-- Barre de recherche -->
        <div class="search-hero">
          <h1>Find your next ride</h1>
          <form method="GET" action="findRide.php">
            <div class="search-bar">
              <div class="sb-field">
                <label>
                  <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  From
                </label>
                <input type="text" name="from" placeholder="City of departure" value="<?= htmlspecialchars($from) ?>">
              </div>
              <div class="sb-field">
                <label>
                  <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  To
                </label>
                <input type="text" name="to" placeholder="Destination" value="<?= htmlspecialchars($to) ?>">
              </div>
              <div class="sb-field">
                <label>
                  <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  Date
                </label>
                <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
              </div>
              <button type="submit" class="sb-btn">
                <svg viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                Search
              </button>
            </div>
          </form>
        </div>

        <!-- Résultats -->
        <div class="results">
          <div class="results-count"><?= count($rides) ?> ride<?= count($rides) !== 1 ? 's' : '' ?> found</div>

          <?php if (empty($rides)): ?>
            <div style="text-align:center;padding:60px 20px;color:var(--text-light);">
              <svg width="48" height="48" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none" style="margin-bottom:12px;opacity:0.4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
              <p>Aucun trajet disponible pour ces critères.<br>Les trajets que vous avez publiés ne sont pas affichés ici.</p>
            </div>
          <?php else: ?>
          <div class="rides-grid">
            <?php foreach ($rides as $i => $ride):
              // Initiales du conducteur
              $initials = strtoupper(substr($ride['driver_name'], 0, 2));
              // Couleur avatar déterministe
              $colors   = ['#7c6fcd','#1a9fa0','#e07a5f','#3d405b','#81b29a'];
              $color    = $colors[crc32($ride['driver_name']) % count($colors)];
              $delay    = 0.05 * ($i + 1);
              // Vérifier si déjà réservé par cet utilisateur
              $stmtChk = $pdo->prepare("SELECT id FROM bookings WHERE user_id = ? AND ride_id = ?");
              $stmtChk->execute([$user_id, $ride['id']]);
              $alreadyBooked = $stmtChk->rowCount() > 0;
              $isOwner       = $ride['driver_id'] == $user_id;
            ?>
            <div class="ride-card" style="animation-delay:<?= $delay ?>s">
              <div class="ride-header">
                <div class="ride-date">
                  <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  <?= htmlspecialchars($ride['date']) ?>
                </div>
                <?php if (!empty($ride['price'])): ?>
                <div class="ride-price"><strong>$<?= htmlspecialchars($ride['price']) ?></strong><small>per seat</small></div>
                <?php endif; ?>
              </div>

              <div class="ride-route">
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span><?= htmlspecialchars($ride['departure']) ?></span>
                <span style="color:var(--text-light)">→</span>
                <span class="arr"><?= htmlspecialchars($ride['destination']) ?></span>
              </div>

              <div class="ride-footer">
                <div class="driver-info">
                  <div class="avatar" style="background:<?= $color ?>"><?= $initials ?></div>
                  <div>
                    <div class="driver-name"><?= htmlspecialchars($ride['driver_name']) ?></div>
                    <div class="driver-rating" style="font-size:0.85rem;color:var(--text-light);margin-top:3px;">
                      <?= $ride['driver_rating_count'] > 0 ? sprintf('%.1f ★ (%d)', $ride['driver_rating'], $ride['driver_rating_count']) : 'Aucune note encore' ?>
                    </div>
                  </div>
                </div>
                <div class="seats">
                  <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/></svg>
                  <?= (int)$ride['seats'] ?> seat<?= $ride['seats'] > 1 ? 's' : '' ?> left
                </div>
              </div>

              <!-- Bouton réservation via POST -->
              <?php if ($isOwner): ?>
                <button type="button" class="btn-book btn-disabled" disabled>Votre trajet</button>
              <?php elseif ($ride['seats'] <= 0): ?>
                <button type="button" class="btn-book btn-disabled" disabled style="background:#e05c5c;">Trip full ✗</button>
              <?php elseif ($alreadyBooked): ?>
                <button type="button" class="btn-book btn-disabled" disabled style="background:#aaa;">Already booked ✓</button>
              <?php else: ?>
                <button type="button" class="btn-book" 
                        data-ride-id="<?= (int)$ride['id'] ?>"
                        data-ride-departure="<?= htmlspecialchars($ride['departure'], ENT_QUOTES) ?>"
                        data-ride-destination="<?= htmlspecialchars($ride['destination'], ENT_QUOTES) ?>"
                        data-ride-seats="<?= (int)$ride['seats'] ?>"
                        onclick="openBookingModal(this)">
                  Réserver
                </button>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>

      </div>
    </main>
  </div>

  <div class="modal-overlay" id="bookingModal">
    <div class="modal">
      <div class="modal-header">
        <h2>Réserver des places</h2>
        <button type="button" class="modal-close" onclick="closeBookingModal()">×</button>
      </div>
      <p id="modalRideLabel" style="margin-bottom:16px;color:var(--text-mid);"></p>
      <form method="POST" id="bookingForm">
        <input type="hidden" name="book_ride_id" id="modalRideId">
        <div style="margin-bottom:16px;">
          <label for="bookSeatCount" style="display:block;margin-bottom:8px;font-weight:600;">Nombre de places</label>
          <input id="bookSeatCount" name="book_seat_count" type="number" min="1" value="1" style="width:100%;padding:10px;border:1px solid #d9e6e9;border-radius:12px;font-size:0.95rem;" required>
          <div id="seatHelp" style="margin-top:8px;font-size:0.88rem;color:var(--text-light);"></div>
        </div>
        <div id="modalError" style="display:none;margin-bottom:16px;padding:12px;border:1px solid #e05c5c;border-radius:10px;background:#fff0f0;color:#a83232;"></div>
        <div style="display:flex;justify-content:flex-end;gap:10px;">
          <button type="button" class="btn-secondary" onclick="closeBookingModal()" style="background:#f1f7f7;color:#1a9fa0;padding:10px 18px;border-radius:10px;border:none;cursor:pointer;">Annuler</button>
          <button type="submit" class="btn-book" style="padding:10px 18px;">Confirmer</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openBookingModal(button) {
      var rideId = button.dataset.rideId;
      var departure = button.dataset.rideDeparture;
      var destination = button.dataset.rideDestination;
      var seats = parseInt(button.dataset.rideSeats, 10);

      document.getElementById('modalRideLabel').textContent = departure + ' → ' + destination + ' (' + seats + ' place' + (seats > 1 ? 's' : '') + ' disponibles)';
      document.getElementById('modalRideId').value = rideId;

      var seatField = document.getElementById('bookSeatCount');
      seatField.max = seats;
      seatField.value = 1;
      document.getElementById('seatHelp').textContent = 'Maximum ' + seats + ' place' + (seats > 1 ? 's' : '') + '.';
      document.getElementById('modalError').style.display = 'none';
      document.getElementById('bookingModal').classList.add('open');
    }

    function closeBookingModal() {
      document.getElementById('bookingModal').classList.remove('open');
    }

    document.getElementById('bookingForm').addEventListener('submit', function (event) {
      var seatField = document.getElementById('bookSeatCount');
      var maxSeats = parseInt(seatField.max, 10);
      var requested = parseInt(seatField.value, 10);
      var errorBox = document.getElementById('modalError');

      if (requested < 1) {
        event.preventDefault();
        errorBox.textContent = 'Veuillez indiquer au moins une place.';
        errorBox.style.display = 'block';
        return;
      }
      if (requested > maxSeats) {
        event.preventDefault();
        errorBox.textContent = 'Le nombre de places demandé est supérieur aux places disponibles.';
        errorBox.style.display = 'block';
        return;
      }
    });
  </script>
</body>
</html>


