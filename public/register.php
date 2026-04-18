<?php
require '../config/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name && $email && $password) {

        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $message = "Email already exists!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword]);

            $message = "User registered successfully!";
        }

    } else {
        $message = "Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wayshare - Sign up</title>
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
      <svg viewBox="0 0 24 24">
        <path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3m-4 12H9m10 0h-2m2 0a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2m10 0h-2"
        stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
      </svg>
    </div>
    <span>Wayshare</span>
  </div>

  <h1>Join the community</h1>
  <p>Sign up in seconds.</p>


  <form method="POST">

    <div class="field-group">
      <label>Full name</label>
      <input type="text" name="name" placeholder="Alex Johnson" required>
    </div>

    <div class="field-group">
      <label>Email</label>
      <input type="email" name="email" placeholder="you@example.com" required>
    </div>

    <div class="field-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="At least 6 characters" required>
    </div>

    <button type="submit" class="btn-full">
      Create account
    </button>

  </form>

  <!-- message PHP -->
  <?php if (!empty($message)) echo "<p style='margin-top:10px;color:var(--teal);'>$message</p>"; ?>

  <div class="auth-footer">
    Already have an account?
    <a href="login.php">Log in</a>
  </div>

</div>
</div>


</body>
</html>