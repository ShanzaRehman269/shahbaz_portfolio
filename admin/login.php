<?php
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* If already logged in */
if (!empty($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

/*
|--------------------------------------------------------------------------
| IMPORTANT
|--------------------------------------------------------------------------
| Keep username EMPTY.
| Do NOT put any default username here.
*/
$username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    /* Validate fields */
    if ($username === '' || $password === '') {

        $error = "Please enter your username and password.";

    } else {

        /* Check username and password */
        if (checkPassword($username, $password)) {

            session_regenerate_id(true);

            /*
            |--------------------------------------------------------------------------
            | Get admin information
            |--------------------------------------------------------------------------
            */
            $stmt = $conn->prepare(
                "SELECT id, username
                 FROM admin
                 WHERE username = ?
                 LIMIT 1"
            );

            if ($stmt) {

                $stmt->bind_param("s", $username);

                $stmt->execute();

                $result = $stmt->get_result();

                if ($result && $result->num_rows === 1) {

                    $admin = $result->fetch_assoc();

                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];

                    $stmt->close();

                    header("Location: dashboard.php");
                    exit;
                }

                $stmt->close();
            }

            $error = "Something went wrong. Please try again.";

        } else {

            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Shahbaz Portfolio</title>
    <style>
        * {box-sizing: border-box;margin: 0;padding: 0;}
        body {min-height: 100vh;font-family: Arial, Helvetica, sans-serif;background: linear-gradient(135deg, #dceeff, #edf7ff);display: flex;align-items: center;justify-content: center;padding: 20px;}
        .login-container {width: 100%;max-width: 430px;}
        .login-card {background: #ffffff;border-radius: 18px;padding: 40px 35px;box-shadow: 0 15px 45px rgba(31, 78, 121, 0.15);border: 1px solid #d5e8f8;}
        .logo {width: 70px;height: 70px;margin: 0 auto 20px;border-radius: 18px;background: #1f5f8b;color: #ffffff;display: flex;align-items: center;justify-content: center;font-size: 28px;font-weight: bold;}
        h1 {text-align: center;color: #173f5f;font-size: 28px;margin-bottom: 8px;}
        .subtitle {text-align: center;color: #668096;font-size: 14px;margin-bottom: 28px;}
        .error {background: #ffe8e8;color: #b42318;border: 1px solid #f5b5b5;padding: 12px 14px;border-radius: 9px;margin-bottom: 20px;font-size: 14px;}
        .form-group {margin-bottom: 18px;}
        label {display: block;color: #244b68;font-size: 14px;font-weight: 600;margin-bottom: 8px;}
        input {width: 100%;padding: 13px 14px;border: 1px solid #bdd4e6;border-radius: 9px;outline: none;font-size: 15px;color: #173f5f;background: #f8fcff;transition: 0.2s;}
        input:focus {border-color: #4c91c2;box-shadow: 0 0 0 3px rgba(76, 145, 194, 0.12);background: #ffffff;}
        .login-btn {width: 100%;border: none;border-radius: 9px;padding: 14px;background: #1f5f8b;color: #ffffff;font-size: 16px;font-weight: 600;cursor: pointer;transition: 0.2s;margin-top: 5px;}
        .login-btn:hover {background: #17496c;}
        .forgot {text-align: center;margin-top: 20px;}
        .forgot a {color: #1f5f8b;text-decoration: none;font-size: 14px;font-weight: 600;}
        .forgot a:hover {text-decoration: underline;}
        .back-home {text-align: center;margin-top: 18px;}
        .back-home a {color: #668096;text-decoration: none;font-size: 13px;}
        .back-home a:hover {color: #1f5f8b;}
        @media (max-width: 480px) {
            body {padding: 15px;}
            .login-card {padding: 30px 22px;border-radius: 15px;}
            h1 {font-size: 24px;}
            .logo {width: 60px;height: 60px;font-size: 24px;}
        }
    </style>
</head>

<body>

<div class="login-container">
    <div class="login-card">
        <div class="logo">S</div>
        <h1>Admin Login</h1>
        <p class="subtitle">Login to manage your portfolio</p>

        <?php if ($error !== ""): ?>
            <div class="error">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <!-- USERNAME -->
            <div class="form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    autocomplete="off"
                    required
                    autofocus
                >
            </div>

            <!-- PASSWORD -->
            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="off"
                    required
                >
            </div>

            <!-- LOGIN BUTTON -->
            <button type="submit" class="login-btn">Login</button>
        </form>

        <!-- FORGOT PASSWORD -->
        <div class="forgot">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>

        <!-- BACK TO PORTFOLIO -->
        <div class="back-home">
            <a href="../index.php">← Back to Portfolio</a>
        </div>
    </div>
</div>

</body>
</html>