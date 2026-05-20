<?php
session_start();
require '../config/db.php';
require '../models/User.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $userModel = new User($pdo);
    $user = $userModel->findByEmail($email);
  
    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        header("Location: ../public/findRide.php");
        exit();

    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Wayshare - Sign in</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>


<nav>
    <a class="nav-logo" href="index.php">
      <div class="logo-icon"><svg viewBox="0 0 24 24"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3m-4 12H9m10 0h-2m2 0a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2m10 0h-2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div>
      <span>Wayshare</span>
    </a>    
</nav>


<div class="auth-wrap">
    <div class="auth-card">
      <div class="auth-logo">
        <div class="logo-icon" style="width:36px;height:36px;">
          <svg viewBox="0 0 24 24"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3m-4 12H9m10 0h-2m2 0a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2m10 0h-2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
        </div>
        <span>Wayshare</span>
      </div>
      <h1>Welcome back</h1>
      <p>Log in to search and book a ride.</p>

      <div id="loginNotice" style="display:none; align-items:center; gap:10px; background:#e8f7f7; border:1.5px solid var(--teal); border-radius:10px; padding:12px 16px; margin-bottom:20px; font-size:0.88rem; color:#1a5050;">
        <svg width="18" height="18" viewBox="0 0 24 24" stroke="#1a9fa0" stroke-width="2" fill="none" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
        <span>You must be logged in to search and book a ride. No account? <strong style="cursor:pointer;text-decoration:underline;" onclick="show('signup')">Sign up for free</strong>.</span>
      </div>

      <form method="POST">
        <div class="field-group">
            <label>Email</label>
            <input name="email" type="email" placeholder="you@example.com" required>
        </div>
        <div class="field-group">
            <label>Password</label>
            <input name="password" type="password" required>
        </div>
        <button class="btn-full" type="submit">Log in</button>
      </form>
      <?php if (!empty($error)) : ?>
        <div class="error-msg">
          <?= $error ?>
        </div>
      <?php endif; ?>
      <div class="auth-footer">
        New here? <a href="register.php">Create an account</a>
      </div>

      
    </div>
  </div>


</body>
</html>