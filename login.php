<?php
session_start();
include("config/db.php");

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT id, name, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $name, $hashed_password);

    if (mysqli_stmt_fetch($stmt)) {
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id']   = $id;
            $_SESSION['user_name'] = $name;
            header("Location: dashboard.php");
            exit();
        }
    }
    $error = "Invalid email or password!";
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Expense Tracker</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="container">

    <!-- LEFT - FORM -->
    <div class="left">
        <div class="login-box">

            <h1>Sign In</h1>
            <p>Welcome back. Please login to continue.</p>

            <?php if ($error): ?>
                <div style="background:#fee; color:#c00; padding:10px; border-radius:8px; margin-bottom:15px; text-align:center;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-box">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" required>
                    <label>Email</label>
                </div>

                <div class="input-box">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" required>
                    <label>Password</label>
                    <span onclick="togglePassword()" class="eye">
                        <i class="fa-regular fa-eye"></i>
                    </span>
                </div>

                <div class="row">
                    <label><input type="checkbox"> Remember me</label>
                    <a href="#">Forgot password?</a>
                </div>

                <button type="submit" name="login">Login</button>
            </form>

            <p style="text-align:center; margin-top:20px;">
                Don't have an account? 
                <a href="register.php" style="color:#6b7a55; font-weight:600;">Sign Up</a>
            </p>
        </div>
    </div>

    <div class="right">
        <div class="overlay">
            <h2>Welcome Back</h2>
            <p>Access your dashboard, manage expenses and stay on top of your finances.</p>
            <div class="circle"></div>
            <div class="circle small"></div>
        </div>
    </div>

</div>

<script>
function togglePassword() {
    const pass = document.getElementById("password");
    const icon = document.querySelector('.eye i');
    
    if (pass.type === "password") {
        pass.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        pass.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>