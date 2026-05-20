<?php
require __DIR__ . '/common.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ride'], $_POST['ride_id'])) {
    $rideId = intval($_POST['ride_id']);
    if ($rideId > 0) {
        $deleteStmt = $pdo->prepare('DELETE FROM rides WHERE id = ?');
        if ($deleteStmt->execute([$rideId])) {
            $message = 'Trajet supprimé avec succès.';
        } else {
            $message = 'Impossible de supprimer ce trajet.';
        }
    }
}

$rides = $pdo->query('SELECT r.id, r.departure, r.destination, r.date, r.departure_time, r.seats, r.price, u.name AS driver_name FROM rides r JOIN users u ON u.id = r.driver_id ORDER BY r.date DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ride Management | Wayshare Admin</title>
  <link rel="stylesheet" href="../assets/style.css" />
  <style>
    :root{--teal:#1a9fa0;--text-dark:#1a2b30;--text-mid:#4a6070;--text-light:#7a9aaa;--white:#ffffff;--border:#dde8ec;--bg:#eef8f8;--danger:#e05555;}
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
    .snav-item:hover{background:rgba(26,159,160,.1);color:var(--teal);}
    .snav-item.active{background:rgba(26,159,160,.1);color:var(--teal);}
    .snav-item.danger{color:var(--danger);} .snav-item.danger:hover{background:#fdecea;color:var(--danger);}
    .snav-item svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
    .admin-shell{padding:36px 32px;}
    .admin-header{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:28px;}
    .admin-header h1{margin:0;font-size:clamp(2rem,2.4vw,2.6rem);}
    .admin-header p{margin:8px 0 0;color:var(--text-mid);font-size:.95rem;}
    .section-card{background:var(--white);border:1px solid var(--border);border-radius:22px;padding:24px;box-shadow:0 2px 16px rgba(26,159,160,.08);}
    .section-card h2{margin-top:0;font-size:1.2rem;margin-bottom:18px;}
    .alert{padding:14px 18px;border-radius:16px;margin-bottom:20px;border:1px solid #cce7e6;color:#1a5a58;background:#e8f7f7;}
    .rides-table{width:100%;border-collapse:collapse;}
    .rides-table th, .rides-table td{padding:14px 16px;border-bottom:1px solid #eef3f5;text-align:left;vertical-align:middle;font-size:.95rem;}
    .rides-table th{color:var(--text-mid);font-size:.78rem;text-transform:uppercase;letter-spacing:.08em;}
    .rides-table tr:last-child td{border-bottom:none;}
    .td-actions{display:flex;gap:10px;}
    .btn-danger{background:var(--danger);color:white;border:none;border-radius:10px;padding:9px 16px;font-size:.88rem;font-weight:700;cursor:pointer;}
    .btn-danger:hover{background:#c84444;}
    @media(max-width:1000px){.app-layout{grid-template-columns:1fr;} .sidebar{position:relative;top:0;height:auto;}} 
  </style>
</head>
<body>
<?php renderAdminTopbar(); ?>
<div class="app-layout">
  <?php renderAdminSidebar('rides'); ?>
  <main class="admin-shell">
    <div class="admin-header">
      <div>
        <h1>Ride Management</h1>
        <p>Gérez tous les trajets proposés sur la plateforme.</p>
      </div>
      <a class="btn-outline" href="dashboard.php">Retour au dashboard</a>
    </div>

    <div class="section-card">
      <h2>Trajets enregistrés</h2>
      <?php if ($message): ?><div class="alert"><?= htmlspecialchars($message) ?></div><?php endif; ?>
      <?php if (empty($rides)): ?>
        <p>Aucun trajet disponible.</p>
      <?php else: ?>
        <table class="rides-table">
          <thead>
            <tr><th>ID</th><th>Conducteur</th><th>Départ</th><th>Destination</th><th>Date</th><th>Heure</th><th>Places</th><th>Prix</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($rides as $ride): ?>
              <tr>
                <td><?= htmlspecialchars($ride['id']) ?></td>
                <td><?= htmlspecialchars($ride['driver_name']) ?></td>
                <td><?= htmlspecialchars($ride['departure']) ?></td>
                <td><?= htmlspecialchars($ride['destination']) ?></td>
                <td><?= htmlspecialchars($ride['date']) ?></td>
                <td><?= htmlspecialchars($ride['departure_time']) ?></td>
                <td><?= htmlspecialchars($ride['seats']) ?></td>
                <td><?= htmlspecialchars($ride['price']) ?> €</td>
                <td class="td-actions">
                  <form method="post" onsubmit="return confirm('Souhaitez-vous vraiment supprimer ce trajet ?');">
                    <input type="hidden" name="ride_id" value="<?= intval($ride['id']) ?>">
                    <button type="submit" name="delete_ride" class="btn-danger">Supprimer</button>
                  </form>
                </td>
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
