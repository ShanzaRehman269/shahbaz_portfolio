<?php
session_start();

require_once './includes/config.php';

$error = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    $stmt = $conn->prepare(
        "SELECT * FROM admin WHERE email=?"
    );

    $stmt->bind_param("s", $email);

    $stmt->execute();

    $res = $stmt->get_result()->fetch_assoc();

    if ($res && password_verify($pass, $res['password'])) {

        $_SESSION['admin_login'] = $res['id'];

        header("Location: dashboard.php");

        exit();

    } else {

        $error = "Invalid Email or Password";
    }

    $stmt->close();
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

    <title>Admin Login</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>

        /* ==========================================
           COLORS
        ========================================== */

        :root {

            --navy: #0a2540;

            --navy-light: #1e3a8a;

            --baby: #e0f2fe;

            --baby-dark: #bae6fd;

            --text-dark: #0a2540;

            --text-muted: #64748b;

            --white: #ffffff;

            --danger: #ef4444;
        }


        /* ==========================================
           RESET
        ========================================== */

        * {
            box-sizing: border-box;
        }


        html {
            width: 100%;
            min-height: 100%;
        }


        body {

            background: var(--baby);

            color: var(--text-dark);

            font-family: 'Inter', sans-serif;

            display: flex;

            justify-content: center;

            align-items: center;

            min-height: 100vh;

            width: 100%;

            margin: 0;

            padding: 20px;

            overflow-x: hidden;
        }


        /* ==========================================
           LOGIN BOX
        ========================================== */

        .box {

            width: 100%;

            max-width: 430px;

            background: var(--white);

            padding: 40px;

            border-radius: 16px;

            border: 2px solid var(--baby-dark);

            box-shadow:
                0 10px 30px rgba(10, 37, 64, 0.10);
        }


        /* ==========================================
           HEADING
        ========================================== */

        .box h2 {

            color: var(--navy);

            text-align: center;

            margin: 0 0 25px 0;

            font-weight: 800;

            font-size: 28px;

            line-height: 1.3;
        }


        .box h2 i {

            color: var(--navy);

            margin-right: 8px;
        }


        /* ==========================================
           ERROR
        ========================================== */

        .error {

            padding: 12px;

            background: var(--danger);

            color: var(--white);

            border-radius: 8px;

            margin-bottom: 18px;

            text-align: center;

            font-weight: 600;

            font-size: 14px;

            line-height: 1.4;
        }


        /* ==========================================
           LABELS
        ========================================== */

        .box label {

            display: block;

            font-weight: 700;

            color: var(--navy);

            font-size: 14px;

            margin-bottom: 6px;
        }


        /* ==========================================
           INPUTS
        ========================================== */

        .box input {

            width: 100%;

            padding: 13px 14px;

            margin: 0 0 17px 0;

            border: 2px solid var(--baby-dark);

            border-radius: 9px;

            background: var(--baby);

            color: var(--text-dark);

            font-family: inherit;

            font-size: 15px;

            font-weight: 600;

            box-sizing: border-box;

            transition: 0.25s ease;
        }


        .box input:focus {

            outline: none;

            border-color: var(--navy);

            box-shadow:
                0 0 0 3px rgba(10, 37, 64, 0.08);
        }


        .box input::placeholder {

            color: #64748b;

            font-weight: 500;
        }


        /* ==========================================
           LOGIN BUTTON
        ========================================== */

        .box .btn {

            width: 100%;

            border: none;

            cursor: pointer;

            padding: 13px;

            min-height: 48px;

            background: var(--navy);

            color: var(--white);

            font-family: inherit;

            font-weight: 800;

            border-radius: 9px;

            font-size: 16px;

            transition: 0.25s ease;
        }


        .box .btn:hover {

            background: var(--navy-light);

            transform: translateY(-2px);

            box-shadow:
                0 5px 15px rgba(10, 37, 64, 0.20);
        }


        .box .btn:active {

            transform: translateY(0);
        }


        /* ==========================================
           FORGOT PASSWORD
        ========================================== */

        .forgot {

            text-align: center;

            margin-top: 20px;

            margin-bottom: 0;
        }


        .forgot a {

            color: var(--navy);

            text-decoration: none;

            font-weight: 700;

            font-size: 14px;
        }


        .forgot a:hover {

            text-decoration: underline;
        }


        /* ==========================================
           DEMO LOGIN
        ========================================== */

        .demo {

            text-align: center;

            margin-top: 12px;

            margin-bottom: 0;

            font-size: 12px;

            color: var(--text-muted);

            line-height: 1.5;
        }


        /* ==========================================
           TABLET
        ========================================== */

        @media (max-width: 600px) {

            body {

                padding: 16px;
            }


            .box {

                max-width: 100%;

                padding: 30px 25px;

                border-radius: 14px;
            }


            .box h2 {

                font-size: 24px;

                margin-bottom: 22px;
            }
        }


        /* ==========================================
           MOBILE
        ========================================== */

        @media (max-width: 400px) {

            body {

                padding: 12px;
            }


            .box {

                padding: 25px 18px;

                border-radius: 12px;
            }


            .box h2 {

                font-size: 21px;

                margin-bottom: 20px;
            }


            .box h2 i {

                margin-right: 5px;
            }


            .box label {

                font-size: 13px;
            }


            .box input {

                padding: 12px;

                font-size: 14px;

                margin-bottom: 15px;
            }


            .box .btn {

                min-height: 47px;

                font-size: 15px;
            }


            .forgot a {

                font-size: 13px;
            }


            .demo {

                font-size: 11px;
            }
        }


        /* ==========================================
           VERY SMALL PHONES
        ========================================== */

        @media (max-width: 320px) {

            body {

                padding: 8px;
            }


            .box {

                padding: 22px 14px;
            }


            .box h2 {

                font-size: 19px;
            }
        }

    </style>

</head>


<body>


    <div class="box">

        <h2>

            <i class="fas fa-user-shield"></i>

            Admin Login

        </h2>


        <?php if ($error): ?>

            <div class="error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <form method="POST">

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="admin@example.com"
                autocomplete="username"
                required
            >


            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter Password"
                autocomplete="current-password"
                required
            >


            <button
                type="submit"
                name="login"
                class="btn"
            >

                <i class="fas fa-right-to-bracket"></i>

                Login

            </button>

        </form>


        <p class="forgot">

            <a href="forgot_password.php">

                Forgot Password?

            </a>

        </p>


        <p class="demo">

            Demo: admin@example.com / password

        </p>

    </div>


</body>

</html>