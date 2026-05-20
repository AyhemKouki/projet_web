<?php
require __DIR__ . '/common.php';

$totalRides = $pdo->query('SELECT COUNT(*) FROM rides')->fetchColumn();
$totalUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalBookings = $pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
$totalReviews = $pdo->query('SELECT COUNT(*) FROM avis')->fetchColumn();

$latestRides = $pdo->query('SELECT r.id, r.departure, r.destination, r.date, r.departure_time, r.seats, r.price, u.name AS driver_name FROM rides r JOIN users u ON u.id = r.driver_id ORDER BY r.date DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard admin | Wayshare</title>
  <link rel="stylesheet" href="../assets/style.css" />
  <style>
    :root{--teal:#1a9fa0;--teal-dark:#138080;--teal-light:#e8f7f7;--teal-xlight:#f3fbfb;--text-dark:#1a2b30;--text-mid:#4a6070;--text-light:#7a9aaa;--white:#ffffff;--border:#dde8ec;--bg:#eef8f8;--danger:#e05555;}
    body{margin:0;font-family:Arial,Helvetica,sans-serif;background:var(--bg);color:var(--text-dark);min-height:100vh;}
    .topbar{display:flex;justify-content:space-between;align-items:center;padding:18px 32px;background:var(--white);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:50;}
    .nav-logo{display:flex;align-items:center;gap:12px;text-decoration:none;color:var(--text-dark);font-weight:800;}
    .logo-icon{width:42px;height:42px;background:var(--teal);border-radius:14px;display:flex;align-items:center;justify-content:center;}
    .logo-icon svg{width:20px;height:20;stroke:white;stroke-width:2;}
    .topbar-info{color:var(--text-mid);font-weight:600;}
    .app-layout{display:grid;grid-template-columns:260px 1fr;min-height:calc(100vh - 80px);}
    .sidebar{background:var(--white);border-right:1px solid var(--border);padding:24px 16px;position:sticky;top:80px;height:calc(100vh - 80px);overflow-y:auto;}
    .sidebar-section{margin-bottom:32px;}
    .sidebar-section-label{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-light);margin-bottom:12px;padding:0 8px;}
    .snav-item{display:flex;align-items:center;gap:10px;padding:11px 14px;border-radius:12px;text-decoration:none;color:var(--text-mid);font-size:.92rem;font-weight:600;transition:.2s;background:none;}
    .snav-item:hover{background:var(--teal-xlight);color:var(--teal);}
    .snav-item.active{background:var(--teal-xlight);color:var(--teal);}
    .snav-item.danger{color:var(--danger);} .snav-item.danger:hover{background:#fdecea;color:var(--danger);}
    .snav-item svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
    .admin-shell{padding:36px 32px;}
    .admin-header{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:28px;}
    .admin-header h1{margin:0;font-size:clamp(2rem,2.4vw,2.6rem);}
    .admin-header p{margin:8px 0 0;color:var(--text-mid);font-size:.95rem;}
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:32px;}
    .stat-card{background:var(--white);border:1px solid var(--border);border-radius:18px;padding:24px;box-shadow:0 2px 16px rgba(26,159,160,.08);}
    .stat-card label{display:block;margin-bottom:12px;color:var(--text-light);font-size:.85rem;font-weight:600;}
    .stat-card strong{display:block;font-size:2rem;margin-bottom:6px;}
    .stat-card .stat-sub{color:var(--text-mid);font-size:.9rem;}
    .section-card{background:var(--white);border:1px solid var(--border);border-radius:22px;padding:24px;box-shadow:0 2px 16px rgba(26,159,160,.08);}
    .section-card h2{margin-top:0;font-size:1.2rem;margin-bottom:18px;}
    .rides-table{width:100%;border-collapse:collapse;}
    .rides-table th, .rides-table td{padding:14px 16px;border-bottom:1px solid #eef3f5;text-align:left;font-size:.95rem;}
    .rides-table th{color:var(--text-mid);font-size:.78rem;text-transform:uppercase;letter-spacing:.08em;}
    .rides-table tr:last-child td{border-bottom:none;}
    .inline-badge{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:999px;background:var(--teal-xlight);color:var(--teal);font-weight:700;font-size:.9rem;}
    .btn-outline{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:12px;border:1.5px solid var(--teal);color:var(--teal);text-decoration:none;font-weight:700;}
    .btn-outline:hover{background:var(--teal-xlight);}
    @media(max-width:1000px){.app-layout{grid-template-columns:1fr;} .sidebar{position:relative;top:0;height:auto;} .stats-grid{grid-template-columns:1fr 1fr;}} 
    @media(max-width:700px){.stats-grid{grid-template-columns:1fr;} .admin-header{flex-direction:column;align-items:flex-start;}} 
  </style>
</head>
<body>
<?php renderAdminTopbar(); ?>
<div class="app-layout">
  <?php renderAdminSidebar('dashboard'); ?>
  <main class="admin-shell">
    <div class="admin-header">
      <div>
        <h1>Dashboard</h1>
        <p>Surveillez les trajets, les utilisateurs et l'activité globale.</p>
      </div>
      <span class="inline-badge">Espace administrateur</span>
    </div>

    <div class="stats-grid">
      <div class="stat-card"><label>Total des trajets</label><strong><?= intval($totalRides) ?></strong><div class="stat-sub">Trajets enregistrés dans la base.</div></div>
      <div class="stat-card"><label>Total des utilisateurs</label><strong><?= intval($totalUsers) ?></strong><div class="stat-sub">Comptes inscrits sur la plateforme.</div></div>
      <div class="stat-card"><label>Total des réservations</label><strong><?= intval($totalBookings) ?></strong><div class="stat-sub">Réservations créées par les passagers.</div></div>
      <div class="stat-card"><label>Total des avis</label><strong><?= intval($totalReviews) ?></strong><div class="stat-sub">Retours publiés par la communauté.</div></div>
    </div>

    <div class="section-card">
      <h2>Derniers trajets ajoutés</h2>
      <?php if (empty($latestRides)): ?>
        <p>Aucun trajet n’a été trouvé.</p>
      <?php else: ?>
        <table class="rides-table">
          <thead>
            <tr><th>ID</th><th>Départ</th><th>Destination</th><th>Date</th><th>Heure</th><th>Places</th><th>Prix</th><th>Conducteur</th></tr>
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
