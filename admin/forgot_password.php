<?php
require __DIR__ . '/includes/functions.php';

$msg = "";

if (isset($_POST['reset'])) {

    $username = trim($_POST['username'] ?? '');
    $newpass  = $_POST['newpass'] ?? '';
    $cpass    = $_POST['cpass'] ?? '';

    if (empty($username) || empty($newpass) || empty($cpass)) {

        $msg = "All fields are required!";

    } elseif ($newpass !== $cpass) {

        $msg = "Passwords do not match!";

    } else {

        $stmt = $conn->prepare(
            "SELECT id FROM admin WHERE username = ?"
        );

        $stmt->bind_param("s", $username);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $hash = password_hash(
                $newpass,
                PASSWORD_DEFAULT
            );

            $stmt2 = $conn->prepare(
                "UPDATE admin SET password=? WHERE username=?"
            );

            $stmt2->bind_param(
                "ss",
                $hash,
                $username
            );

            if ($stmt2->execute()) {

                $msg = "Password Successfully Reset!
                <a href='login.php'>Login Now</a>";

            } else {

                $msg = "Error updating password!";
            }

            $stmt2->close();

        } else {

            $msg = "Username not found!";
        }

        $stmt->close();
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

    <title>Reset Password</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


<style>

/* =========================================================
   COLORS
========================================================= */

:root {

    --navy: #0a2540;

    --navy-light: #1e3a8a;

    --baby: #e0f2fe;

    --baby-dark: #bae6fd;

    --text-dark: #0a2540;

    --text-light: #e0f2fe;

    --danger: #ef4444;

    --success: #22c55e;

    --white: #ffffff;

    --muted: #64748b;
}


/* =========================================================
   RESET
========================================================= */

* {

    margin: 0;

    padding: 0;

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

    width: 100%;

    min-height: 100vh;

    display: flex;

    justify-content: center;

    align-items: center;

    padding: 20px;

    margin: 0;

    overflow-x: hidden;
}


/* =========================================================
   RESET PASSWORD BOX
========================================================= */

.box {

    width: 100%;

    max-width: 400px;

    background: var(--white);

    padding: 40px;

    border-radius: 16px;

    border: 2px solid var(--baby-dark);

    box-shadow:
        0 10px 30px rgba(10, 37, 64, 0.10);
}


/* =========================================================
   HEADING
========================================================= */

h2 {

    color: var(--navy);

    text-align: center;

    margin: 0 0 25px 0;

    font-weight: 800;

    font-size: 28px;

    line-height: 1.3;
}


/* =========================================================
   ALERT
========================================================= */

.alert {

    background: var(--danger);

    color: var(--white);

    padding: 12px;

    border-radius: 8px;

    margin-bottom: 18px;

    text-align: center;

    font-weight: 600;

    font-size: 14px;

    line-height: 1.5;

    overflow-wrap: anywhere;
}


.alert.success {

    background: var(--success);
}


.alert a {

    color: var(--white);

    text-decoration: underline;

    font-weight: 800;
}


/* =========================================================
   LABELS
========================================================= */

label {

    display: block;

    font-weight: 700;

    color: var(--navy);

    font-size: 14px;

    margin-bottom: 6px;
}


/* =========================================================
   INPUTS
========================================================= */

input {

    width: 100%;

    padding: 13px 14px;

    margin: 0 0 17px 0;

    background: var(--baby);

    border: 2px solid var(--baby-dark);

    color: var(--text-dark);

    border-radius: 8px;

    font-family: 'Inter', sans-serif;

    font-size: 15px;

    font-weight: 600;

    transition: 0.25s ease;
}


input:focus {

    outline: none;

    border-color: var(--navy);

    box-shadow:
        0 0 0 3px rgba(10, 37, 64, 0.08);
}


input::placeholder {

    color: var(--muted);

    font-weight: 500;
}


/* =========================================================
   RESET BUTTON
========================================================= */

.btn {

    width: 100%;

    min-height: 48px;

    padding: 13px;

    background: var(--navy);

    color: var(--text-light);

    border: none;

    font-family: 'Inter', sans-serif;

    font-weight: 800;

    border-radius: 8px;

    cursor: pointer;

    font-size: 16px;

    transition: 0.25s ease;
}


.btn:hover {

    background: var(--navy-light);

    transform: translateY(-2px);

    box-shadow:
        0 5px 15px rgba(10, 37, 64, 0.20);
}


.btn:active {

    transform: translateY(0);
}


/* =========================================================
   BACK TO LOGIN
========================================================= */

.back-login {

    text-align: center;

    margin-top: 18px;
}


.back-login a {

    color: var(--navy);

    text-decoration: none;

    font-weight: 700;

    font-size: 14px;
}


.back-login a:hover {

    text-decoration: underline;
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 600px) {

    body {

        padding: 16px;
    }


    .box {

        max-width: 100%;

        padding: 30px 25px;

        border-radius: 14px;
    }


    h2 {

        font-size: 24px;

        margin-bottom: 22px;
    }
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 400px) {

    body {

        padding: 12px;
    }


    .box {

        padding: 25px 18px;

        border-radius: 12px;
    }


    h2 {

        font-size: 22px;

        margin-bottom: 20px;
    }


    label {

        font-size: 13px;
    }


    input {

        padding: 12px;

        font-size: 14px;

        margin-bottom: 15px;
    }


    .btn {

        min-height: 47px;

        font-size: 15px;
    }


    .alert {

        font-size: 13px;

        padding: 10px;
    }


    .back-login a {

        font-size: 13px;
    }
}


/* =========================================================
   VERY SMALL PHONES
========================================================= */

@media (max-width: 320px) {

    body {

        padding: 8px;
    }


    .box {

        padding: 22px 14px;
    }


    h2 {

        font-size: 20px;
    }


    input {

        font-size: 13px;
    }


    .btn {

        font-size: 14px;
    }
}

</style>

</head>


<body>


<div class="box">


    <h2>
        Reset Password
    </h2>


    <?php if ($msg != ""): ?>

        <?php
        $class = str_contains(
            $msg,
            'Successfully'
        ) ? 'success' : '';
        ?>

        <div class="alert <?= $class ?>">

            <?= $msg ?>

        </div>

    <?php endif; ?>


    <form method="POST">


        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            name="username"
            placeholder="admin"
            autocomplete="username"
            required
        >


        <label for="newpass">
            New Password
        </label>

        <input
            type="password"
            id="newpass"
            name="newpass"
            placeholder="Enter new password"
            autocomplete="new-password"
            required
        >


        <label for="cpass">
            Confirm Password
        </label>

        <input
            type="password"
            id="cpass"
            name="cpass"
            placeholder="Confirm new password"
            autocomplete="new-password"
            required
        >


        <button
            type="submit"
            name="reset"
            class="btn"
        >

            Reset Password

        </button>

    </form>


    <p class="back-login">

        <a href="login.php">

            ← Back to Login

        </a>

    </p>


</div>


</body>

</html>