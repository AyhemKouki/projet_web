<?php
require __DIR__ . '/common.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name === '' || $email === '') {
        $message = 'Le nom et l’email sont requis.';
    } else {
        $_SESSION['admin_name'] = $name;
        $_SESSION['admin_email'] = $email;
        $_SESSION['admin_phone'] = $phone;
        $adminName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $adminEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $adminPhone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
        $message = 'Paramètres administrateur enregistrés dans la session.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings | Wayshare Admin</title>
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
    .field-group{display:flex;flex-direction:column;gap:8px;margin-bottom:18px;}
    .field-group label{font-weight:700;color:var(--text-dark);}
    .field-group input{padding:12px 14px;border:1.5px solid var(--border);border-radius:12px;font-size:.95rem;outline:none;}
    .field-group input:focus{border-color:var(--teal);}
    .btn-teal{background:var(--teal);color:white;border:none;border-radius:12px;padding:12px 20px;font-weight:700;cursor:pointer;}
    .btn-teal:hover{background:#138080;}
    .alert{padding:14px 18px;border-radius:16px;margin-bottom:20px;border:1px solid #cce7e6;color:#1a5a58;background:#e8f7f7;}
    @media(max-width:1000px){.app-layout{grid-template-columns:1fr;} .sidebar{position:relative;top:0;height:auto;}} 
  </style>
</head>
<body>
<?php renderAdminTopbar(); ?>
<div class="app-layout">
  <?php renderAdminSidebar('settings'); ?>
  <main class="admin-shell">
    <div class="admin-header">
      <div>
        <h1>Settings</h1>
        <p>Modifiez vos informations administrateur et le contact du compte.</p>
      </div>
      <a class="btn-outline" href="dashboard.php">Retour au dashboard</a>
    </div>

    <div class="section-card">
      <h2>Compte administrateur</h2>
      <?php if ($message): ?><div class="alert"><?= htmlspecialchars($message) ?></div><?php endif; ?>
      <form method="post">
        <div class="field-group"><label>Nom</label><input type="text" name="name" value="<?= $adminName ?>" required></div>
        <div class="field-group"><label>Email</label><input type="email" name="email" value="<?= $adminEmail ?>" required></div>
        <div class="field-group"><label>Téléphone</label><input type="tel" name="phone" value="<?= $adminPhone ?>"></div>
        <button class="btn-teal" type="submit">Enregistrer</button>
      </form>
    </div>
  </main>
</div>
</body>
</html>
