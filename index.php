<?php
require_once 'admin/includes/config.php';
require_once 'admin/includes/functions.php';

$info = getInfo($conn);
$skills = getSkills($conn);
$projects = getProjects($conn);
$experience = getExperience($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($info['name'] ?? '') ?>
        -
        <?= htmlspecialchars($info['title'] ?? '') ?>
    </title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

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

    --iceblue: #38bdf8;

    --white: #ffffff;

    --muted: #526579;
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

    scroll-behavior: smooth;

    scroll-padding-top: 80px;
}


body {

    background: var(--baby);

    color: var(--text-dark);

    font-family: 'Inter', sans-serif;

    overflow-x: hidden;

    width: 100%;
}


img {

    max-width: 100%;
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

header {

    background: var(--navy);

    position: sticky;

    top: 0;

    z-index: 1000;

    border-bottom: 2px solid var(--iceblue);

    box-shadow:
        0 4px 15px rgba(10, 37, 64, 0.15);
}


/* =========================================================
   NAVIGATION
========================================================= */

nav {

    display: flex;

    justify-content: space-between;

    align-items: center;

    min-height: 70px;

    gap: 20px;
}


/* LOGO */

.logo {

    font-size: 22px;

    font-weight: 800;

    color: var(--text-light);

    white-space: nowrap;
}


.logo span {

    color: var(--iceblue);
}


/* DESKTOP NAV */

nav ul {

    display: flex;

    align-items: center;

    list-style: none;

    gap: 22px;

    margin: 0;

    padding: 0;
}


nav ul a {

    color: var(--text-light);

    text-decoration: none;

    font-weight: 600;

    font-size: 14px;

    transition: 0.3s;

    padding: 8px 0;

    white-space: nowrap;
}


nav ul a:hover {

    color: var(--iceblue);
}


/* MOBILE MENU BUTTON */

.menu-toggle {

    display: none;

    background: transparent;

    border: none;

    color: var(--white);

    font-size: 24px;

    cursor: pointer;

    padding: 8px;

    line-height: 1;
}


/* =========================================================
   HERO
========================================================= */

.hero {

    display: grid;

    grid-template-columns: 1.2fr 1fr;

    gap: 40px;

    align-items: center;

    padding: 100px 0;
}


.hero-text h4 {

    color: var(--navy);

    font-family: monospace;

    font-size: 16px;

    font-weight: 800;
}


.hero-text h1 {

    font-size: 50px;

    color: var(--navy);

    margin: 10px 0;

    line-height: 1.1;

    overflow-wrap: anywhere;
}


.hero-text h1 span {

    color: var(--iceblue);
}


.hero-text h2 {

    font-size: 28px;

    color: var(--navy-light);

    margin-bottom: 15px;

    line-height: 1.3;
}


.hero-text p {

    margin-bottom: 25px;

    line-height: 1.7;

    font-size: 17px;

    overflow-wrap: anywhere;
}


/* HERO BUTTONS */

.hero-buttons {

    display: flex;

    gap: 15px;

    flex-wrap: wrap;
}


.btn,
.btn-outline {

    padding: 12px 20px;

    border-radius: 8px;

    text-decoration: none;

    font-weight: 800;

    transition: 0.3s;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    min-height: 46px;
}


.btn {

    background: var(--navy);

    color: var(--text-light);
}


.btn:hover {

    box-shadow:
        0 0 15px rgba(10, 37, 64, 0.35);

    transform: translateY(-2px);
}


.btn-outline {

    border: 2px solid var(--navy);

    color: var(--navy);

    background: transparent;
}


.btn-outline:hover {

    background: var(--navy);

    color: var(--text-light);
}


/* =========================================================
   HERO IMAGE
========================================================= */

.hero-img {

    width: 100%;

    max-width: 350px;

    aspect-ratio: 1 / 1;

    border-radius: 16px;

    overflow: hidden;

    border: 3px solid var(--navy);

    box-shadow:
        0 0 25px rgba(10, 37, 64, 0.3);

    margin: 0 auto;

    background: var(--white);
}


.hero-img img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;
}


.hero-img i {

    font-size: 100px;

    color: var(--baby-dark);

    width: 100%;

    height: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    background: var(--white);
}


/* =========================================================
   SECTIONS
========================================================= */

section {

    padding: 80px 0;
}


/* SECTION TITLE */

.section-title {

    font-size: 28px;

    color: var(--navy);

    margin-bottom: 40px;

    display: flex;

    align-items: center;

    gap: 15px;

    font-weight: 800;

    line-height: 1.3;
}


.section-title::before {

    content: '0' counter(section) '. ';

    counter-increment: section;

    color: var(--navy);

    font-family: monospace;

    font-size: 20px;

    flex-shrink: 0;
}


body {

    counter-reset: section;
}


/* =========================================================
   ABOUT
========================================================= */

.about-layout {

    display: grid;

    grid-template-columns: 2fr 1fr;

    gap: 40px;

    align-items: start;
}


.about-text h3 {

    color: var(--navy);

    margin-bottom: 15px;

    font-size: 22px;

    line-height: 1.4;
}


.about-text p {

    color: var(--navy-light);

    line-height: 1.8;

    font-size: 16px;

    overflow-wrap: anywhere;
}


.about-grid {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 15px;
}


.about-card {

    background: var(--white);

    padding: 20px;

    border-radius: 8px;

    border: 2px solid var(--baby-dark);

    text-align: center;

    font-weight: 700;

    color: var(--navy);

    transition: 0.3s;
}


.about-card i {

    color: var(--navy);

    margin-right: 8px;
}


.about-card:hover {

    border-color: var(--navy);

    transform: translateY(-3px);
}


/* =========================================================
   EXPERIENCE
========================================================= */

.timeline {

    border-left: 3px solid var(--baby-dark);

    padding-left: 30px;
}


.timeline-item {

    margin-bottom: 30px;

    position: relative;
}


.timeline-item::before {

    content: '';

    position: absolute;

    left: -38px;

    top: 5px;

    width: 14px;

    height: 14px;

    border-radius: 50%;

    background: var(--navy);

    border: 3px solid var(--baby);
}


.timeline-item .date {

    color: var(--navy);

    font-family: monospace;

    font-size: 14px;

    font-weight: 700;
}


.timeline-item h3 {

    color: var(--navy);

    margin: 5px 0;

    line-height: 1.4;
}


.timeline-item > p:last-child {

    color: var(--navy-light);

    line-height: 1.7;

    overflow-wrap: anywhere;
}


/* =========================================================
   SKILLS
========================================================= */

.skills-grid {

    display: grid;

    grid-template-columns:
        repeat(auto-fit, minmax(150px, 1fr));

    gap: 15px;
}


.skill-item {

    background: var(--white);

    padding: 15px;

    border-radius: 8px;

    border: 2px solid var(--baby-dark);

    text-align: center;

    font-weight: 700;

    color: var(--navy);

    transition: 0.3s;

    overflow-wrap: anywhere;
}


.skill-item:hover {

    border-color: var(--navy);

    background: var(--navy);

    color: var(--white);
}


/* =========================================================
   PROJECTS
========================================================= */

.projects-grid {

    display: grid;

    grid-template-columns:
        repeat(auto-fit, minmax(280px, 1fr));

    gap: 20px;
}


.project-card {

    background: var(--white);

    padding: 25px;

    border-radius: 12px;

    border: 2px solid var(--baby-dark);

    transition: 0.3s;

    min-width: 0;
}


.project-card:hover {

    transform: translateY(-5px);

    border-color: var(--navy);
}


.project-card i {

    font-size: 30px;

    color: var(--navy);

    margin-bottom: 15px;
}


.project-card h3 {

    color: var(--navy);

    margin-bottom: 10px;

    line-height: 1.4;
}


.project-card p {

    line-height: 1.6;

    overflow-wrap: anywhere;
}


.project-card .tags {

    color: var(--navy);

    font-size: 12px;

    font-family: monospace;

    margin-top: 10px;

    font-weight: 700;
}


/* =========================================================
   CONTACT
========================================================= */

.contact-wrapper {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 40px;

    align-items: start;
}


.contact-intro h3 {

    color: var(--navy);

    font-size: 28px;

    margin-bottom: 15px;

    line-height: 1.3;
}


.contact-intro p {

    color: var(--navy-light);

    font-size: 16px;

    line-height: 1.7;
}


.contact-info-item {

    display: flex;

    align-items: center;

    gap: 12px;

    background: var(--white);

    padding: 15px 20px;

    border-radius: 8px;

    margin-bottom: 12px;

    border: 2px solid var(--baby-dark);

    color: var(--navy);

    font-weight: 600;

    text-decoration: none;

    transition: 0.3s;

    min-width: 0;

    overflow-wrap: anywhere;
}


.contact-info-item:hover {

    border-color: var(--navy);

    background: var(--navy);

    color: var(--white);

    transform: translateX(5px);
}


.contact-info-item i {

    color: var(--navy);

    font-size: 20px;

    width: 20px;

    min-width: 20px;

    text-align: center;

    transition: 0.3s;
}


.contact-info-item:hover i {

    color: var(--white);
}


/* =========================================================
   FOOTER
========================================================= */

footer {

    text-align: center;

    padding: 30px 20px;

    background: var(--navy);

    color: var(--text-light);

    border-top: 2px solid var(--iceblue);
}


footer p {

    color: var(--baby-dark);

    font-size: 12px;

    line-height: 1.5;
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 900px) {

    .container {

        padding-left: 18px;

        padding-right: 18px;
    }


    nav ul {

        gap: 14px;
    }


    nav ul a {

        font-size: 13px;
    }


    .hero {

        gap: 30px;

        padding: 80px 0;
    }


    .hero-text h1 {

        font-size: 42px;
    }


    .hero-text h2 {

        font-size: 24px;
    }


    .about-layout {

        grid-template-columns: 1fr;

        gap: 30px;
    }


    .contact-wrapper {

        gap: 25px;
    }
}


/* =========================================================
   MOBILE NAVIGATION
========================================================= */

@media (max-width: 768px) {

    header {

        position: sticky;

        top: 0;
    }


    nav {

        min-height: 64px;

        flex-wrap: wrap;

        padding: 10px 0;
    }


    .logo {

        font-size: 19px;
    }


    .menu-toggle {

        display: block;

        margin-left: auto;
    }


    nav ul {

        display: none;

        width: 100%;

        flex-direction: column;

        align-items: stretch;

        gap: 2px;

        order: 3;

        padding: 10px 0 5px;

        border-top:
            1px solid rgba(224, 242, 254, 0.2);

        margin-top: 5px;
    }


    nav ul.show {

        display: flex;
    }


    nav ul li {

        width: 100%;
    }


    nav ul a {

        display: block;

        width: 100%;

        padding: 12px 10px;

        border-radius: 7px;

        font-size: 14px;
    }


    nav ul a:hover {

        background: rgba(56, 189, 248, 0.12);
    }


    .hero {

        grid-template-columns: 1fr;

        text-align: center;

        padding: 60px 0 50px;

        gap: 35px;
    }


    .hero-text {

        order: 1;
    }


    .hero-img {

        order: 2;

        width: min(80vw, 300px);
    }


    .hero-text h4 {

        font-size: 14px;
    }


    .hero-text h1 {

        font-size: clamp(32px, 9vw, 42px);

        margin: 8px 0;
    }


    .hero-text h2 {

        font-size: clamp(21px, 6vw, 26px);

        margin-bottom: 14px;
    }


    .hero-text p {

        font-size: 15px;

        line-height: 1.7;
    }


    .hero-buttons {

        justify-content: center;

        width: 100%;
    }


    .btn,
    .btn-outline {

        min-height: 46px;

        padding: 11px 17px;
    }


    section {

        padding: 55px 0;
    }


    .section-title {

        font-size: 23px;

        margin-bottom: 28px;

        gap: 10px;
    }


    .section-title::before {

        font-size: 16px;
    }


    .about-layout {

        grid-template-columns: 1fr;

        gap: 25px;
    }


    .about-grid {

        grid-template-columns: 1fr 1fr;

        gap: 10px;
    }


    .about-card {

        padding: 16px 10px;

        font-size: 13px;
    }


    .about-card i {

        display: block;

        margin: 0 0 7px;
    }


    .timeline {

        padding-left: 23px;
    }


    .timeline-item::before {

        left: -31px;

        width: 12px;

        height: 12px;
    }


    .skills-grid {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 10px;
    }


    .skill-item {

        padding: 14px 8px;

        font-size: 13px;
    }


    .projects-grid {

        grid-template-columns: 1fr;

        gap: 15px;
    }


    .project-card {

        padding: 20px;
    }


    .contact-wrapper {

        grid-template-columns: 1fr;

        gap: 25px;
    }


    .contact-intro h3 {

        font-size: 24px;
    }


    .contact-info-item {

        padding: 14px;

        font-size: 13px;

        transform: none !important;
    }
}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 480px) {

    .container {

        padding-left: 13px;

        padding-right: 13px;
    }


    .logo {

        font-size: 17px;
    }


    .hero {

        padding-top: 45px;

        padding-bottom: 40px;

        gap: 28px;
    }


    .hero-img {

        width: min(75vw, 260px);

        border-radius: 13px;
    }


    .hero-img i {

        font-size: 70px;
    }


    .hero-buttons {

        flex-direction: column;

        align-items: stretch;

        gap: 10px;
    }


    .btn,
    .btn-outline {

        width: 100%;
    }


    section {

        padding: 45px 0;
    }


    .section-title {

        font-size: 21px;

        margin-bottom: 24px;
    }


    .about-grid {

        grid-template-columns: 1fr 1fr;
    }


    .about-card {

        padding: 14px 7px;

        font-size: 12px;
    }


    .about-text h3 {

        font-size: 19px;
    }


    .about-text p {

        font-size: 14px;
    }


    .timeline {

        padding-left: 20px;
    }


    .timeline-item {

        margin-bottom: 25px;
    }


    .timeline-item::before {

        left: -28px;

        width: 10px;

        height: 10px;
    }


    .timeline-item .date {

        font-size: 12px;
    }


    .timeline-item h3 {

        font-size: 16px;
    }


    .timeline-item > p:last-child {

        font-size: 14px;
    }


    .skills-grid {

        grid-template-columns: 1fr 1fr;
    }


    .skill-item {

        min-height: 48px;

        display: flex;

        align-items: center;

        justify-content: center;
    }


    .project-card {

        padding: 18px;
    }


    .project-card i {

        font-size: 26px;
    }


    .project-card h3 {

        font-size: 17px;
    }


    .project-card p {

        font-size: 14px;
    }


    .contact-intro h3 {

        font-size: 22px;
    }


    .contact-intro p {

        font-size: 14px;
    }


    .contact-info-item {

        padding: 13px;

        gap: 9px;

        font-size: 12px;
    }


    .contact-info-item i {

        font-size: 17px;

        width: 18px;

        min-width: 18px;
    }
}


/* =========================================================
   VERY SMALL PHONES
========================================================= */

@media (max-width: 340px) {

    .container {

        padding-left: 10px;

        padding-right: 10px;
    }


    .logo {

        font-size: 15px;
    }


    .hero-text h1 {

        font-size: 29px;
    }


    .hero-text h2 {

        font-size: 19px;
    }


    .about-grid {

        grid-template-columns: 1fr;
    }


    .skills-grid {

        grid-template-columns: 1fr;
    }


    .section-title {

        font-size: 19px;
    }
}

</style>

</head>


<body>


<!-- =======================================================
     HEADER
======================================================= -->

<header>

    <div class="container">

        <nav>

            <div class="logo">

                <span>Muhammad</span>
                <?= htmlspecialchars(explode(' ', $info['name'] ?? '')[1] ?? '') ?>

            </div>


            <!-- MOBILE MENU BUTTON -->

            <button
                type="button"
                class="menu-toggle"
                id="menuToggle"
                aria-label="Open navigation"
                aria-expanded="false"
            >

                <i class="fa-solid fa-bars"></i>

            </button>


            <!-- NAVIGATION -->

            <ul id="navMenu">

                <li>
                    <a href="#home">Home</a>
                </li>

                <li>
                    <a href="#about">About</a>
                </li>

                <li>
                    <a href="#experience">Experience</a>
                </li>

                <li>
                    <a href="#skills">Skills</a>
                </li>

                <li>
                    <a href="#projects">Projects</a>
                </li>

                <li>
                    <a href="#contact">Contact</a>
                </li>

            </ul>

        </nav>

    </div>

</header>


<!-- =======================================================
     MAIN
======================================================= -->

<main class="container">


    <!-- ===================================================
         HOME
    =================================================== -->

    <section id="home" class="hero">

        <div class="hero-text">

            <h4>
                Hi, my name is
            </h4>


            <h1>

                <?= htmlspecialchars($info['name'] ?? '') ?>

            </h1>


            <h2>

                <?= htmlspecialchars($info['title'] ?? '') ?>

            </h2>


            <p>

                <?= htmlspecialchars($info['bio'] ?? '') ?>

            </p>


            <div class="hero-buttons">

                <a
                    href="#contact"
                    class="btn"
                >

                    Contact Me

                </a>


                <?php if (!empty($info['cv_file'])): ?>

                    <a
                        href="admin/uploads/<?= htmlspecialchars($info['cv_file']) ?>"
                        target="_blank"
                        class="btn-outline"
                    >

                        <i class="fa-solid fa-download"></i>

                        Download CV

                    </a>

                <?php endif; ?>

            </div>

        </div>


        <!-- PROFILE IMAGE -->

        <div class="hero-img">

            <?php if (!empty($info['profile_pic'])): ?>

                <img
                    src="admin/uploads/<?= htmlspecialchars($info['profile_pic']) ?>"
                    alt="Profile"
                >

            <?php else: ?>

                <i class="fa-solid fa-user"></i>

            <?php endif; ?>

        </div>

    </section>


    <!-- ===================================================
         ABOUT
    =================================================== -->

    <section id="about">

        <h2 class="section-title">
            About Me
        </h2>


        <div class="about-layout">

            <div class="about-text">

                <h3>

                    <?= htmlspecialchars($info['title'] ?? '') ?>

                    based in Bhakkar

                </h3>


                <p>

                    <?= htmlspecialchars($info['bio'] ?? '') ?>

                </p>

            </div>


            <div class="about-grid">

                <div class="about-card">

                    <i class="fa-solid fa-mobile-screen"></i>

                    Flutter Apps

                </div>


                <div class="about-card">

                    <i class="fa-brands fa-dart-lang"></i>

                    Dart

                </div>


                <div class="about-card">

                    <i class="fa-solid fa-bolt"></i>

                    Fast Performance

                </div>


                <div class="about-card">

                    <i class="fa-solid fa-palette"></i>

                    UI/UX Design

                </div>

            </div>

        </div>

    </section>


    <!-- ===================================================
         EXPERIENCE
    =================================================== -->

    <section id="experience">

        <h2 class="section-title">
            Experience
        </h2>


        <div class="timeline">

            <?php while ($e = $experience->fetch_assoc()): ?>

                <div class="timeline-item">

                    <p class="date">

                        <?= htmlspecialchars($e['date'] ?? '') ?>

                    </p>


                    <h3>

                        <?= htmlspecialchars($e['role'] ?? '') ?>

                    </h3>


                    <p>

                        <?= htmlspecialchars($e['description'] ?? '') ?>

                    </p>

                </div>

            <?php endwhile; ?>

        </div>

    </section>


    <!-- ===================================================
         SKILLS
    =================================================== -->

    <section id="skills">

        <h2 class="section-title">
            Technical Skills
        </h2>


        <div class="skills-grid">

            <?php while ($s = $skills->fetch_assoc()): ?>

                <div class="skill-item">

                    <?= htmlspecialchars($s['skill_name'] ?? '') ?>

                </div>

            <?php endwhile; ?>

        </div>

    </section>


    <!-- ===================================================
         PROJECTS
    =================================================== -->

    <section id="projects">

        <h2 class="section-title">
            Projects
        </h2>


        <div class="projects-grid">

            <?php while ($p = $projects->fetch_assoc()): ?>

                <div class="project-card">

                    <?php

                    $projectIcon = trim($p['icon'] ?? '');

                    $projectIcon =
                        str_replace('fa-', '', $projectIcon);

                    if ($projectIcon === '') {
                        $projectIcon = 'code';
                    }

                    ?>


                    <i
                        class="fa-solid fa-<?= htmlspecialchars($projectIcon) ?>"
                    ></i>


                    <h3>

                        <?= htmlspecialchars($p['title'] ?? '') ?>

                    </h3>


                    <p>

                        <?= htmlspecialchars($p['description'] ?? '') ?>

                    </p>


                    <p class="tags">

                        <?= htmlspecialchars($p['tags'] ?? '') ?>

                    </p>

                </div>

            <?php endwhile; ?>

        </div>

    </section>


    <!-- ===================================================
         CONTACT
    =================================================== -->

    <section id="contact">

        <h2 class="section-title">
            Contact
        </h2>


        <div class="contact-wrapper">


            <div class="contact-intro">

                <h3>
                    Let's Connect
                </h3>


                <p>

                    I am currently open to new opportunities.
                    Feel free to reach out to me.

                </p>

            </div>


            <div class="contact-info">

                <?php

                $contact = getContact($conn);

                ?>


                <?php while ($c = $contact->fetch_assoc()): ?>

                    <a
                        href="<?= htmlspecialchars($c['link'] ?? '') ?>"
                        target="_blank"
                        class="contact-info-item"
                    >

                        <i
                            class="<?= htmlspecialchars($c['icon'] ?? '') ?>"
                        ></i>


                        <span>

                            <?= htmlspecialchars($c['value'] ?? '') ?>

                        </span>

                    </a>

                <?php endwhile; ?>

            </div>

        </div>

    </section>


</main>


<!-- =======================================================
     FOOTER
======================================================= -->

<footer>

    <p>

        © 2026. All Rights Reserved.

    </p>

</footer>


<!-- =======================================================
     MOBILE NAVIGATION JAVASCRIPT
======================================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const menuToggle =
        document.getElementById("menuToggle");

    const navMenu =
        document.getElementById("navMenu");


    /* Open / Close Mobile Menu */

    menuToggle.addEventListener("click", function () {

        navMenu.classList.toggle("show");


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


    /* Close menu after clicking a link */

    navMenu
        .querySelectorAll("a")
        .forEach(function (link) {

            link.addEventListener("click", function () {

                navMenu.classList.remove("show");


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