<?php
session_start();

require_once 'includes/functions.php';
checkLogin();

$msg = "";
$upload_dir = "uploads/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

/* =========================================================
   1. UPDATE PROFILE INFORMATION
========================================================= */
if (isset($_POST['update_info'])) {

    $name  = trim($_POST['name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $bio   = trim($_POST['bio'] ?? '');

    $info = getInfo($conn);

    $pic_name = $info['profile_pic'] ?? '';
    $cv_name  = $info['cv_file'] ?? '';

    /* Profile Picture */
    if (
        isset($_FILES['profile_pic']) &&
        !empty($_FILES['profile_pic']['name']) &&
        $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK
    ) {
        $pic_name = time() . '_' . basename($_FILES['profile_pic']['name']);

        move_uploaded_file(
            $_FILES['profile_pic']['tmp_name'],
            $upload_dir . $pic_name
        );
    }

    /* CV */
    if (
        isset($_FILES['cv_file']) &&
        !empty($_FILES['cv_file']['name']) &&
        $_FILES['cv_file']['error'] === UPLOAD_ERR_OK
    ) {
        $cv_name = time() . '_' . basename($_FILES['cv_file']['name']);

        move_uploaded_file(
            $_FILES['cv_file']['tmp_name'],
            $upload_dir . $cv_name
        );
    }

    if (
        updateInfo(
            $conn,
            $name,
            $title,
            $bio,
            $pic_name,
            $cv_name
        )
    ) {
        $msg = "Profile information updated successfully!";
    }
}


/* =========================================================
   2. ADD SKILL
========================================================= */
if (isset($_POST['add_skill'])) {

    $skill = trim($_POST['skill'] ?? '');

    if ($skill !== '') {

        $stmt = $conn->prepare(
            "SELECT * FROM skills WHERE skill_name = ?"
        );

        $stmt->bind_param("s", $skill);
        $stmt->execute();

        $check = $stmt->get_result();

        if ($check->num_rows == 0) {

            if (addSkill($conn, $skill)) {
                $msg = "Skill added successfully!";
            }

        } else {

            $msg = "This skill already exists!";
        }

        $stmt->close();
    }
}


/* =========================================================
   3. ADD PROJECT
========================================================= */
if (isset($_POST['add_project'])) {

    $icon  = trim($_POST['icon'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['desc'] ?? '');
    $tags  = trim($_POST['tags'] ?? '');

    if ($title !== '') {

        $stmt = $conn->prepare(
            "SELECT * FROM projects WHERE title = ?"
        );

        $stmt->bind_param("s", $title);
        $stmt->execute();

        $check = $stmt->get_result();

        if ($check->num_rows == 0) {

            if (
                addProject(
                    $conn,
                    $icon,
                    $title,
                    $desc,
                    $tags
                )
            ) {
                $msg = "Project added successfully!";
            }

        } else {

            $msg = "This project already exists!";
        }

        $stmt->close();
    }
}


/* =========================================================
   4. ADD EXPERIENCE
========================================================= */
if (isset($_POST['add_exp'])) {

    $date = trim($_POST['date'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $desc = trim($_POST['desc'] ?? '');

    if ($role !== '') {

        $stmt = $conn->prepare(
            "SELECT * FROM experience WHERE role = ?"
        );

        $stmt->bind_param("s", $role);
        $stmt->execute();

        $check = $stmt->get_result();

        if ($check->num_rows == 0) {

            if (
                addExperience(
                    $conn,
                    $date,
                    $role,
                    $desc
                )
            ) {
                $msg = "Experience added successfully!";
            }

        } else {

            $msg = "This experience already exists!";
        }

        $stmt->close();
    }
}


/* =========================================================
   5. DELETE DATA
========================================================= */
if (isset($_GET['delete'])) {

    $table = $_GET['table'] ?? '';
    $id    = intval($_GET['id'] ?? 0);
    $tab   = $_GET['tab'] ?? 'info';

    if ($id > 0 && $table !== '') {

        deleteData(
            $conn,
            $table,
            $id
        );
    }

    header(
        "Location: dashboard.php?tab=" .
        urlencode($tab)
    );

    exit;
}


/* =========================================================
   6. CHANGE PASSWORD
========================================================= */
if (isset($_POST['change_pass'])) {

    $current = $_POST['current_pass'] ?? '';
    $new     = $_POST['new_pass'] ?? '';
    $confirm = $_POST['confirm_pass'] ?? '';

    if ($new !== $confirm) {

        $msg = "New password and confirmation password do not match.";

    } elseif (!checkPassword($conn, 'admin', $current)) {

        $msg = "Current password is incorrect.";

    } else {

        if (updatePassword($conn, 'admin', $new)) {
            $msg = "Password updated successfully!";
        }
    }
}


/* =========================================================
   7. UPDATE CONTACT INFORMATION
========================================================= */
if (isset($_POST['update_contact'])) {

    if (
        isset($_POST['id']) &&
        isset($_POST['value']) &&
        isset($_POST['link'])
    ) {

        foreach ($_POST['id'] as $key => $id) {

            $id = intval($id);

            $value = $_POST['value'][$key] ?? '';
            $link  = $_POST['link'][$key] ?? '';

            updateContact(
                $conn,
                $id,
                $value,
                $link
            );
        }

        $msg = "Contact information updated successfully!";
    }
}


/* =========================================================
   8. GET DATA
========================================================= */
$info = getInfo($conn);

$active_tab = $_GET['tab'] ?? 'info';

$allowed_tabs = [
    'info',
    'skills',
    'projects',
    'experience',
    'password'
];

if (!in_array($active_tab, $allowed_tabs)) {
    $active_tab = 'info';
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

<title>Admin Dashboard</title>

<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
    rel="stylesheet"
>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
>

<style>

/* =========================================================
   VARIABLES
========================================================= */

:root {

    --navy: #0a2540;

    --navy-light: #123b63;

    --blue: #1e3a8a;

    --baby: #e0f2fe;

    --baby-dark: #bae6fd;

    --text-dark: #0a2540;

    --text-muted: #526579;

    --white: #ffffff;

    --danger: #ef4444;

    --success: #22c55e;

    --shadow:
        0 8px 25px rgba(10, 37, 64, 0.08);
}


/* =========================================================
   RESET
========================================================= */

* {
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {

    margin: 0;

    background: var(--baby);

    color: var(--text-dark);

    font-family: 'Inter', sans-serif;

    min-height: 100vh;

    overflow-x: hidden;
}

a {
    -webkit-tap-highlight-color: transparent;
}


/* =========================================================
   CONTAINER
========================================================= */

.container {

    width: 100%;

    max-width: 1100px;

    margin: 0 auto;

    padding-left: 20px;

    padding-right: 20px;
}


/* =========================================================
   HEADER
========================================================= */

.header {

    background: var(--navy);

    border-bottom: 2px solid var(--baby-dark);

    position: sticky;

    top: 0;

    z-index: 1000;

    box-shadow:
        0 4px 15px rgba(10, 37, 64, 0.15);
}


/* =========================================================
   NAVIGATION
========================================================= */

.nav {

    min-height: 70px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    padding-top: 10px;

    padding-bottom: 10px;
}


/* LOGO */

.logo {

    font-size: 20px;

    font-weight: 800;

    color: var(--white);

    white-space: nowrap;

    display: flex;

    align-items: center;

    gap: 8px;
}

.logo i {
    color: var(--baby-dark);
}


/* NAV LINKS */

.nav-menu {

    list-style: none;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 5px;

    margin: 0;

    padding: 0;
}

.nav-menu li {
    margin: 0;
}

.nav-menu a {

    color: var(--baby);

    text-decoration: none;

    font-weight: 700;

    font-size: 14px;

    padding: 9px 11px;

    border-radius: 8px;

    display: block;

    transition: 0.25s ease;

    white-space: nowrap;
}

.nav-menu a.active,
.nav-menu a:hover {

    background: var(--baby);

    color: var(--navy);
}


/* LOGOUT */

.logout {

    color: var(--white);

    text-decoration: none;

    background: var(--danger);

    padding: 9px 14px;

    border-radius: 8px;

    font-weight: 700;

    font-size: 14px;

    white-space: nowrap;

    transition: 0.25s ease;
}

.logout:hover {
    background: #dc2626;
}


/* MOBILE MENU BUTTON */

.menu-toggle {

    display: none;

    border: 0;

    background: transparent;

    color: var(--white);

    font-size: 24px;

    cursor: pointer;

    padding: 5px;

    line-height: 1;
}


/* =========================================================
   CONTENT
========================================================= */

.content {

    padding-top: 30px;

    padding-bottom: 40px;
}


/* =========================================================
   ALERT
========================================================= */

.alert {

    background: var(--success);

    color: var(--white);

    padding: 13px 15px;

    border-radius: 10px;

    margin-bottom: 20px;

    border-left: 5px solid #16a34a;

    font-weight: 600;

    line-height: 1.5;
}

.alert.error {

    background: var(--danger);

    border-left-color: #dc2626;
}


/* =========================================================
   MAIN BOX
========================================================= */

.box,
.card {

    background: var(--white);

    padding: 28px;

    border-radius: 16px;

    border: 2px solid var(--baby-dark);

    box-shadow: var(--shadow);

    width: 100%;
}


/* =========================================================
   TABS
========================================================= */

.tab-content {
    display: none;
}

.tab-content.active {

    display: block;

    animation: fadeIn 0.35s ease;
}

@keyframes fadeIn {

    from {

        opacity: 0;

        transform: translateY(8px);
    }

    to {

        opacity: 1;

        transform: translateY(0);
    }
}


/* =========================================================
   HEADINGS
========================================================= */

.box h2 {

    color: var(--navy);

    margin-top: 0;

    margin-bottom: 22px;

    font-size: 23px;

    line-height: 1.3;

    font-weight: 800;
}

.box h3,
.card h3 {

    color: var(--navy);

    font-size: 16px;

    font-weight: 700;
}


/* =========================================================
   FORMS
========================================================= */

form {
    width: 100%;
}

input,
textarea {

    width: 100%;

    padding: 13px 14px;

    margin: 7px 0;

    background: var(--baby);

    border: 2px solid var(--baby-dark);

    color: var(--text-dark);

    border-radius: 9px;

    font-family: inherit;

    font-size: 14px;

    font-weight: 600;

    transition: 0.25s ease;

    display: block;
}

textarea {

    resize: vertical;

    min-height: 100px;

    line-height: 1.5;
}

input:focus,
textarea:focus {

    outline: none;

    border-color: var(--navy);

    box-shadow:
        0 0 0 3px rgba(10, 37, 64, 0.1);
}

input[type="file"] {

    padding: 10px;

    cursor: pointer;
}

label {

    display: block;

    font-size: 14px;

    color: var(--navy);

    font-weight: 700;

    margin-top: 12px;
}


/* =========================================================
   BUTTON
========================================================= */

.btn {

    padding: 13px 16px;

    background: var(--navy);

    color: var(--white);

    border: none;

    font-family: inherit;

    font-size: 14px;

    font-weight: 800;

    border-radius: 9px;

    cursor: pointer;

    width: 100%;

    margin-top: 10px;

    min-height: 46px;

    transition: 0.25s ease;
}

.btn:hover {

    transform: translateY(-2px);

    background: var(--blue);

    box-shadow:
        0 5px 15px rgba(10, 37, 64, 0.2);
}

.btn:active {
    transform: translateY(0);
}


/* =========================================================
   PROFILE
========================================================= */

.profile-pic {

    width: 105px;

    height: 105px;

    border-radius: 50%;

    object-fit: cover;

    border: 3px solid var(--navy);

    margin-bottom: 15px;

    box-shadow:
        0 0 15px rgba(10, 37, 64, 0.2);

    display: block;
}


/* CV LINK */

.cv-link {

    display: inline-block;

    margin-top: 5px;

    color: var(--navy);

    font-weight: 700;

    text-decoration: none;

    word-break: break-word;
}

.cv-link:hover {
    text-decoration: underline;
}


/* =========================================================
   ITEMS
========================================================= */

.item {

    background: var(--baby);

    padding: 13px 15px;

    border-radius: 9px;

    margin: 9px 0;

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 15px;

    border: 2px solid var(--baby-dark);

    font-weight: 600;

    min-height: 48px;

    overflow-wrap: anywhere;
}

.item a {

    color: var(--danger);

    text-decoration: none;

    font-weight: 800;

    flex-shrink: 0;

    padding: 5px 8px;
}

.item a:hover {
    color: #dc2626;
}


/* =========================================================
   CONTACT CARD
========================================================= */

.contact-card {

    margin-top: 25px;
}

.contact-card h3 {

    margin-top: 0;

    margin-bottom: 20px;

    font-size: 20px;
}

.contact-group {

    margin-bottom: 18px;
}

.contact-group label {

    margin-bottom: 4px;
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 900px) {

    .container {
        padding-left: 16px;
        padding-right: 16px;
    }

    .nav {
        gap: 12px;
    }

    .nav-menu {
        gap: 2px;
    }

    .nav-menu a {
        font-size: 13px;
        padding: 8px 8px;
    }

    .logout {
        padding: 8px 10px;
        font-size: 13px;
    }
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 700px) {

    .header {
        position: sticky;
    }

    .nav {

        min-height: 64px;

        flex-wrap: wrap;

        padding-top: 12px;

        padding-bottom: 12px;
    }

    .logo {
        font-size: 18px;
    }

    .menu-toggle {
        display: block;
        margin-left: auto;
    }

    .nav-menu {

        display: none;

        width: 100%;

        flex-direction: column;

        align-items: stretch;

        gap: 4px;

        padding-top: 10px;

        border-top: 1px solid rgba(224, 242, 254, 0.2);

        order: 3;
    }

    .nav-menu.show {
        display: flex;
    }

    .nav-menu li {
        width: 100%;
    }

    .nav-menu a {

        width: 100%;

        padding: 12px 14px;

        font-size: 14px;

        border-radius: 8px;
    }

    .logout {

        display: none;

        width: 100%;

        text-align: center;

        margin-top: 5px;

        padding: 12px;

        order: 4;
    }

    .logout.show {
        display: block;
    }

    .content {

        padding-top: 18px;

        padding-bottom: 25px;
    }

    .box,
    .card {

        padding: 20px 16px;

        border-radius: 13px;
    }

    .box h2 {

        font-size: 20px;

        margin-bottom: 18px;
    }

    .box h3,
    .card h3 {
        font-size: 15px;
    }

    input,
    textarea {

        padding: 12px;

        font-size: 14px;
    }

    .btn {

        min-height: 48px;

        font-size: 14px;
    }

    .item {

        padding: 12px;

        gap: 10px;

        font-size: 14px;
    }

    .profile-pic {

        width: 90px;

        height: 90px;
    }

    .alert {

        font-size: 13px;

        padding: 11px 12px;
    }
}


/* =========================================================
   SMALL PHONES
========================================================= */

@media (max-width: 400px) {

    .container {

        padding-left: 12px;

        padding-right: 12px;
    }

    .logo {

        font-size: 16px;
    }

    .box,
    .card {

        padding: 17px 13px;
    }

    .box h2 {

        font-size: 18px;
    }

    input,
    textarea {

        font-size: 13px;
    }

    .item {

        font-size: 13px;
    }
}

</style>

</head>

<body>


<!-- =======================================================
     HEADER
======================================================= -->

<header class="header">

    <div class="container nav">

        <div class="logo">

            <i class="fa-solid fa-user-shield"></i>

            Admin Panel

        </div>


        <!-- Mobile Menu Button -->

        <button
            type="button"
            class="menu-toggle"
            id="menuToggle"
            aria-label="Open navigation"
            aria-expanded="false"
        >

            <i class="fa-solid fa-bars"></i>

        </button>


        <!-- Navigation -->

        <ul class="nav-menu" id="navMenu">

            <li>
                <a
                    href="?tab=info"
                    class="<?= $active_tab == 'info' ? 'active' : '' ?>"
                >
                    <i class="fa-solid fa-user"></i>
                    Profile
                </a>
            </li>

            <li>
                <a
                    href="?tab=skills"
                    class="<?= $active_tab == 'skills' ? 'active' : '' ?>"
                >
                    <i class="fa-solid fa-code"></i>
                    Skills
                </a>
            </li>

            <li>
                <a
                    href="?tab=projects"
                    class="<?= $active_tab == 'projects' ? 'active' : '' ?>"
                >
                    <i class="fa-solid fa-folder"></i>
                    Projects
                </a>
            </li>

            <li>
                <a
                    href="?tab=experience"
                    class="<?= $active_tab == 'experience' ? 'active' : '' ?>"
                >
                    <i class="fa-solid fa-briefcase"></i>
                    Experience
                </a>
            </li>

            <li>
                <a
                    href="?tab=password"
                    class="<?= $active_tab == 'password' ? 'active' : '' ?>"
                >
                    <i class="fa-solid fa-lock"></i>
                    Password
                </a>
            </li>

        </ul>


        <!-- Logout -->

        <a
            href="logout.php"
            class="logout"
            id="logoutButton"
        >

            <i class="fa-solid fa-right-from-bracket"></i>

            Logout

        </a>

    </div>

</header>


<!-- =======================================================
     MAIN CONTENT
======================================================= -->

<div class="container content">


    <!-- MESSAGE -->

    <?php if ($msg): ?>

        <div class="alert">

            <?= htmlspecialchars($msg) ?>

        </div>

    <?php endif; ?>


    <!-- ===================================================
         MAIN BOX
    =================================================== -->

    <div class="box">


        <!-- =================================================
             PROFILE
        ================================================= -->

        <div
            class="tab-content <?= $active_tab == 'info' ? 'active' : '' ?>"
        >

            <h2>

                <i class="fa-solid fa-id-card"></i>

                Update Profile Info

            </h2>


            <?php if (!empty($info['profile_pic'])): ?>

                <img
                    src="<?= htmlspecialchars($upload_dir . $info['profile_pic']) ?>"
                    class="profile-pic"
                    alt="Profile Picture"
                >

            <?php endif; ?>


            <form
                method="POST"
                enctype="multipart/form-data"
            >

                <label>Name</label>

                <input
                    name="name"
                    value="<?= htmlspecialchars($info['name'] ?? '') ?>"
                    placeholder="Enter your name"
                >


                <label>Professional Title</label>

                <input
                    name="title"
                    value="<?= htmlspecialchars($info['title'] ?? '') ?>"
                    placeholder="e.g. Software Engineer"
                >


                <label>Bio</label>

                <textarea
                    name="bio"
                    rows="5"
                    placeholder="Write your professional bio"
                ><?= htmlspecialchars($info['bio'] ?? '') ?></textarea>


                <label>Profile Picture</label>

                <input
                    type="file"
                    name="profile_pic"
                    accept="image/*"
                >


                <label>Upload CV (PDF)</label>

                <input
                    type="file"
                    name="cv_file"
                    accept=".pdf,application/pdf"
                >


                <?php if (!empty($info['cv_file'])): ?>

                    <a
                        href="<?= htmlspecialchars($upload_dir . $info['cv_file']) ?>"
                        target="_blank"
                        class="cv-link"
                    >
                        <i class="fa-solid fa-file-pdf"></i>
                        View Current CV
                    </a>

                <?php endif; ?>


                <button
                    type="submit"
                    class="btn"
                    name="update_info"
                >

                    <i class="fa-solid fa-save"></i>

                    Update Information

                </button>

            </form>

        </div>


        <!-- =================================================
             SKILLS
        ================================================= -->

        <div
            class="tab-content <?= $active_tab == 'skills' ? 'active' : '' ?>"
        >

            <h2>

                <i class="fa-solid fa-code"></i>

                Manage Skills

            </h2>


            <form method="POST">

                <label>Add New Skill</label>

                <input
                    name="skill"
                    placeholder="e.g. PHP, MySQL, Flutter"
                    required
                >

                <button
                    type="submit"
                    class="btn"
                    name="add_skill"
                >

                    <i class="fa-solid fa-plus"></i>

                    Add Skill

                </button>

            </form>


            <h3 style="margin-top:25px;">

                Your Skills

            </h3>


            <?php

            $skills = getSkills($conn);

            while ($s = $skills->fetch_assoc()):

            ?>

                <div class="item">

                    <span>
                        <?= htmlspecialchars($s['skill_name']) ?>
                    </span>

                    <a
                        href="?delete=1&table=skills&id=<?= intval($s['id']) ?>&tab=skills"
                        onclick="return confirm('Delete this skill?');"
                        title="Delete Skill"
                    >

                        <i class="fa-solid fa-trash"></i>

                    </a>

                </div>

            <?php endwhile; ?>

        </div>


        <!-- =================================================
             PROJECTS
        ================================================= -->

        <div
            class="tab-content <?= $active_tab == 'projects' ? 'active' : '' ?>"
        >

            <h2>

                <i class="fa-solid fa-folder-open"></i>

                Manage Projects

            </h2>


            <form method="POST">

                <label>Icon</label>

                <input
                    name="icon"
                    placeholder="e.g. code, book, globe"
                    required
                >


                <label>Project Title</label>

                <input
                    name="title"
                    placeholder="Project Title"
                    required
                >


                <label>Description</label>

                <textarea
                    name="desc"
                    rows="5"
                    placeholder="Project description"
                ></textarea>


                <label>Tags</label>

                <input
                    name="tags"
                    placeholder="PHP, MySQL, JavaScript"
                >


                <button
                    type="submit"
                    class="btn"
                    name="add_project"
                >

                    <i class="fa-solid fa-plus"></i>

                    Add Project

                </button>

            </form>


            <h3 style="margin-top:25px;">

                Your Projects

            </h3>


            <?php

            $projects = getProjects($conn);

            while ($p = $projects->fetch_assoc()):

                $icon = trim($p['icon'] ?? '');

                $icon = str_replace('fa-', '', $icon);

                if ($icon === '') {
                    $icon = 'code';
                }

            ?>

                <div class="item">

                    <span>

                        <i
                            class="fa-solid fa-<?= htmlspecialchars($icon) ?>"
                            style="color:var(--navy); margin-right:8px;"
                        ></i>

                        <?= htmlspecialchars($p['title']) ?>

                    </span>


                    <a
                        href="?delete=1&table=projects&id=<?= intval($p['id']) ?>&tab=projects"
                        onclick="return confirm('Delete this project?');"
                        title="Delete Project"
                    >

                        <i class="fa-solid fa-trash"></i>

                    </a>

                </div>

            <?php endwhile; ?>

        </div>


        <!-- =================================================
             EXPERIENCE
        ================================================= -->

        <div
            class="tab-content <?= $active_tab == 'experience' ? 'active' : '' ?>"
        >

            <h2>

                <i class="fa-solid fa-briefcase"></i>

                Manage Experience

            </h2>


            <form method="POST">

                <label>Date</label>

                <input
                    name="date"
                    placeholder="2023 - 2024"
                    required
                >


                <label>Job Role</label>

                <input
                    name="role"
                    placeholder="Job Role"
                    required
                >


                <label>Description</label>

                <textarea
                    name="desc"
                    rows="5"
                    placeholder="Describe your experience"
                ></textarea>


                <button
                    type="submit"
                    class="btn"
                    name="add_exp"
                >

                    <i class="fa-solid fa-plus"></i>

                    Add Experience

                </button>

            </form>


            <h3 style="margin-top:25px;">

                Your Experience

            </h3>


            <?php

            $exp = getExperience($conn);

            while ($e = $exp->fetch_assoc()):

            ?>

                <div class="item">

                    <span>

                        <?= htmlspecialchars($e['role']) ?>

                    </span>


                    <a
                        href="?delete=1&table=experience&id=<?= intval($e['id']) ?>&tab=experience"
                        onclick="return confirm('Delete this experience?');"
                        title="Delete Experience"
                    >

                        <i class="fa-solid fa-trash"></i>

                    </a>

                </div>

            <?php endwhile; ?>

        </div>


        <!-- =================================================
             PASSWORD
        ================================================= -->

        <div
            class="tab-content <?= $active_tab == 'password' ? 'active' : '' ?>"
        >

            <h2>

                <i class="fa-solid fa-lock"></i>

                Change Password

            </h2>


            <form method="POST">

                <label>Current Password</label>

                <input
                    type="password"
                    name="current_pass"
                    placeholder="Current Password"
                    required
                >


                <label>New Password</label>

                <input
                    type="password"
                    name="new_pass"
                    placeholder="New Password"
                    required
                >


                <label>Confirm New Password</label>

                <input
                    type="password"
                    name="confirm_pass"
                    placeholder="Confirm New Password"
                    required
                >


                <button
                    type="submit"
                    class="btn"
                    name="change_pass"
                >

                    <i class="fa-solid fa-key"></i>

                    Update Password

                </button>

            </form>

        </div>

    </div>


    <!-- ===================================================
         CONTACT INFORMATION
    =================================================== -->

    <div class="card contact-card">

        <h3>

            <i class="fa-solid fa-address-book"></i>

            Edit Contact Information

        </h3>


        <?php

        $contact = getContact($conn);

        ?>


        <form method="POST">

            <?php while ($c = $contact->fetch_assoc()): ?>

                <div class="contact-group">

                    <input
                        type="hidden"
                        name="id[]"
                        value="<?= intval($c['id']) ?>"
                    >


                    <label>

                        <?= htmlspecialchars($c['type']) ?>

                    </label>


                    <input
                        type="text"
                        name="value[]"
                        value="<?= htmlspecialchars($c['value']) ?>"
                        placeholder="Display Text"
                        required
                    >


                    <input
                        type="text"
                        name="link[]"
                        value="<?= htmlspecialchars($c['link']) ?>"
                        placeholder="Link: mailto: or https://"
                        required
                    >

                </div>

            <?php endwhile; ?>


            <button
                type="submit"
                name="update_contact"
                class="btn"
            >

                <i class="fa-solid fa-save"></i>

                Update Contacts

            </button>

        </form>

    </div>

</div>


<!-- =======================================================
     MOBILE MENU JAVASCRIPT
======================================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const menuToggle =
        document.getElementById("menuToggle");

    const navMenu =
        document.getElementById("navMenu");

    const logoutButton =
        document.getElementById("logoutButton");


    menuToggle.addEventListener("click", function () {

        navMenu.classList.toggle("show");

        logoutButton.classList.toggle("show");


        const isOpen =
            navMenu.classList.contains("show");


        menuToggle.setAttribute(
            "aria-expanded",
            isOpen
        );


        menuToggle.innerHTML = isOpen
            ? '<i class="fa-solid fa-xmark"></i>'
            : '<i class="fa-solid fa-bars"></i>';

    });


    /* Close menu after selecting a page */

    navMenu.querySelectorAll("a").forEach(function (link) {

        link.addEventListener("click", function () {

            navMenu.classList.remove("show");

            logoutButton.classList.remove("show");

            menuToggle.setAttribute(
                "aria-expanded",
                "false"
            );

            menuToggle.innerHTML =
                '<i class="fa-solid fa-bars"></i>';

        });

    });

});

</script>


</body>

</html>