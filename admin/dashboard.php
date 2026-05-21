<?php
require __DIR__ . '/common.php';

$rideModel    = new Ride($pdo);
$userModel    = new User($pdo);
$bookingModel = new Booking($pdo);
$avisModel    = new Avis($pdo);

$totalRides    = $rideModel->countAll();
$totalUsers    = $userModel->countAll();
$totalBookings = $bookingModel->countAll();
$totalReviews  = $avisModel->countAll();

$latestRides = $rideModel->getLatest(5);

$activePage = 'dashboard';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin | Wayshare</title>

  <link rel="stylesheet" href="../assets/style.css" />
  <link rel="stylesheet" href="../assets/admin.css" />
</head>

<body>

<!-- TOPBAR -->
<nav class="topbar">
  <a class="nav-logo" href="dashboard.php">
    <div class="logo-icon">
      <svg viewBox="0 0 24 24">
        <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3m-4 12H9m10 0h-2m2 0a2 2 0 0 1 2-2v-6a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2m10 0h-2" fill="white"/>
      </svg>
    </div>
    <span>Wayshare Admin</span>
  </a>
</nav>

<div class="app-layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-section">
      <div class="sidebar-section-label">Admin</div>

      <a class="snav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">
        Dashboard
      </a>

      <a class="snav-item <?= $activePage === 'rides' ? 'active' : '' ?>" href="rides.php">
        Ride Management
      </a>

      <a class="snav-item <?= $activePage === 'users' ? 'active' : '' ?>" href="users.php">
        User Management
      </a>

      <a class="snav-item danger" href="logout.php">
        Logout
      </a>

    </div>
  </aside>

  <!-- MAIN -->
  <main class="admin-shell">

    <div class="admin-header">
      <div>
        <h1>Dashboard</h1>
        <p>Surveillez les activités de la plateforme.</p>
        
      </div>
      <span class="inline-badge">Espace administrateur</span>

      
    </div>

    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card">
        <label>Total des trajets</label>
        <strong><?= intval($totalRides) ?></strong>
      </div>

      <div class="stat-card">
        <label>Total des utilisateurs</label>
        <strong><?= intval($totalUsers) ?></strong>
      </div>

      <div class="stat-card">
        <label>Total des réservations</label>
        <strong><?= intval($totalBookings) ?></strong>
      </div>

      <div class="stat-card">
        <label>Total des avis</label>
        <strong><?= intval($totalReviews) ?></strong>
      </div>
    </div>

    <!-- LATEST RIDES -->
    <div class="section-card">
      <h2>Derniers trajets</h2>

      <?php if (empty($latestRides)): ?>
        <p>Aucun trajet trouvé.</p>
      <?php else: ?>
        <table class="rides-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Départ</th>
              <th>Destination</th>
              <th>Date</th>
              <th>Heure</th>
              <th>Places</th>
              <th>Prix</th>
              <th>Conducteur</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($latestRides as $ride): ?>
              <tr>
                <td><?= htmlspecialchars($ride['id']) ?></td>
                <td><?= htmlspecialchars($ride['departure']) ?></td>
                <td><?= htmlspecialchars($ride['destination']) ?></td>
                <td><?= htmlspecialchars($ride['date']) ?></td>
                <td><?= htmlspecialchars($ride['departure_time']) ?></td>
                <td><?= htmlspecialchars($ride['seats']) ?></td>
                <td><?= htmlspecialchars($ride['price']) ?> €</td>
                <td><?= htmlspecialchars($ride['driver_name']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    </div>

  </main>
</div>

</body>
</html>