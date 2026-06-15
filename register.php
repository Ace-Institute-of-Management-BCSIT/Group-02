<?php
session_start();
include("config/db.php");

$errors = [];
$success = "";

if (isset($_POST['register'])) {
    $name            = trim($_POST['name']);
    $email           = trim($_POST['email']);
    $password        = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name)){
        $errors[] = "Name is required";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = "Valid email is required";
    }
    if (strlen($password) < 6){
        $errors[] = "Password must be at least 6 characters";
    }
    if ($password !== $confirm_password){
        $errors[] = "Passwords do not match";
    }
    if (empty($errors)) {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $errors[] = "Email already registered!";
        }
        mysqli_stmt_close($check);
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO users(name, email, password) VALUES(?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Expense Tracker</title>
    <link rel="stylesheet" href="Register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="container">

    <div class="left">
        <form method="POST" class="form-card">
            <h1>Create Account</h1>

            <?php if (!empty($errors)): ?>
                <div style="background:#fee; color:#c00; padding:10px; border-radius:8px; margin-bottom:15px;">
                    <?php foreach($errors as $err): ?>
                        <p><strong>⚠️</strong> <?= htmlspecialchars($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="field">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>

            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="field">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="field">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit" name="register">Sign Up</button>

            <p style="text-align:center; margin-top:15px;">
                Already have an account? 
                <a href="login.php" style="color:#6f775f; font-weight:600;">Login here</a>
            </p>
        </form>
    </div>

    <div class="right">
        <div class="hero">
            <h1>Welcome to Expense Tracker</h1>
            <p>Take control of your finances with smart tracking and insights.</p>
        </div>
    </div>

</div>

</body>
</html>