
<?php
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$success = "";
$username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($username === '') {

        $error = "Please enter your username.";

    } elseif ($username !== 'shahbazdev@gmail.com') {

        $error = "Invalid username.";

    } elseif ($newPassword === '') {

        $error = "Please enter a new password.";

    } elseif (strlen($newPassword) < 8) {

        $error = "Password must be at least 8 characters.";

    } elseif ($confirmPassword === '') {

        $error = "Please confirm your new password.";

    } elseif ($newPassword !== $confirmPassword) {

        $error = "Passwords do not match.";

    } else {

        if (updatePassword($username, $newPassword)) {

            $success = "Password reset successfully. You can now login with your new password.";

            $username = "";

        } else {

            $error = "Unable to reset password. Please check your database and try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Forgot Password | Shahbaz Portfolio</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;

            font-family: Arial, Helvetica, sans-serif;

            background: linear-gradient(
                135deg,
                #dceeff,
                #edf7ff
            );

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 430px;
        }

        .card {
            background: #ffffff;

            border-radius: 18px;

            padding: 40px 35px;

            border: 1px solid #d5e8f8;

            box-shadow:
                0 15px 45px
                rgba(31, 78, 121, 0.15);
        }

        .icon {
            width: 70px;
            height: 70px;

            margin: 0 auto 20px;

            border-radius: 18px;

            background: #1f5f8b;

            color: #ffffff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 30px;
        }

        h1 {
            text-align: center;

            color: #173f5f;

            font-size: 27px;

            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;

            color: #668096;

            font-size: 14px;

            line-height: 1.6;

            margin-bottom: 26px;
        }

        .error {
            background: #ffe8e8;

            color: #b42318;

            border: 1px solid #f5b5b5;

            padding: 12px 14px;

            border-radius: 9px;

            margin-bottom: 20px;

            font-size: 14px;

            line-height: 1.5;
        }

        .success {
            background: #e9f7ef;

            color: #176b3a;

            border: 1px solid #b9e4c9;

            padding: 12px 14px;

            border-radius: 9px;

            margin-bottom: 20px;

            font-size: 14px;

            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;

            color: #244b68;

            font-size: 14px;

            font-weight: 600;

            margin-bottom: 8px;
        }

        input {
            width: 100%;

            padding: 13px 14px;

            border: 1px solid #bdd4e6;

            border-radius: 9px;

            outline: none;

            font-size: 15px;

            color: #173f5f;

            background: #f8fcff;

            transition: 0.2s;
        }

        input:focus {
            border-color: #4c91c2;

            box-shadow:
                0 0 0 3px
                rgba(76, 145, 194, 0.12);

            background: #ffffff;
        }

        .reset-btn {
            width: 100%;

            border: none;

            border-radius: 9px;

            padding: 14px;

            background: #1f5f8b;

            color: #ffffff;

            font-size: 16px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;
        }

        .reset-btn:hover {
            background: #17496c;
        }

        .back-login {
            text-align: center;

            margin-top: 20px;
        }

        .back-login a {
            color: #1f5f8b;

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;
        }

        .back-login a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {

            body {
                padding: 15px;
            }

            .card {
                padding: 30px 22px;

                border-radius: 15px;
            }

            h1 {
                font-size: 24px;
            }

            .icon {
                width: 60px;
                height: 60px;

                font-size: 25px;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <div class="card">

        <div class="icon">
            🔑
        </div>

        <h1>Reset Password</h1>

        <p class="subtitle">
            Enter your username and choose a new password.
        </p>

        <?php if ($error !== ""): ?>

            <div class="error">
                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>

        <?php if ($success !== ""): ?>

            <div class="success">
                <?= htmlspecialchars(
                    $success,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?= htmlspecialchars(
                        $username,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    autocomplete="username"
                    required
                >

            </div>

            <div class="form-group">

                <label for="new_password">
                    New Password
                </label>

                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    autocomplete="new-password"
                    required
                >

            </div>

            <div class="form-group">

                <label for="confirm_password">
                    Confirm New Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    autocomplete="new-password"
                    required
                >

            </div>

            <button
                type="submit"
                class="reset-btn"
            >
                Reset Password
            </button>

        </form>

        <div class="back-login">

            <a href="login.php">
                ← Back to Login
            </a>

        </div>

    </div>

</div>

</body>

</html>
