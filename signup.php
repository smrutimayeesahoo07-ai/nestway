<?php
// ============================================================
//  signup.php  –  Student Registration Page
// ============================================================
include 'db.php';
session_start();

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error   = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $phone    = trim(mysqli_real_escape_string($conn, $_POST['phone']));
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "An account with this email already exists.";
        } else {
            // Hash password securely
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $sql = "INSERT INTO users (name, email, password, phone)
                    VALUES ('$name', '$email', '$hashedPassword', '$phone')";

            if (mysqli_query($conn, $sql)) {
                $success = "Account created successfully! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NestWay – Sign Up</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet"/>
  <style>
    :root{--primary:#1a1a2e;--accent:#e94560;--accent2:#f5a623;--border:#e5e7eb;--muted:#6b7280;}
    body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#1a1a2e 0%,#0f3460 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:30px 16px;}
    .auth-card{background:#fff;border-radius:24px;padding:40px;width:100%;max-width:460px;box-shadow:0 24px 60px rgba(0,0,0,0.4);}
    .brand{font-family:'Syne',sans-serif;font-weight:800;font-size:1.8rem;text-align:center;margin-bottom:4px;}
    .brand span{color:var(--accent);}
    .auth-title{font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:700;text-align:center;margin-bottom:6px;color:var(--primary);}
    .auth-sub{text-align:center;color:var(--muted);font-size:.88rem;margin-bottom:28px;}
    .form-label{font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:5px;}
    .form-control{border:1.5px solid var(--border);border-radius:10px;padding:11px 14px;font-size:.92rem;transition:border-color .2s;}
    .form-control:focus{border-color:var(--accent);box-shadow:none;}
    .input-icon{position:relative;}
    .input-icon i{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:1rem;}
    .input-icon .form-control{padding-left:38px;}
    .btn-auth{width:100%;background:var(--accent);color:#fff;border:none;border-radius:10px;padding:13px;font-size:1rem;font-weight:700;font-family:'Syne',sans-serif;margin-top:8px;transition:background .2s,transform .15s;}
    .btn-auth:hover{background:#c73652;transform:translateY(-1px);}
    .auth-footer{text-align:center;margin-top:20px;font-size:.88rem;color:var(--muted);}
    .auth-footer a{color:var(--accent);text-decoration:none;font-weight:600;}
    .alert-custom{border-radius:10px;font-size:.88rem;padding:10px 14px;margin-bottom:16px;}
  </style>
</head>
<body>
  <div class="auth-card">
    <div class="brand">Nest<span>Way</span></div>
    <div class="auth-title">Create Your Account</div>
    <div class="auth-sub">Join thousands of students finding PGs easily</div>

    <?php if ($error): ?>
      <div class="alert alert-danger alert-custom"><i class="bi bi-exclamation-circle me-2"></i><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success alert-custom"><i class="bi bi-check-circle me-2"></i><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="signup.php">
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <div class="input-icon">
          <i class="bi bi-person"></i>
          <input type="text" name="name" class="form-control" placeholder="Enter your full name" required/>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <div class="input-icon">
          <i class="bi bi-envelope"></i>
          <input type="email" name="email" class="form-control" placeholder="you@example.com" required/>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Phone Number</label>
        <div class="input-icon">
          <i class="bi bi-phone"></i>
          <input type="text" name="phone" class="form-control" placeholder="10-digit mobile number"/>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-icon">
          <i class="bi bi-lock"></i>
          <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required/>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <div class="input-icon">
          <i class="bi bi-lock-fill"></i>
          <input type="password" name="confirm" class="form-control" placeholder="Repeat password" required/>
        </div>
      </div>
      <button type="submit" class="btn-auth">Create Account</button>
    </form>

    <div class="auth-footer">
      Already have an account? <a href="login.php">Login here</a>
    </div>
    <div class="auth-footer mt-2">
      <a href="index.php" style="color:var(--muted);"><i class="bi bi-arrow-left me-1"></i>Back to Listings</a>
    </div>
  </div>
</body>
</html>
<?php mysqli_close($conn); ?>
