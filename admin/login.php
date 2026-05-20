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
  <style>
    body { margin:0; font-family: Arial, Helvetica, sans-serif; background: #eef8f8; color: #1a2b30; }
    .auth-container { max-width: 420px; margin: 80px auto; background: #fff; padding: 32px; border-radius: 24px; box-shadow: 0 16px 40px rgba(0,0,0,.08); border: 1px solid #dde8ec; }
    .auth-title { margin-bottom: 24px; }
    .auth-title h1 { margin: 0 0 10px; font-size: 1.9rem; }
    .auth-title p { margin: 0; color: #4a6070; }
    .field-group { margin-bottom: 18px; }
    .field-group label { display: block; margin-bottom: 8px; color: #34404b; font-weight: 700; }
    .field-group input { width: 100%; padding: 12px 14px; border: 1.5px solid #d9e6e9; border-radius: 14px; font-size: .95rem; }
    .field-group input:focus { outline: none; border-color: #1a9fa0; }
    .btn-full { width: 100%; padding: 12px 16px; border: none; border-radius: 14px; background: #1a9fa0; color: white; font-weight: 700; cursor: pointer; }
    .btn-full:hover { background: #138080; }
    .error-msg { margin-bottom: 18px; padding: 12px 14px; border-radius: 14px; background: #fdecea; color: #b01f1f; border: 1px solid #f5c3c3; }
    .note { margin-top: 12px; color: #7a9aaa; font-size: .92rem; }
  </style>
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
