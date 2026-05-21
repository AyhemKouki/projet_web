<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../models/Admin.php';

$adminModel = new Admin($pdo);

if ($adminModel->isLogged()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($adminModel->authenticate($email, $password)) {
        $adminModel->login($email);
        header('Location: dashboard.php');
        exit();
    }

    $error = 'Identifiants admin invalides.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Wayshare</title>
  <link rel="stylesheet" href="../assets/style.css">
  <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-title">
      <h1>Connexion Admin</h1>
      <p>Connectez-vous avec votre compte superviseur.</p>
    </div>

    <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="field-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="field-group">
        <label>Mot de passe</label>
        <input type="password" name="password" required>
      </div>
      <button class="btn-full" type="submit">Connexion</button>
    </form>
    <p class="note">Cette page est dédiée au superviseur. Les utilisateurs normaux se connectent depuis le dossier public.</p>
  </div>
</body>
</html>
