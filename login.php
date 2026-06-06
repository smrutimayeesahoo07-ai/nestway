<?php
// ============================================================
//  login.php  –  Student Login Page
// ============================================================
include 'db.php';
session_start();

// Already logged in? Go home
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Find user by email
        $sql    = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $user   = mysqli_fetch_assoc($result);

        // Verify password against stored hash
        if ($user && password_verify($password, $user['password'])) {
            // Store user info in session
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email']= $user['email'];

            // Redirect to home page
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NestWay – Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet"/>
  <style>
    :root{--primary:#1a1a2e;--accent:#e94560;--accent2:#f5a623;--border:#e5e7eb;--muted:#6b7280;}
    body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#1a1a2e 0%,#0f3460 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:30px 16px;}
    .auth-card{background:#fff;border-radius:24px;padding:44px;width:100%;max-width:420px;box-shadow:0 24px 60px rgba(0,0,0,0.4);}
    .brand{font-family:'Syne',sans-serif;font-weight:800;font-size:1.8rem;text-align:center;margin-bottom:4px;}
    .brand span{color:var(--accent);}
    .auth-title{font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:700;text-align:center;margin-bottom:6px;}
    .auth-sub{text-align:center;color:var(--muted);font-size:.88rem;margin-bottom:28px;}
    .form-label{font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:5px;}
    .form-control{border:1.5px solid var(--border);border-radius:10px;padding:11px 14px 11px 38px;font-size:.92rem;transition:border-color .2s;}
    .form-control:focus{border-color:var(--accent);box-shadow:none;}
    .input-icon{position:relative;}
    .input-icon i{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted);}
    .btn-auth{width:100%;background:var(--accent);color:#fff;border:none;border-radius:10px;padding:13px;font-size:1rem;font-weight:700;font-family:'Syne',sans-serif;margin-top:8px;transition:background .2s;}
    .btn-auth:hover{background:#c73652;}
    .auth-footer{text-align:center;margin-top:20px;font-size:.88rem;color:var(--muted);}
    .auth-footer a{color:var(--accent);text-decoration:none;font-weight:600;}
    .alert-custom{border-radius:10px;font-size:.88rem;padding:10px 14px;margin-bottom:18px;}
    .divider{display:flex;align-items:center;gap:10px;margin:20px 0;color:var(--muted);font-size:.82rem;}
    .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border);}
  </style>
</head>
<body>
  <div class="auth-card">
    <div class="brand">Nest<span>Way</span></div>
    <div class="auth-title">Welcome Back!</div>
    <div class="auth-sub">Login to view your shortlisted PGs</div>

    <?php if ($error): ?>
      <div class="alert alert-danger alert-custom"><i class="bi bi-exclamation-circle me-2"></i><?= $error ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['registered'])): ?>
      <div class="alert alert-success alert-custom"><i class="bi bi-check-circle me-2"></i>Account created! Please login.</div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <div class="input-icon">
          <i class="bi bi-envelope"></i>
          <input type="email" name="email" class="form-control" placeholder="you@example.com" required/>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label">Password</label>
        <div class="input-icon">
          <i class="bi bi-lock"></i>
          <input type="password" name="password" class="form-control" placeholder="Your password" required/>
        </div>
      </div>
      <button type="submit" class="btn-auth">
        <i class="bi bi-box-arrow-in-right me-2"></i>Login to NestWay
      </button>
    </form>

    <div class="divider">or</div>

    <div class="auth-footer">
      Don't have an account? <a href="signup.php">Sign up free</a>
    </div>
    <div class="auth-footer mt-2">
      <a href="index.php" style="color:var(--muted);"><i class="bi bi-arrow-left me-1"></i>Back to Listings</a>
    </div>
  </div>
</body>
</html>
<?php mysqli_close($conn); ?>
