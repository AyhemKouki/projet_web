<?php
session_start();
require '../config/db.php';
require '../models/User.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$userModel = new User($pdo);
$user = $userModel->findById($user_id);

if (!$user) {
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($phone)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        // Vérifier si l'email existe déjà pour un autre utilisateur
        $existingUser = $userModel->findByEmail($email);
        if ($existingUser && $existingUser['id'] != $user_id) {
            $error = "Cet email est déjà utilisé par un autre utilisateur.";
        } else {
            if ($userModel->updateProfile($user_id, $name, $email, $phone, $password)) {
                $success = "Profil mis à jour avec succès !";
                // Recharger les informations de l'utilisateur
                $user = $userModel->findById($user_id);
            } else {
                $error = "Erreur lors de la mise à jour du profil.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Wayshare - Edit Profile</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
      .sidebar-avatar {
        cursor: pointer;
        transition: transform 0.2s, background-color 0.2s;
      }
      .sidebar-avatar:hover {
        background-color: var(--teal-dark);
        transform: scale(1.05);
      }
    </style>
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
            <div class="sidebar-avatar" onclick="window.location.href='editProfile.php'">
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
              <a class="snav-item" href="myBookings.php">
                <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                My bookings
              </a>
            </div>
        </nav>
    </aside>

    <!-- ═══ CONTENT ═══ -->
    <main class="panel-area">
      <div class="panel-inner" style="max-width: 600px;">
        <div class="panel-title-row">
            <div class="panel-icon" style="background:#e8f7f7">
                <svg width="22" height="22" viewBox="0 0 24 24" stroke="#1a9fa0" stroke-width="2" fill="none" stroke-linecap="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <h2>Edit my account</h2>
                <p>Update your personal information below</p>
            </div>
        </div>

        <?php if ($success): ?>
          <div class="success-msg" style="margin-top: 0; margin-bottom: 24px;">
            <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>

        <?php if ($error): ?>
          <div class="error-msg" style="margin-top: 0; margin-bottom: 24px;">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form method="POST">
          <div class="field-group">
            <label>Full name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Alex Johnson" required>
          </div>

          <div class="field-group">
            <label>Email address</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="you@example.com" required>
          </div>

          <div class="field-group">
            <label>Phone number</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+216 " required>
          </div>

          <div class="field-group">
            <label>New Password (leave blank to keep current)</label>
            <input type="password" name="password" placeholder="At least 6 characters">
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-full" style="max-width: 220px;">Save changes</button>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
