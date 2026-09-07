<?php
/**
 * Shahbaz Portfolio - Public Portfolio Page
 * File: /shahbaz_portfolio/index.php
 *
 * This page is database-driven.
 * It reads portfolio information from:
 * admin/includes/functions.php
 */

require_once __DIR__ . '/admin/includes/functions.php';

/* ---------------------------------------------------------
   DATABASE DATA
--------------------------------------------------------- */
$info       = getInfo();
$skills     = getSkills();
$projects   = getProjects();
$education  = getEducation();
$highlights = getHighlights();
$contact    = getContact();
$dbExp      = getExperience();
$languages  = getLanguages();
$certificates = getCertificates();

/* ---------------------------------------------------------
   FALLBACK PROFILE DATA
   Used only when the database does not yet contain data.
--------------------------------------------------------- */
$profile = [
    'name'       => 'Muhammad Shahbaz',
    'title'      => 'Flutter Developer',
    'bio'        => 'Flutter Developer skilled in building cross-platform apps with Dart, clean UIs, and efficient state management. Passionate about performance and user-focused design.',
    'profile_pic' => '',
    'cv_file'    => ''
];

if (!empty($info)) {
    $profile['name']        = $info['name'] ?? $profile['name'];
    $profile['title']       = $info['title'] ?? $profile['title'];
    $profile['bio']         = $info['bio'] ?? $profile['bio'];
    $profile['profile_pic'] = $info['profile_pic'] ?? '';
    $profile['cv_file']     = $info['cv_file'] ?? '';
}

/* CV-based fallback skills */
$fallbackSkills = [
    'Dart',
    'Python',
    'Java',
    'Database',
    'RESTful APIs',
    'SQLite',
    'MySQL',
    'OOP',
    'State Management',
    'MVC / MVVM Architecture',
    'Deployment',
    'Coding for Mobile Applications',
    'Problem-Solving & Collaboration'
];

/* Languages */
if (empty($languages)) {
    $languages = [
        ['language' => 'English', 'level' => 'Professional'],
        ['language' => 'Urdu', 'level' => 'Native'],
        ['language' => 'Punjabi', 'level' => 'Conversational']
    ];
}

if (!is_array($certificates)) {
    $certificates = [];
}

/* CV + user-provided experience */
$fallbackExperience = [
    [
        'role' => 'Mathematics Mentor',
        'date' => 'First Experience',
        'company' => 'Nasir Forces Academy',
        'description' => 'Worked as a Mathematics Mentor, supporting students in mathematics learning and academic development.'
    ],
    [
        'role' => 'Flutter Developer',
        'date' => 'Second Experience',
        'company' => 'AlhaiSofts',
        'description' => 'Worked as a Flutter Developer with responsibilities including coding, software debugging and testing.'
    ],
    [
        'role' => 'Founder',
        'date' => 'Current',
        'company' => 'Dev Drive Private Limited',
        'description' => 'Founder of Dev Drive Private Limited, working on software and technology-focused initiatives.'
    ]
];

/* Use database experiences when available; otherwise show the three experiences above. */
$experience = [];
if (!empty($dbExp)) {
    foreach ($dbExp as $item) {
        $experience[] = [
            'role' => $item['role'] ?? 'Professional Experience',
            'date' => $item['date'] ?? '',
            'company' => $item['company'] ?? '',
            'description' => $item['description'] ?? ''
        ];
    }
} else {
    $experience = $fallbackExperience;
}

/* Education fallback from CV */
$fallbackEducation = [
    [
        'degree' => 'Software Engineering',
        'institution' => 'Thal University Bhakkar',
        'duration' => '2022 - 2026',
        'result' => 'CGPA: 3.68 / 4.0',
        'description' => ''
    ],
    [
        'degree' => 'Intermediate',
        'institution' => 'Pakistan Public School and College Dullewala',
        'duration' => '2020 - 2022',
        'result' => '',
        'description' => ''
    ],
    [
        'degree' => 'Matric',
        'institution' => 'Pakistan Public School and College Dullewala',
        'duration' => '2017 - 2019',
        'result' => '867/1100',
        'description' => ''
    ]
];

if (empty($education)) {
    $education = $fallbackEducation;
}

/* Use DB skills when available */
if (empty($skills)) {
    $skills = array_map(
        fn($skill) => ['skill_name' => $skill],
        $fallbackSkills
    );
}

/* ---------------------------------------------------------
   CONTACT
--------------------------------------------------------- */
$email = 'shahbazdev@gmail.com';
$linkedin = '';
$instagram = '';

if (!empty($contact)) {
    foreach ($contact as $c) {
        $type  = strtolower(trim($c['type'] ?? ''));
        $value = trim($c['value'] ?? '');

        if ($value === '') {
            continue;
        }

        if ($type === 'email' && $email === 'shahbazdev@gmail.com') {
            $email = $value;
        } elseif (in_array($type, ['linkedin', 'linked-in', 'linkedin profile'], true)) {
            $linkedin = $value;
        } elseif (in_array($type, ['instagram', 'instagram profile'], true)) {
            $instagram = $value;
        }
    }
}

/* Social links can also be entered as complete URLs or usernames. */
function socialUrl(string $value, string $network): string
{
    if ($value === '') {
        return '';
    }

    if (preg_match('/^https?:\\/\\//i', $value)) {
        return $value;
    }

    if ($network === 'linkedin') {
        return 'https://www.linkedin.com/in/' . ltrim($value, '@/');
    }

    if ($network === 'instagram') {
        return 'https://www.instagram.com/' . ltrim($value, '@/');
    }

    return $value;
}

$linkedinUrl = socialUrl($linkedin, 'linkedin');
$instagramUrl = socialUrl($instagram, 'instagram');

/* ---------------------------------------------------------
   HELPERS
--------------------------------------------------------- */
function esc($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function projectTags($tags): array
{
    if (empty($tags)) {
        return [];
    }

    $parts = preg_split('/[,|]+/', $tags);
    return array_values(array_filter(array_map('trim', $parts)));
}

function renderIcon($icon, string $fallback = '✦'): string
{
    $icon = trim((string)$icon);

    if ($icon === '') {
        return esc($fallback);
    }

    // Render Font Awesome class values stored by the dashboard.
    if (preg_match('/^fa-(solid|regular|brands)\s+fa-[a-z0-9-]+(?:\s+fa-[a-z0-9-]+)*$/i', $icon)) {
        return '<i class="' . esc($icon) . '" aria-hidden="true"></i>';
    }

    return esc($icon);
}

$profileImage = '';
if (!empty($profile['profile_pic'])) {
    $candidate = __DIR__ . '/admin/uploads/profile/' . basename($profile['profile_pic']);
    if (is_file($candidate)) {
        $profileImage = 'admin/uploads/profile/' . rawurlencode(basename($profile['profile_pic']));
    }
}

$cvLink = '';
if (!empty($profile['cv_file'])) {
    $candidate = __DIR__ . '/admin/uploads/cv/' . basename($profile['cv_file']);
    if (is_file($candidate)) {
        $cvLink = 'admin/uploads/cv/' . rawurlencode(basename($profile['cv_file']));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= esc($profile['name']); ?> | <?= esc($profile['title']); ?></title>

    <meta name="description" content="<?= esc($profile['bio']); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --bg: #f4f1ff;
            --bg-soft: #eeebfa;
            --surface: #ffffff;
            --surface-2: #f8f7ff;
            --text: #20233a;
            --muted: #73758b;
            --primary: #6956d8;
            --primary-dark: #5141bd;
            --secondary: #8ea9ff;
            --line: #ddd9ee;
            --shadow: 0 18px 55px rgba(65, 53, 125, .10);
            --radius: 22px;
            --max: 1160px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: "DM Sans", sans-serif;
            color: var(--text);
            padding-top: 76px;
            background:
                radial-gradient(circle at 85% 8%, rgba(142, 169, 255, .20), transparent 25%),
                radial-gradient(circle at 10% 28%, rgba(105, 86, 216, .10), transparent 28%),
                var(--bg);
            line-height: 1.7;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .container {
            width: min(var(--max), calc(100% - 40px));
            margin: auto;
        }

        section {
            scroll-margin-top: 95px;
        }

        /* NAVBAR */
        .navbar {
            position: fixed;
            top: 0;
            z-index: 1000;
            background: rgba(244, 241, 255, .90);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(221, 217, 238, .85);
            left: 0;
            right: 0;
        }

        .nav-inner {
            min-height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        .brand {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -.5px;
        }

        .brand span {
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
            list-style: none;
            color: #5e6074;
            font-size: 14px;
            font-weight: 600;
        }

        .nav-links a {
            transition: .25s ease;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-admin {
            padding: 10px 17px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: var(--surface);
            color: var(--primary);
        }

        /* HERO */
        .hero {
            min-height: 690px;
            display: flex;
            align-items: center;
            padding: 85px 0 75px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            align-items: center;
            gap: 70px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: var(--primary);
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.6px;
            margin-bottom: 18px;
        }

        .eyebrow::before {
            content: "";
            width: 34px;
            height: 2px;
            background: var(--primary);
            border-radius: 20px;
        }

        .hero h1 {
            font-family: "Playfair Display", serif;
            font-size: clamp(46px, 7vw, 78px);
            line-height: 1.02;
            letter-spacing: -2.5px;
            margin-bottom: 17px;
        }

        .hero h1 span {
            color: var(--primary);
        }

        .hero-title {
            font-size: clamp(20px, 3vw, 28px);
            font-weight: 600;
            color: #555770;
            margin-bottom: 20px;
        }

        .hero-text {
            max-width: 650px;
            color: var(--muted);
            font-size: 16px;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 13px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 21px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            transition: .25s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 12px 28px rgba(105, 86, 216, .25);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: var(--surface);
            color: var(--text);
            border: 1px solid var(--line);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .hero-card {
            position: relative;
            display: flex;
            justify-content: center;
        }

        .profile-frame {
            position: relative;
            width: min(370px, 80vw);
            aspect-ratio: 1 / 1;
            border-radius: 50%;
            padding: 11px;
            background: linear-gradient(145deg, var(--primary), var(--secondary));
            box-shadow: 0 28px 65px rgba(72, 59, 157, .20);
        }

        .profile-inner {
            width: 100%;
            height: 100%;
            overflow: hidden;
            border-radius: 50%;
            background: var(--surface-2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-inner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-placeholder {
            font-family: "Playfair Display", serif;
            font-size: 92px;
            color: var(--primary);
        }

        .floating-card {
            position: absolute;
            bottom: 18px;
            left: 0;
            background: rgba(255, 255, 255, .94);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 14px 18px;
            box-shadow: var(--shadow);
        }

        .floating-card strong {
            display: block;
            font-size: 14px;
        }

        .floating-card small {
            color: var(--muted);
            font-size: 12px;
        }

        /* GENERAL SECTIONS */
        section {
            padding: 90px 0;
        }

        .section-heading {
            text-align: center;
            margin-bottom: 48px;
        }

        .section-heading .mini {
            color: var(--primary);
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 2px;
            font-weight: 800;
            margin-bottom: 9px;
        }

        .section-heading h2 {
            font-family: "Playfair Display", serif;
            font-size: clamp(34px, 5vw, 48px);
            line-height: 1.15;
            margin-bottom: 12px;
        }

        .section-heading p {
            max-width: 650px;
            margin: auto;
            color: var(--muted);
            font-size: 15px;
        }

        /* ABOUT */
        .about-wrap {
            display: grid;
            grid-template-columns: .85fr 1.15fr;
            gap: 35px;
        }

        .about-box,
        .highlight-card,
        .timeline-card,
        .education-card,
        .project-card,
        .contact-card {
            background: rgba(255, 255, 255, .82);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
        }

        .about-box {
            padding: 35px;
            border-radius: var(--radius);
        }

        .about-box h3 {
            font-family: "Playfair Display", serif;
            font-size: 30px;
            margin-bottom: 15px;
        }

        .about-box p {
            color: var(--muted);
        }

        .about-points {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .highlight-card {
            border-radius: 18px;
            padding: 23px;
        }

        .highlight-icon {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: #eeebff;
            color: var(--primary);
            font-size: 20px;
            margin-bottom: 15px;
        }

        .highlight-card h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .highlight-card p {
            color: var(--muted);
            font-size: 13px;
        }

        /* SKILLS */
        .skills-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            max-width: 920px;
            margin: auto;
        }

        .skill-pill {
            padding: 12px 18px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 999px;
            color: #505267;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 8px 25px rgba(65, 53, 125, .06);
            transition: .25s ease;
        }

        .skill-pill:hover {
            color: var(--primary);
            border-color: #bcb4e8;
            transform: translateY(-2px);
        }

        /* EXPERIENCE */
        .timeline {
            max-width: 920px;
            margin: auto;
            position: relative;
        }

        .timeline::before {
            content: "";
            position: absolute;
            left: 13px;
            top: 8px;
            bottom: 8px;
            width: 2px;
            background: #d7d2ed;
        }

        .timeline-item {
            position: relative;
            padding-left: 48px;
            margin-bottom: 25px;
        }

        .timeline-dot {
            position: absolute;
            left: 5px;
            top: 21px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--primary);
            border: 4px solid var(--bg);
            box-shadow: 0 0 0 2px #c9c2eb;
        }

        .timeline-card {
            border-radius: 19px;
            padding: 25px 27px;
        }

        .timeline-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .timeline-card h3 {
            font-size: 20px;
            margin-bottom: 2px;
        }

        .company {
            color: var(--primary);
            font-weight: 700;
            font-size: 14px;
        }

        .date {
            flex-shrink: 0;
            padding: 7px 11px;
            background: #eeebff;
            color: var(--primary);
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .timeline-card p {
            color: var(--muted);
            font-size: 14px;
            margin-top: 10px;
        }

        /* EDUCATION */
        .education-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .education-card {
            border-radius: 20px;
            padding: 27px;
        }

        .education-year {
            display: inline-block;
            color: var(--primary);
            background: #eeebff;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            margin-bottom: 17px;
        }

        .education-card h3 {
            font-family: "Playfair Display", serif;
            font-size: 23px;
            margin-bottom: 8px;
        }

        .education-card .institution {
            color: #5c5e73;
            font-weight: 600;
            font-size: 14px;
        }

        .education-card .result {
            margin-top: 13px;
            color: var(--primary);
            font-weight: 700;
            font-size: 13px;
        }

        /* PROJECTS */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .project-card {
            overflow: hidden;
            border-radius: 20px;
            transition: .25s ease;
            min-width: 0;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 24px 60px rgba(65, 53, 125, .14);
        }

        .project-icon {
            height: 150px;
            display: grid;
            place-items: center;
            background: linear-gradient(145deg, #ebe8ff, #e7efff);
            color: var(--primary);
            font-size: 45px;
        }

        .project-body {
            padding: 23px;
        }

        .project-body h3 {
            font-size: 19px;
            margin-bottom: 8px;
        }

        .project-body p {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 15px;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .tag {
            font-size: 10px;
            font-weight: 700;
            color: var(--primary);
            background: #f0edff;
            padding: 5px 8px;
            border-radius: 7px;
        }

        .empty-projects {
            text-align: center;
            padding: 38px;
            color: var(--muted);
            background: rgba(255,255,255,.55);
            border: 1px dashed #cfc9e7;
            border-radius: 20px;
            grid-column: 1 / -1;
        }

        /* LANGUAGES */
        .language-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            max-width: 920px;
            margin: auto;
        }

        .language-card {
            background: rgba(255,255,255,.82);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            border-radius: 18px;
            padding: 22px;
        }

        .language-card h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .language-level {
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
        }

        /* CERTIFICATES & ACHIEVEMENTS */
        .certificate-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            max-width: 920px;
            margin: auto;
        }

        .certificate-card {
            background: rgba(255,255,255,.82);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            border-radius: 20px;
            padding: 25px;
            min-height: 205px;
        }

        .certificate-icon {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: #eeebff;
            color: var(--primary);
            font-size: 21px;
            margin-bottom: 14px;
        }

        .certificate-card h3 {
            font-size: 19px;
            margin-bottom: 7px;
        }

        .certificate-issuer {
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .certificate-card p {
            color: var(--muted);
            font-size: 13px;
        }

        .certificate-year {
            display: inline-block;
            margin-top: 13px;
            padding: 5px 9px;
            border-radius: 999px;
            background: #f0edff;
            color: var(--primary);
            font-size: 10px;
            font-weight: 800;
        }

        .empty-content {
            grid-column: 1 / -1;
            text-align: center;
            padding: 35px;
            color: var(--muted);
            background: rgba(255,255,255,.55);
            border: 1px dashed #cfc9e7;
            border-radius: 20px;
        }

        /* CONTACT */
        .contact-section {
            padding-bottom: 105px;
        }

        .contact-box {
            max-width: 850px;
            margin: auto;
            background: linear-gradient(145deg, #ebe8ff, #e7efff);
            border: 1px solid #d3cdec;
            border-radius: 28px;
            padding: 45px;
            text-align: center;
        }

        .contact-box h2 {
            font-family: "Playfair Display", serif;
            font-size: 42px;
            margin-bottom: 10px;
        }

        .contact-box p {
            color: var(--muted);
            margin-bottom: 25px;
        }

        .email-link {
            display: inline-block;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 24px;
        }

        /* FOOTER */
        footer {
            background: #e9e6f5;
            border-top: 1px solid var(--line);
            padding: 25px 0;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            color: var(--muted);
            font-size: 13px;
        }

        .footer-brand {
            font-weight: 700;
            color: var(--text);
        }

        /* MOBILE MENU */
        .menu-toggle,
        .mobile-menu {
            display: none;
        }

        .social-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin: 0 0 24px;
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 16px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(255,255,255,.78);
            color: var(--text);
            font-size: 13px;
            font-weight: 700;
            transition: .25s ease;
        }

        .social-link:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .social-icon {
            font-size: 16px;
            color: var(--primary);
        }

        /* Better tablet/mobile sizing */
        @media (max-width: 1100px) {
            .hero-grid {
                gap: 45px;
            }

            .nav-links {
                gap: 16px;
            }

            .hero h1 {
                font-size: clamp(42px, 6vw, 68px);
            }
        }

        /* MOBILE */
        @media (max-width: 900px) {
            .hero-grid,
            .about-wrap {
                grid-template-columns: 1fr;
            }

            .hero {
                padding-top: 65px;
            }

            .hero-card {
                order: -1;
            }

            .profile-frame {
                width: min(310px, 72vw);
            }

            .education-grid,
            .projects-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 680px) {
            body {
                padding-top: 68px;
            }

            section {
                scroll-margin-top: 82px;
            }

            .container {
                width: min(100% - 24px, var(--max));
            }

            .nav-inner {
                min-height: 68px;
                position: relative;
            }

            .brand {
                font-size: 18px;
            }

            .nav-links {
                display: none;
            }

            .menu-toggle {
                display: flex;
                width: 44px;
                height: 44px;
                border: 1px solid var(--line);
                border-radius: 12px;
                background: var(--surface);
                align-items: center;
                justify-content: center;
                flex-direction: column;
                gap: 5px;
                cursor: pointer;
            }

            .menu-toggle span {
                width: 20px;
                height: 2px;
                border-radius: 10px;
                background: var(--text);
                transition: .25s ease;
            }

            .menu-toggle.active span:nth-child(1) {
                transform: translateY(7px) rotate(45deg);
            }

            .menu-toggle.active span:nth-child(2) {
                opacity: 0;
            }

            .menu-toggle.active span:nth-child(3) {
                transform: translateY(-7px) rotate(-45deg);
            }

            .mobile-menu {
                position: absolute;
                display: flex;
                top: calc(100% + 1px);
                left: 0;
                right: 0;
                padding: 10px;
                flex-direction: column;
                gap: 3px;
                background: rgba(244, 241, 255, .98);
                border: 1px solid var(--line);
                border-top: 0;
                border-radius: 0 0 18px 18px;
                box-shadow: var(--shadow);
                opacity: 0;
                visibility: hidden;
                transform: translateY(-8px);
                transition: .25s ease;
            }

            .mobile-menu.open {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }

            .mobile-menu a {
                padding: 12px 14px;
                border-radius: 10px;
                font-size: 14px;
                font-weight: 700;
                color: var(--text);
            }

            .mobile-menu a:hover {
                background: var(--surface);
                color: var(--primary);
            }

            .mobile-menu .mobile-admin {
                background: var(--primary);
                color: #fff;
                text-align: center;
                margin-top: 5px;
            }

            section {
                padding: 58px 0;
            }

            .hero {
                min-height: auto;
                padding: 48px 0 65px;
            }

            .hero-grid {
                gap: 38px;
            }

            .hero-content {
                text-align: center;
            }

            .eyebrow {
                justify-content: center;
                font-size: 11px;
                letter-spacing: 1.2px;
            }

            .hero h1 {
                font-size: clamp(38px, 12vw, 52px);
                letter-spacing: -1.5px;
            }

            .hero-title {
                font-size: 20px;
            }

            .hero-text {
                font-size: 14px;
                margin-left: auto;
                margin-right: auto;
            }

            .hero-buttons {
                justify-content: center;
            }

            .btn {
                width: 100%;
                max-width: 260px;
            }

            .profile-frame {
                width: min(280px, 72vw);
            }

            .floating-card {
                left: 50%;
                transform: translateX(-50%);
                bottom: 0;
                width: max-content;
                max-width: 90%;
                text-align: center;
                padding: 11px 14px;
            }

            .section-heading {
                margin-bottom: 35px;
            }

            .section-heading h2 {
                font-size: clamp(30px, 9vw, 40px);
            }

            .about-box,
            .timeline-card,
            .education-card {
                padding: 22px;
            }

            .about-points,
            .education-grid,
            .projects-grid {
                grid-template-columns: 1fr;
            }

            .timeline {
                padding-left: 0;
            }

            .timeline-item {
                padding-left: 36px;
            }

            .timeline::before {
                left: 9px;
            }

            .timeline-dot {
                left: 1px;
            }

            .timeline-top {
                flex-direction: column;
                gap: 9px;
            }

            .date {
                align-self: flex-start;
            }

            .project-icon {
                height: 125px;
            }

            .language-grid,
            .certificate-grid {
                grid-template-columns: 1fr;
            }

            .contact-box {
                padding: 32px 18px;
                border-radius: 22px;
            }

            .contact-box h2 {
                font-size: clamp(30px, 9vw, 36px);
            }

            .email-link {
                overflow-wrap: anywhere;
                font-size: 14px;
            }

            .social-links {
                flex-direction: column;
                width: 100%;
            }

            .social-link {
                width: 100%;
                max-width: 280px;
            }

            .footer-inner {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 380px) {
            .hero h1 {
                font-size: 36px;
            }

            .profile-frame {
                width: 235px;
            }

            .floating-card {
                font-size: 12px;
            }

            .about-box,
            .timeline-card,
            .education-card,
            .project-body {
                padding: 18px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
</head>

<body>

<!-- NAVIGATION -->
<header class="navbar">
    <div class="container nav-inner">
        <a href="#home" class="brand">
            Shahbaz<span>.</span>
        </a>

        <button class="menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="mobileMenu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#skills">Skills</a></li>
            <li><a href="#languages">Languages</a></li>
            <li><a href="#experience">Experience</a></li>
            <li><a href="#education">Education</a></li>
            <li><a href="#projects">Projects</a></li>
            <li><a href="#certificates">Certificates</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>

        <div class="mobile-menu" id="mobileMenu">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#skills">Skills</a>
            <a href="#languages">Languages</a>
            <a href="#experience">Experience</a>
            <a href="#education">Education</a>
            <a href="#projects">Projects</a>
            <a href="#certificates">Certificates</a>
            <a href="#contact">Contact</a>
            <a class="mobile-admin" href="admin/login.php">Admin</a>
        </div>
    </div>
</header>

<main>

    <!-- HERO -->
    <section class="hero" id="home">
        <div class="container hero-grid">

            <div class="hero-content">
                <div class="eyebrow">Welcome to my portfolio</div>

                <h1>
                    <?= esc($profile['name']); ?><br>
                    <span><?= esc($profile['title']); ?></span>
                </h1>

                <p class="hero-text">
                    <?= esc($profile['bio']); ?>
                </p>

                <div class="hero-buttons">
                    <a href="#projects" class="btn btn-primary">View My Work</a>

                    <?php if ($cvLink): ?>
                        <a href="<?= esc($cvLink); ?>" target="_blank" class="btn btn-outline">View CV</a>
                    <?php else: ?>
                        <a href="#contact" class="btn btn-outline">Contact Me</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="hero-card">
                <div class="profile-frame">
                    <div class="profile-inner">
                        <?php if ($profileImage): ?>
                            <img src="<?= esc($profileImage); ?>" alt="<?= esc($profile['name']); ?>">
                        <?php else: ?>
                            <div class="profile-placeholder">MS</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="floating-card">
                    <strong>Flutter Developer</strong>
                    <small>Mobile & Cross-Platform Development</small>
                </div>
            </div>

        </div>
    </section>

    <!-- ABOUT / HIGHLIGHTS -->
    <section id="about">
        <div class="container">

            <div class="section-heading">
                <div class="mini">About Me</div>
                <h2>Building useful digital experiences</h2>
                <p>
                    A professional profile combining software development,
                    problem-solving and continuous learning.
                </p>
            </div>

            <div class="about-wrap">

                <div class="about-box">
                    <h3>Profile</h3>
                    <p><?= esc($profile['bio']); ?></p>
                </div>

                <div class="about-points">
                    <?php if (!empty($highlights)): ?>
                        <?php foreach ($highlights as $highlight): ?>
                            <article class="highlight-card">
                                <div class="highlight-icon">
                                    <?= renderIcon($highlight['icon'] ?? '', '✦'); ?>
                                </div>
                                <h3><?= esc($highlight['title'] ?? 'Highlight'); ?></h3>
                                <p><?= esc($highlight['description'] ?? ''); ?></p>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <article class="highlight-card">
                            <div class="highlight-icon">✦</div>
                            <h3>Cross-Platform Development</h3>
                            <p>Focused on Flutter and Dart for cross-platform mobile applications.</p>
                        </article>

                        <article class="highlight-card">
                            <div class="highlight-icon">⌘</div>
                            <h3>Clean UI</h3>
                            <p>Interested in clean, practical and user-focused interface design.</p>
                        </article>

                        <article class="highlight-card">
                            <div class="highlight-icon">⚙</div>
                            <h3>Problem Solving</h3>
                            <p>Experienced in coding, debugging, testing and solving technical problems.</p>
                        </article>

                        <article class="highlight-card">
                            <div class="highlight-icon">↗</div>
                            <h3>Performance</h3>
                            <p>Focused on efficient applications and better user experiences.</p>
                        </article>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- SKILLS -->
    <section id="skills">
        <div class="container">

            <div class="section-heading">
                <div class="mini">My Skills</div>
                <h2>Technologies & Expertise</h2>
                <p>
                    Technical skills and areas of knowledge can be managed directly
                    from the admin dashboard.
                </p>
            </div>

            <div class="skills-grid">
                <?php foreach ($skills as $skill): ?>
                    <span class="skill-pill">
                        <?= esc($skill['skill_name'] ?? ''); ?>
                    </span>
                <?php endforeach; ?>
            </div>

        </div>
    </section>


    <!-- LANGUAGES -->
    <section id="languages">
        <div class="container">
            <div class="section-heading">
                <div class="mini">Languages</div>
                <h2>Languages I Know</h2>
                <p>Language information can be managed from the admin dashboard.</p>
            </div>
            <div class="language-grid">
                <?php if (!empty($languages)): ?>
                    <?php foreach ($languages as $language): ?>
                        <article class="language-card">
                            <h3><?= esc($language['language'] ?? ''); ?></h3>
                            <?php if (!empty($language['level'])): ?>
                                <div class="language-level"><?= esc($language['level']); ?></div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-content">No languages have been added yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- EXPERIENCE -->
    <section id="experience">
        <div class="container">

            <div class="section-heading">
                <div class="mini">Career Journey</div>
                <h2>Professional Experience</h2>
                <p>
                    My professional journey across teaching, software development
                    and entrepreneurship.
                </p>
            </div>

            <div class="timeline">

                <?php foreach ($experience as $item): ?>
                    <article class="timeline-item">
                        <div class="timeline-dot"></div>

                        <div class="timeline-card">
                            <div class="timeline-top">
                                <div>
                                    <h3><?= esc($item['role']); ?></h3>

                                    <?php if (!empty($item['company'])): ?>
                                        <div class="company">
                                            <?= esc($item['company']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($item['date'])): ?>
                                    <div class="date">
                                        <?= esc($item['date']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($item['description'])): ?>
                                <p><?= esc($item['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

    <!-- EDUCATION -->
    <section id="education">
        <div class="container">

            <div class="section-heading">
                <div class="mini">Education</div>
                <h2>Academic Background</h2>
                <p>
                    Academic qualifications from the information provided in the CV.
                </p>
            </div>

            <div class="education-grid">

                <?php foreach ($education as $edu): ?>
                    <article class="education-card">

                        <?php if (!empty($edu['duration'])): ?>
                            <span class="education-year">
                                <?= esc($edu['duration']); ?>
                            </span>
                        <?php endif; ?>

                        <h3><?= esc($edu['degree'] ?? ''); ?></h3>

                        <div class="institution">
                            <?= esc($edu['institution'] ?? ''); ?>
                        </div>

                        <?php if (!empty($edu['result'])): ?>
                            <div class="result">
                                <?= esc($edu['result']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($edu['description'])): ?>
                            <p style="margin-top:10px;color:var(--muted);font-size:13px;">
                                <?= esc($edu['description']); ?>
                            </p>
                        <?php endif; ?>

                    </article>
                <?php endforeach; ?>

            </div>
        </div>
    </section>


    <!-- CERTIFICATES & ACHIEVEMENTS -->
    <section id="certificates">
        <div class="container">
            <div class="section-heading">
                <div class="mini">Credentials</div>
                <h2>Certificates & Achievements</h2>
                <p>Certificates and achievements can be managed from the admin dashboard.</p>
            </div>
            <div class="certificate-grid">
                <?php if (!empty($certificates)): ?>
                    <?php foreach ($certificates as $certificate): ?>
                        <article class="certificate-card">
                            <div class="certificate-icon"><?= renderIcon($certificate['icon'] ?? '', '🏆'); ?></div>
                            <h3><?= esc($certificate['title'] ?? 'Certificate'); ?></h3>
                            <?php if (!empty($certificate['issuer'])): ?>
                                <div class="certificate-issuer"><?= esc($certificate['issuer']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($certificate['description'])): ?>
                                <p><?= esc($certificate['description']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($certificate['year'])): ?>
                                <span class="certificate-year"><?= esc($certificate['year']); ?></span>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-content">No certificates or achievements have been added yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- PROJECTS -->
    <section id="projects">
        <div class="container">

            <div class="section-heading">
                <div class="mini">Portfolio</div>
                <h2>Featured Projects</h2>
                <p>
                    Projects added from the admin dashboard will automatically
                    appear here.
                </p>
            </div>

            <div class="projects-grid">

                <?php if (!empty($projects)): ?>

                    <?php foreach ($projects as $project): ?>
                        <?php
                            $tags = projectTags($project['tags'] ?? '');
                            $icon = !empty($project['icon']) ? $project['icon'] : '⌘';
                        ?>

                        <article class="project-card">

                            <div class="project-icon">
                                <?= renderIcon($icon, '⌘'); ?>
                            </div>

                            <div class="project-body">
                                <h3><?= esc($project['title'] ?? 'Project'); ?></h3>

                                <p>
                                    <?= esc($project['description'] ?? ''); ?>
                                </p>

                                <?php if (!empty($tags)): ?>
                                    <div class="tags">
                                        <?php foreach ($tags as $tag): ?>
                                            <span class="tag"><?= esc($tag); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </article>
                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="empty-projects">
                        <strong>Projects will appear here.</strong>
                        <br>
                        Add your projects from the Admin Dashboard.
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </section>

    <!-- CONTACT -->
    <section class="contact-section" id="contact">
        <div class="container">

            <div class="contact-box">
                <div class="section-heading" style="margin-bottom:15px;">
                    <div class="mini">Get In Touch</div>
                    <h2>Let's connect</h2>
                </div>

                <p>
                    Interested in discussing a project, collaboration or
                    professional opportunity?
                </p>

                <a class="email-link" href="https://mail.google.com/mail/?view=cm&amp;fs=1&amp;to=<?= rawurlencode(trim($email)); ?>" target="_blank" rel="noopener noreferrer">
                    <?= esc($email); ?>
                </a>

                <?php if ($linkedinUrl || $instagramUrl): ?>
                    <div class="social-links">
                        <?php if ($linkedinUrl): ?>
                            <a class="social-link" href="<?= esc($linkedinUrl); ?>" target="_blank" rel="noopener noreferrer">
                                <span class="social-icon">in</span>
                                LinkedIn
                            </a>
                        <?php endif; ?>

                        <?php if ($instagramUrl): ?>
                            <a class="social-link" href="<?= esc($instagramUrl); ?>" target="_blank" rel="noopener noreferrer">
                                <span class="social-icon">◎</span>
                                Instagram
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <a href="https://mail.google.com/mail/?view=cm&amp;fs=1&amp;to=<?= rawurlencode(trim($email)); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                    Send Email
                </a>
            </div>

        </div>
    </section>

</main>

<footer>
    <div class="container footer-inner">
        <div class="footer-brand">
            <?= esc($profile['name']); ?>
        </div>

        <div>
            © <?= date('Y'); ?> <?= esc($profile['name']); ?>. All rights reserved.
        </div>

        <div>
            <a href="admin/login.php">Admin</a>
        </div>
    </div>
</footer>

<script>
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileMenu = document.getElementById('mobileMenu');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function () {
            const isOpen = mobileMenu.classList.toggle('open');
            menuToggle.classList.toggle('active', isOpen);
            menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        mobileMenu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                mobileMenu.classList.remove('open');
                menuToggle.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        });

        document.addEventListener('click', function (event) {
            if (!mobileMenu.contains(event.target) && !menuToggle.contains(event.target)) {
                mobileMenu.classList.remove('open');
                menuToggle.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
</script>

</body>
</html>