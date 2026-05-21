<?php

require __DIR__ . '/common.php';

$activePage = 'users';

$userModel = new User($pdo);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'], $_POST['user_id'])) {
    $userId = intval($_POST['user_id']);

    if ($userId > 0) {
        if ($userModel->deleteById($userId)) {
            $message = 'Utilisateur supprimé avec succès.';
        } else {
            $message = 'Erreur lors de la suppression.';
        }
    }
}

$users = $userModel->getAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Management | Wayshare Admin</title>

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
        <h1>User Management</h1>
        <p>Gérez les utilisateurs de la plateforme.</p>
      </div>
    </div>

    <!-- MESSAGE -->
    <?php if ($message): ?>
      <div class="alert">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <!-- TABLE -->
    <div class="section-card">
      <h2>Liste des utilisateurs</h2>

      <?php if (empty($users)): ?>
        <p>Aucun utilisateur trouvé.</p>
      <?php else: ?>
        <table class="users-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>

                <td>
                  <form method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                    <input type="hidden" name="user_id" value="<?= intval($user['id']) ?>">
                    <button type="submit" name="delete_user" class="btn-danger">
                      Supprimer
                    </button>
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