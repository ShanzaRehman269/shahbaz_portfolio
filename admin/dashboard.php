<?php
/**
 * SHAHBAZ PORTFOLIO
 * Admin Dashboard
 * File: admin/dashboard.php
 */

require_once __DIR__ . '/includes/functions.php';

checkLogin();

$msg = "";
$error = "";


/* =========================================================
   UPLOAD DIRECTORIES
========================================================= */

$uploadBase  = __DIR__ . '/uploads/';
$profileDir  = $uploadBase . 'profile/';
$cvDir       = $uploadBase . 'cv/';
$projectDir  = $uploadBase . 'projects/';

foreach ([$profileDir, $cvDir, $projectDir] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}


/* =========================================================
   HELPERS
========================================================= */

function clean($value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}


function uploadFile(
    string $field,
    string $directory,
    array $allowedExtensions
): ?string {

    if (
        !isset($_FILES[$field]) ||
        $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload failed.");
    }

    $originalName = $_FILES[$field]['name'];
    $tmpName      = $_FILES[$field]['tmp_name'];

    $extension = strtolower(
        pathinfo($originalName, PATHINFO_EXTENSION)
    );

    if (!in_array($extension, $allowedExtensions, true)) {
        throw new Exception(
            "Invalid file type. Allowed: " .
            implode(', ', $allowedExtensions)
        );
    }

    $newName = uniqid('', true) . '.' . $extension;

    $destination = $directory . $newName;

    if (!move_uploaded_file($tmpName, $destination)) {
        throw new Exception("Unable to save uploaded file.");
    }

    return $newName;
}


function contactValue(array $contact, string $type): string
{
    foreach ($contact as $row) {

        if (($row['type'] ?? '') === $type) {
            return (string)($row['value'] ?? '');
        }

    }

    return '';
}


function contactLink(array $contact, string $type): string
{
    foreach ($contact as $row) {

        if (($row['type'] ?? '') === $type) {
            return (string)($row['link'] ?? '');
        }

    }

    return '';
}


/* =========================================================
   POST ACTIONS
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    try {

        /* =================================================
           PROFILE
        ================================================= */

        if ($action === 'save_profile') {

            $name = trim($_POST['name'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $bio = trim($_POST['bio'] ?? '');

            if ($name === '') {
                throw new Exception("Name is required.");
            }

            if ($title === '') {
                throw new Exception("Title is required.");
            }

            $profileImage = uploadFile(
                'profile_pic',
                $profileDir,
                ['jpg', 'jpeg', 'png', 'webp']
            );

            $cvFile = uploadFile(
                'cv_file',
                $cvDir,
                ['pdf', 'doc', 'docx']
            );

            if (!updateInfo(
                $name,
                $title,
                $bio,
                $profileImage,
                $cvFile
            )) {
                throw new Exception("Unable to update profile.");
            }

            $msg = "Profile updated successfully.";
        }


        /* =================================================
           ADD SKILL
        ================================================= */

        elseif ($action === 'add_skill') {

            $skill = trim($_POST['skill'] ?? '');

            if ($skill === '') {
                throw new Exception("Skill name is required.");
            }

            if (!addSkill($skill)) {
                throw new Exception("Unable to add skill.");
            }

            $msg = "Skill added successfully.";
        }


        /* =================================================
           UPDATE SKILL
        ================================================= */

        elseif ($action === 'update_skill') {

            $id = (int)($_POST['id'] ?? 0);
            $skill = trim($_POST['skill'] ?? '');

            if ($id <= 0) {
                throw new Exception("Invalid skill ID.");
            }

            if ($skill === '') {
                throw new Exception("Skill name is required.");
            }

            if (!updateSkill($id, $skill)) {
                throw new Exception("Unable to update skill.");
            }

            $msg = "Skill updated successfully.";
        }


        /* =================================================
           ADD PROJECT
        ================================================= */

        elseif ($action === 'add_project') {

            $icon = trim($_POST['icon'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $tags = trim($_POST['tags'] ?? '');

            if ($title === '') {
                throw new Exception("Project title is required.");
            }

            if (!addProject(
                $icon,
                $title,
                $description,
                $tags
            )) {
                throw new Exception("Unable to add project.");
            }

            $msg = "Project added successfully.";
        }


        /* =================================================
           UPDATE PROJECT
        ================================================= */

        elseif ($action === 'update_project') {

            $id = (int)($_POST['id'] ?? 0);
            $icon = trim($_POST['icon'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $tags = trim($_POST['tags'] ?? '');

            if ($id <= 0) {
                throw new Exception("Invalid project ID.");
            }

            if ($title === '') {
                throw new Exception("Project title is required.");
            }

            if (!updateProject(
                $id,
                $icon,
                $title,
                $description,
                $tags
            )) {
                throw new Exception("Unable to update project.");
            }

            $msg = "Project updated successfully.";
        }


        /* =================================================
           ADD EXPERIENCE
        ================================================= */

        elseif ($action === 'add_experience') {

            $date = trim($_POST['date'] ?? '');
            $role = trim($_POST['role'] ?? '');
            $company = trim($_POST['company'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($role === '') {
                throw new Exception("Role is required.");
            }

            if (!addExperience(
                $date,
                $role,
                $company,
                $description
            )) {
                throw new Exception("Unable to add experience.");
            }

            $msg = "Experience added successfully.";
        }


        /* =================================================
           UPDATE EXPERIENCE
        ================================================= */

        elseif ($action === 'update_experience') {

            $id = (int)($_POST['id'] ?? 0);
            $date = trim($_POST['date'] ?? '');
            $role = trim($_POST['role'] ?? '');
            $company = trim($_POST['company'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($id <= 0) {
                throw new Exception("Invalid experience ID.");
            }

            if ($role === '') {
                throw new Exception("Role is required.");
            }

            if (!updateExperience(
                $id,
                $date,
                $role,
                $company,
                $description
            )) {
                throw new Exception("Unable to update experience.");
            }

            $msg = "Experience updated successfully.";
        }


        /* =================================================
           ADD EDUCATION
        ================================================= */

        elseif ($action === 'add_education') {

            $degree = trim($_POST['degree'] ?? '');
            $institution = trim($_POST['institution'] ?? '');
            $duration = trim($_POST['duration'] ?? '');
            $result = trim($_POST['result'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($degree === '') {
                throw new Exception("Degree is required.");
            }

            if (!addEducation(
                $degree,
                $institution,
                $duration,
                $result,
                $description
            )) {
                throw new Exception("Unable to add education.");
            }

            $msg = "Education added successfully.";
        }


        /* =================================================
           UPDATE EDUCATION
        ================================================= */

        elseif ($action === 'update_education') {

            $id = (int)($_POST['id'] ?? 0);
            $degree = trim($_POST['degree'] ?? '');
            $institution = trim($_POST['institution'] ?? '');
            $duration = trim($_POST['duration'] ?? '');
            $result = trim($_POST['result'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($id <= 0) {
                throw new Exception("Invalid education ID.");
            }

            if ($degree === '') {
                throw new Exception("Degree is required.");
            }

            if (!updateEducation(
                $id,
                $degree,
                $institution,
                $duration,
                $result,
                $description
            )) {
                throw new Exception("Unable to update education.");
            }

            $msg = "Education updated successfully.";
        }


        /* =================================================
           ADD HIGHLIGHT
        ================================================= */

        elseif ($action === 'add_highlight') {

            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? '✦');

            if ($title === '') {
                throw new Exception("Highlight title is required.");
            }

            if ($icon === '') {
                $icon = '✦';
            }

            if (!addHighlight(
                $title,
                $description,
                $icon
            )) {
                throw new Exception("Unable to add highlight.");
            }

            $msg = "Highlight added successfully.";
        }


        /* =================================================
           UPDATE HIGHLIGHT
        ================================================= */

        elseif ($action === 'update_highlight') {

            $id = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? '✦');

            if ($id <= 0) {
                throw new Exception("Invalid highlight ID.");
            }

            if ($title === '') {
                throw new Exception("Highlight title is required.");
            }

            if ($icon === '') {
                $icon = '✦';
            }

            if (!updateHighlight(
                $id,
                $title,
                $description,
                $icon
            )) {
                throw new Exception("Unable to update highlight.");
            }

            $msg = "Highlight updated successfully.";
        }


        /* =================================================
           ADD LANGUAGE
        ================================================= */

        elseif ($action === 'add_language') {

            $language = trim($_POST['language'] ?? '');
            $level = trim($_POST['level'] ?? '');

            if ($language === '') {
                throw new Exception("Language is required.");
            }

            if (!addLanguage(
                $language,
                $level
            )) {
                throw new Exception("Unable to add language.");
            }

            $msg = "Language added successfully.";
        }


        /* =================================================
           UPDATE LANGUAGE
        ================================================= */

        elseif ($action === 'update_language') {

            $id = (int)($_POST['id'] ?? 0);
            $language = trim($_POST['language'] ?? '');
            $level = trim($_POST['level'] ?? '');

            if ($id <= 0) {
                throw new Exception("Invalid language ID.");
            }

            if ($language === '') {
                throw new Exception("Language is required.");
            }

            if (!updateLanguage(
                $id,
                $language,
                $level
            )) {
                throw new Exception("Unable to update language.");
            }

            $msg = "Language updated successfully.";
        }


        /* =================================================
           ADD CERTIFICATE
        ================================================= */

        elseif ($action === 'add_certificate') {

            $icon = trim($_POST['icon'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $issuer = trim($_POST['issuer'] ?? '');
            $year = trim($_POST['year'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($title === '') {
                throw new Exception("Certificate title is required.");
            }

            if (!addCertificate(
                $icon,
                $title,
                $issuer,
                $year,
                $description
            )) {
                throw new Exception("Unable to add certificate.");
            }

            $msg = "Certificate added successfully.";
        }


        /* =================================================
           UPDATE CERTIFICATE
        ================================================= */

        elseif ($action === 'update_certificate') {

            $id = (int)($_POST['id'] ?? 0);
            $icon = trim($_POST['icon'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $issuer = trim($_POST['issuer'] ?? '');
            $year = trim($_POST['year'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($id <= 0) {
                throw new Exception("Invalid certificate ID.");
            }

            if ($title === '') {
                throw new Exception("Certificate title is required.");
            }

            if (!updateCertificate(
                $id,
                $icon,
                $title,
                $issuer,
                $year,
                $description
            )) {
                throw new Exception("Unable to update certificate.");
            }

            $msg = "Certificate updated successfully.";
        }


        /* =================================================
           CONTACT
        ================================================= */

        elseif ($action === 'save_contact') {

            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $linkedin = trim($_POST['linkedin'] ?? '');
            $instagram = trim($_POST['instagram'] ?? '');
            $github = trim($_POST['github'] ?? '');

            $emailLink = '';

            if ($email !== '') {
                $emailLink = 'mailto:' . $email;
            }

            $success = true;

            $success = updateContact(
                'email',
                $email,
                $emailLink
            ) && $success;

            $success = updateContact(
                'phone',
                $phone,
                $phone
            ) && $success;

            $success = updateContact(
                'linkedin',
                $linkedin,
                $linkedin
            ) && $success;

            $success = updateContact(
                'instagram',
                $instagram,
                $instagram
            ) && $success;

            $success = updateContact(
                'github',
                $github,
                $github
            ) && $success;

            if (!$success) {
                throw new Exception("Unable to update contact information.");
            }

            $msg = "Contact information updated successfully.";
        }


        /* =================================================
           CHANGE PASSWORD
        ================================================= */

        elseif ($action === 'change_password') {

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($currentPassword === '') {
                throw new Exception("Current password is required.");
            }

            if ($newPassword === '') {
                throw new Exception("New password is required.");
            }

            if (strlen($newPassword) < 6) {
                throw new Exception(
                    "New password must contain at least 6 characters."
                );
            }

            if ($newPassword !== $confirmPassword) {
                throw new Exception("New passwords do not match.");
            }

            $username = $_SESSION['admin_username'] ?? '';

            if ($username === '') {
                throw new Exception("Admin session expired.");
            }

            if (!checkPassword(
                $username,
                $currentPassword
            )) {
                throw new Exception("Current password is incorrect.");
            }

            if (!updatePassword(
                $username,
                $newPassword
            )) {
                throw new Exception("Unable to change password.");
            }

            $msg = "Password changed successfully.";
        }


        /* =================================================
           DELETE
        ================================================= */

        elseif ($action === 'delete') {

            $type = $_POST['type'] ?? '';
            $id = (int)($_POST['id'] ?? 0);

            $tableMap = [

                'skill'       => 'skills',
                'project'     => 'projects',
                'experience'  => 'experience',
                'education'   => 'education',
                'highlight'   => 'highlights',
                'language'    => 'languages',
                'certificate' => 'certificates',
                'contact'     => 'contact'

            ];

            if (
                $id <= 0 ||
                !isset($tableMap[$type])
            ) {
                throw new Exception("Invalid delete request.");
            }

            if (!deleteData(
                $tableMap[$type],
                $id
            )) {
                throw new Exception("Unable to delete item.");
            }

            $msg = "Item deleted successfully.";
        }

    } catch (Throwable $e) {

        $error = $e->getMessage();
    }
}


/* =========================================================
   LOAD DATA
========================================================= */

$info = getInfo() ?: [];

$skills = getSkills();
$projects = getProjects();
$experience = getExperience();
$education = getEducation();
$highlights = getHighlights();
$languages = getLanguages();
$certificates = getCertificates();
$contact = getContact();


$email = contactValue($contact, 'email');
$phone = contactValue($contact, 'phone');
$linkedin = contactValue($contact, 'linkedin');
$instagram = contactValue($contact, 'instagram');
$github = contactValue($contact, 'github');


$adminUsername = $_SESSION['admin_username'] ?? 'Admin';


$profilePic = $info['profile_pic'] ?? '';
$cvFile = $info['cv_file'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard | Shahbaz Portfolio</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f4f8fd;
            color: #16283f;
            min-height: 100vh;
        }


        /* =================================================
           SIDEBAR
        ================================================= */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;

            width: 255px;
            height: 100vh;

            background: #173f67;
            color: #fff;

            padding: 24px 16px;

            overflow-y: auto;

            z-index: 1000;
        }

        .brand {
            padding: 5px 12px 25px;
        }

        .brand h2 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .brand p {
            color: #c9def3;
            font-size: 13px;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .sidebar nav a {
            color: #dcecff;
            text-decoration: none;

            padding: 11px 13px;

            border-radius: 8px;

            font-size: 14px;

            transition: .2s;
        }

        .sidebar nav a:hover {
            background: #2d6598;
            color: #fff;
        }


        /* =================================================
           MAIN
        ================================================= */

        .main {
            margin-left: 255px;
            min-height: 100vh;
        }


        /* =================================================
           TOP HEADER
        ================================================= */

        .topbar {
            position: fixed;

            top: 0;
            left: 255px;
            right: 0;

            height: 72px;

            background: #ffffff;

            border-bottom: 1px solid #dce8f5;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 28px;

            z-index: 900;
        }

        .topbar h1 {
            font-size: 21px;
            color: #173f67;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-name {
            color: #5c7086;
            font-size: 14px;
            margin-right: 8px;
        }


        /* =================================================
           BUTTONS
        ================================================= */

        .btn {
            border: none;
            cursor: pointer;

            border-radius: 8px;

            padding: 9px 14px;

            font-size: 13px;

            text-decoration: none;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            transition: .2s;
        }

        .btn-primary {
            background: #276da4;
            color: white;
        }

        .btn-primary:hover {
            background: #1d598a;
        }

        .btn-secondary {
            background: #e3f0fc;
            color: #205b89;
        }

        .btn-secondary:hover {
            background: #d1e5f7;
        }

        .btn-danger {
            background: #fde7e7;
            color: #b33a3a;
        }

        .btn-danger:hover {
            background: #f9d3d3;
        }

        .btn-dark {
            background: #173f67;
            color: #fff;
        }

        .btn-dark:hover {
            background: #102f4d;
        }


        /* =================================================
           CONTENT
        ================================================= */

        .content {
            padding: 105px 28px 40px;
        }


        /* =================================================
           ALERTS
        ================================================= */

        .alert {
            padding: 13px 16px;
            border-radius: 9px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #e6f6ed;
            color: #17663a;
            border: 1px solid #c7ead6;
        }

        .alert-error {
            background: #fde9e9;
            color: #a92d2d;
            border: 1px solid #f4caca;
        }


        /* =================================================
           STATS
        ================================================= */

        .stats {
            display: grid;

            grid-template-columns:
                repeat(5, minmax(0, 1fr));

            gap: 15px;

            margin-bottom: 28px;
        }

        .stat-card {
            background: #fff;

            border: 1px solid #dce8f5;

            border-radius: 12px;

            padding: 18px;

            box-shadow:
                0 3px 12px rgba(33, 76, 115, .05);
        }

        .stat-card span {
            display: block;

            color: #73869b;

            font-size: 13px;

            margin-bottom: 8px;
        }

        .stat-card strong {
            color: #173f67;
            font-size: 25px;
        }


        /* =================================================
           SECTION
        ================================================= */

        .section {
            background: #fff;

            border: 1px solid #dce8f5;

            border-radius: 13px;

            padding: 22px;

            margin-bottom: 24px;

            box-shadow:
                0 4px 15px rgba(33, 76, 115, .05);
        }

        .section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 15px;

            margin-bottom: 18px;
        }

        .section-title h2 {
            color: #173f67;
            font-size: 19px;
        }

        .section-title p {
            color: #73869b;
            font-size: 13px;
        }


        /* =================================================
           FORMS
        ================================================= */

        .form-grid {
            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            color: #38536d;
            font-size: 13px;
            font-weight: 600;
        }

        input,
        textarea,
        select {
            width: 100%;

            padding: 11px 12px;

            border: 1px solid #d6e3f0;

            border-radius: 8px;

            background: #fbfdff;

            color: #19324c;

            font-family: inherit;

            outline: none;

            font-size: 14px;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #75a9d4;

            box-shadow:
                0 0 0 3px rgba(69, 130, 181, .10);
        }

        textarea {
            min-height: 105px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            gap: 9px;
            margin-top: 5px;
        }


        /* =================================================
           TABLE
        ================================================= */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 550px;
        }

        th {
            text-align: left;

            background: #edf5fc;

            color: #315775;

            font-size: 13px;

            padding: 12px;

            border-bottom: 1px solid #d8e6f2;
        }

        td {
            padding: 12px;

            border-bottom: 1px solid #e6eef6;

            color: #435d75;

            font-size: 14px;

            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .action-buttons form {
            display: flex;
            gap: 6px;
            align-items: center;
        }


        /* =================================================
           PROFILE
        ================================================= */

        .profile-preview {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 18px;
        }

        .profile-preview img {
            width: 85px;
            height: 85px;

            border-radius: 50%;

            object-fit: cover;

            border: 4px solid #e1effb;
        }

        .profile-preview .no-image {
            width: 85px;
            height: 85px;

            border-radius: 50%;

            background: #e8f3fc;

            color: #35719f;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 30px;

            border: 4px solid #dcecf9;
        }

        .file-note {
            color: #7a8da1;
            font-size: 12px;
            margin-top: 4px;
        }


        /* =================================================
           EMPTY
        ================================================= */

        .empty {
            text-align: center;

            padding: 25px;

            color: #8a9aac;

            font-size: 14px;
        }


        /* =================================================
           TAG
        ================================================= */

        .tag {
            display: inline-block;

            background: #e7f2fc;

            color: #2b648f;

            border-radius: 20px;

            padding: 4px 9px;

            font-size: 12px;

            margin: 2px;
        }


        /* =================================================
           FOOTER
        ================================================= */

        .footer {
            text-align: center;

            color: #8293a4;

            font-size: 12px;

            padding: 10px 0 25px;
        }


        /* =================================================
           RESPONSIVE - 1000px
        ================================================= */

        @media (max-width: 1100px) {

            .stats {
                grid-template-columns:
                    repeat(3, minmax(0, 1fr));
            }

        }


        /* =================================================
           RESPONSIVE - 800px
        ================================================= */

        @media (max-width: 800px) {

            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;
            }

            .topbar {
                left: 210px;
            }

            .content {
                padding-left: 18px;
                padding-right: 18px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .stats {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .admin-name {
                display: none;
            }

        }


        /* =================================================
           RESPONSIVE - 600px
        ================================================= */

        @media (max-width: 600px) {

            .sidebar {
                position: relative;

                width: 100%;
                height: auto;

                padding: 15px;
            }

            .brand {
                padding-bottom: 14px;
            }

            .sidebar nav {
                display: grid;

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap: 5px;
            }

            .main {
                margin-left: 0;
            }

            .topbar {
                position: sticky;

                left: 0;

                height: auto;

                min-height: 65px;

                padding: 12px 15px;
            }

            .topbar h1 {
                font-size: 18px;
            }

            .topbar-right .btn {
                padding: 8px 10px;
                font-size: 12px;
            }

            .content {
                padding: 20px 12px 30px;
            }

            .stats {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .stat-card {
                padding: 14px;
            }

            .stat-card strong {
                font-size: 21px;
            }

            .section {
                padding: 16px;
            }

            .section-title {
                align-items: flex-start;
                flex-direction: column;
            }

            .profile-preview {
                align-items: flex-start;
            }

        }


        /* =================================================
           RESPONSIVE - 480px
        ================================================= */

        @media (max-width: 480px) {

            .sidebar nav {
                grid-template-columns: 1fr;
            }

            .topbar {
                flex-direction: column;
                align-items: stretch;
                gap: 9px;
            }

            .topbar-right {
                justify-content: space-between;
            }

            .stats {
                grid-template-columns: 1fr 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
            }

            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .action-buttons form {
                width: 100%;
            }

            .action-buttons input {
                max-width: none !important;
            }

            .action-buttons .btn {
                width: 100%;
            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     SIDEBAR
====================================================== -->

<aside class="sidebar">

    <div class="brand">

        <h2>Shahbaz Portfolio</h2>

        <p>Admin Dashboard</p>

    </div>


    <nav>

        <a href="#dashboard">Dashboard</a>

        <a href="#profile">Profile</a>

        <a href="#skills">Skills</a>

        <a href="#projects">Projects</a>

        <a href="#experience">Experience</a>

        <a href="#education">Education</a>

        <a href="#highlights">Highlights</a>

        <a href="#languages">Languages</a>

        <a href="#certificates">Certificates</a>

        <a href="#contact">Contact</a>

        <a href="#password">Password</a>

    </nav>

</aside>


<!-- =====================================================
     MAIN
====================================================== -->

<main class="main">


<!-- =====================================================
     TOPBAR
====================================================== -->

<header class="topbar">

    <h1>Admin Dashboard</h1>

    <div class="topbar-right">

        <span class="admin-name">
            Welcome, <?= clean($adminUsername) ?>
        </span>

        <a
            href="../index.php"
            target="_blank"
            class="btn btn-secondary"
        >
            View Portfolio
        </a>

        <a
            href="logout.php"
            class="btn btn-dark"
        >
            Logout
        </a>

    </div>

</header>


<!-- =====================================================
     CONTENT
====================================================== -->

<div class="content" id="dashboard">


<?php if ($msg !== ''): ?>

    <div class="alert alert-success">
        <?= clean($msg) ?>
    </div>

<?php endif; ?>


<?php if ($error !== ''): ?>

    <div class="alert alert-error">
        <?= clean($error) ?>
    </div>

<?php endif; ?>


<!-- =====================================================
     STATS
====================================================== -->

<div class="stats">

    <div class="stat-card">
        <span>Skills</span>
        <strong><?= count($skills) ?></strong>
    </div>

    <div class="stat-card">
        <span>Projects</span>
        <strong><?= count($projects) ?></strong>
    </div>

    <div class="stat-card">
        <span>Experience</span>
        <strong><?= count($experience) ?></strong>
    </div>

    <div class="stat-card">
        <span>Education</span>
        <strong><?= count($education) ?></strong>
    </div>

    <div class="stat-card">
        <span>Certificates</span>
        <strong><?= count($certificates) ?></strong>
    </div>

</div>


<!-- =====================================================
     PROFILE
====================================================== -->

<section class="section" id="profile">

    <div class="section-title">

        <div>
            <h2>Profile Information</h2>
            <p>Update your main portfolio information.</p>
        </div>

    </div>


    <div class="profile-preview">

        <?php if ($profilePic !== ''): ?>

            <img
                src="uploads/profile/<?= clean($profilePic) ?>"
                alt="Profile"
            >

        <?php else: ?>

            <div class="no-image">
                👤
            </div>

        <?php endif; ?>


        <div>

            <strong>
                <?= clean($info['name'] ?? 'Your Name') ?>
            </strong>

            <p class="file-note">
                <?= clean($info['title'] ?? 'Professional Title') ?>
            </p>

            <?php if ($cvFile !== ''): ?>

                <p class="file-note">
                    CV uploaded:
                    <?= clean($cvFile) ?>
                </p>

            <?php endif; ?>

        </div>

    </div>


    <form method="POST" enctype="multipart/form-data">

        <input
            type="hidden"
            name="action"
            value="save_profile"
        >


        <div class="form-grid">

            <div class="form-group">

                <label>Name</label>

                <input
                    type="text"
                    name="name"
                    value="<?= clean($info['name'] ?? '') ?>"
                    placeholder="Your name"
                    required
                >

            </div>


            <div class="form-group">

                <label>Professional Title</label>

                <input
                    type="text"
                    name="title"
                    value="<?= clean($info['title'] ?? '') ?>"
                    placeholder="Software Engineer"
                    required
                >

            </div>


            <div class="form-group full">

                <label>Bio</label>

                <textarea
                    name="bio"
                    placeholder="Write your professional bio..."
                ><?= clean($info['bio'] ?? '') ?></textarea>

            </div>


            <div class="form-group">

                <label>Profile Picture</label>

                <input
                    type="file"
                    name="profile_pic"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <span class="file-note">
                    JPG, JPEG, PNG or WEBP
                </span>

            </div>


            <div class="form-group">

                <label>CV / Resume</label>

                <input
                    type="file"
                    name="cv_file"
                    accept=".pdf,.doc,.docx"
                >

                <span class="file-note">
                    PDF, DOC or DOCX
                </span>

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Save Profile
                </button>

            </div>

        </div>

    </form>

</section>


<!-- =====================================================
     SKILLS
====================================================== -->

<section class="section" id="skills">

    <div class="section-title">

        <div>
            <h2>Skills</h2>
            <p>Add and manage your technical skills.</p>
        </div>

    </div>


    <!-- ADD SKILL -->

    <form method="POST">

        <input
            type="hidden"
            name="action"
            value="add_skill"
        >


        <div class="form-grid">

            <div class="form-group">

                <label>Skill Name</label>

                <input
                    type="text"
                    name="skill"
                    placeholder="PHP"
                    required
                >

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Skill
                </button>

            </div>

        </div>

    </form>


    <br>


    <!-- SKILLS TABLE -->

    <div class="table-wrapper">

        <table>

            <thead>

                <tr>

                    <th>Skill</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

            <?php if (!empty($skills)): ?>

                <?php foreach ($skills as $skill): ?>

                    <tr>

                        <td>
                            <?= clean($skill['skill_name'] ?? '') ?>
                        </td>


                        <td>

                            <div class="action-buttons">


                                <!-- UPDATE -->

                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="update_skill"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$skill['id'] ?>"
                                    >

                                    <input
                                        type="text"
                                        name="skill"
                                        value="<?= clean($skill['skill_name'] ?? '') ?>"
                                        required
                                        style="max-width:180px;"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-secondary"
                                    >
                                        Update
                                    </button>

                                </form>


                                <!-- DELETE -->

                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="type"
                                        value="skill"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$skill['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Delete this skill?')"
                                    >
                                        Delete
                                    </button>

                                </form>


                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td
                        colspan="2"
                        class="empty"
                    >
                        No skills added yet.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>


<!-- =====================================================
     PROJECTS
====================================================== -->

<section class="section" id="projects">

    <div class="section-title">

        <div>
            <h2>Projects</h2>
            <p>Manage your portfolio projects.</p>
        </div>

    </div>


    <!-- ADD PROJECT -->

    <form method="POST">

        <input
            type="hidden"
            name="action"
            value="add_project"
        >


        <div class="form-grid">

            <div class="form-group">

                <label>Icon</label>

                <input
                    type="text"
                    name="icon"
                    placeholder="💻"
                >

            </div>


            <div class="form-group">

                <label>Project Title</label>

                <input
                    type="text"
                    name="title"
                    placeholder="Portfolio Website"
                    required
                >

            </div>


            <div class="form-group full">

                <label>Description</label>

                <textarea
                    name="description"
                    placeholder="Project description..."
                ></textarea>

            </div>


            <div class="form-group">

                <label>Tags</label>

                <input
                    type="text"
                    name="tags"
                    placeholder="PHP, MySQL, JavaScript"
                >

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Project
                </button>

            </div>

        </div>

    </form>


    <br>


    <div class="table-wrapper">

        <table>

            <thead>

                <tr>

                    <th>Project</th>
                    <th>Description</th>
                    <th>Tags</th>
                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

            <?php if (!empty($projects)): ?>

                <?php foreach ($projects as $project): ?>

                    <tr>

                        <td>

                            <?= clean($project['icon'] ?? '') ?>

                            <strong>
                                <?= clean($project['title'] ?? '') ?>
                            </strong>

                        </td>


                        <td>
                            <?= clean($project['description'] ?? '') ?>
                        </td>


                        <td>

                            <?php

                            $tags = array_filter(
                                array_map(
                                    'trim',
                                    explode(
                                        ',',
                                        $project['tags'] ?? ''
                                    )
                                )
                            );

                            foreach ($tags as $tag):

                            ?>

                                <span class="tag">
                                    <?= clean($tag) ?>
                                </span>

                            <?php endforeach; ?>

                        </td>


                        <td>

                            <div class="action-buttons">

                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="update_project"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$project['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="icon"
                                        value="<?= clean($project['icon'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="title"
                                        value="<?= clean($project['title'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="description"
                                        value="<?= clean($project['description'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="tags"
                                        value="<?= clean($project['tags'] ?? '') ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-secondary"
                                    >
                                        Update
                                    </button>

                                </form>


                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="type"
                                        value="project"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$project['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Delete this project?')"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="4" class="empty">
                        No projects added yet.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>


<!-- =====================================================
     EXPERIENCE
====================================================== -->

<section class="section" id="experience">

    <div class="section-title">

        <div>
            <h2>Experience</h2>
            <p>Manage internships and work experience.</p>
        </div>

    </div>


    <form method="POST">

        <input
            type="hidden"
            name="action"
            value="add_experience"
        >


        <div class="form-grid">

            <div class="form-group">

                <label>Date / Duration</label>

                <input
                    type="text"
                    name="date"
                    placeholder="2026"
                >

            </div>


            <div class="form-group">

                <label>Role</label>

                <input
                    type="text"
                    name="role"
                    placeholder="Software Engineer Intern"
                    required
                >

            </div>


            <div class="form-group">

                <label>Company / Organization</label>

                <input
                    type="text"
                    name="company"
                    placeholder="Company Name"
                >

            </div>


            <div class="form-group full">

                <label>Description</label>

                <textarea
                    name="description"
                    placeholder="Describe your responsibilities..."
                ></textarea>

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Experience
                </button>

            </div>

        </div>

    </form>


    <br>


    <div class="table-wrapper">

        <table>

            <thead>

                <tr>
                    <th>Date</th>
                    <th>Role</th>
                    <th>Company</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>

            </thead>


            <tbody>

            <?php if (!empty($experience)): ?>

                <?php foreach ($experience as $item): ?>

                    <tr>

                        <td>
                            <?= clean($item['date'] ?? '') ?>
                        </td>

                        <td>
                            <?= clean($item['role'] ?? '') ?>
                        </td>

                        <td>
                            <?= clean($item['company'] ?? '') ?>
                        </td>

                        <td>
                            <?= clean($item['description'] ?? '') ?>
                        </td>

                        <td>

                            <div class="action-buttons">

                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="update_experience"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="date"
                                        value="<?= clean($item['date'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="role"
                                        value="<?= clean($item['role'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="company"
                                        value="<?= clean($item['company'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="description"
                                        value="<?= clean($item['description'] ?? '') ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-secondary"
                                    >
                                        Update
                                    </button>

                                </form>


                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="type"
                                        value="experience"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Delete this experience?')"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5" class="empty">
                        No experience added yet.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>


<!-- =====================================================
     EDUCATION
====================================================== -->

<section class="section" id="education">

    <div class="section-title">

        <div>
            <h2>Education</h2>
            <p>Manage your academic qualifications.</p>
        </div>

    </div>


    <form method="POST">

        <input
            type="hidden"
            name="action"
            value="add_education"
        >


        <div class="form-grid">

            <div class="form-group">

                <label>Degree</label>

                <input
                    type="text"
                    name="degree"
                    placeholder="BS Software Engineering"
                    required
                >

            </div>


            <div class="form-group">

                <label>Institution</label>

                <input
                    type="text"
                    name="institution"
                    placeholder="University Name"
                >

            </div>


            <div class="form-group">

                <label>Duration</label>

                <input
                    type="text"
                    name="duration"
                    placeholder="2022 - 2026"
                >

            </div>


            <div class="form-group">

                <label>Result / CGPA</label>

                <input
                    type="text"
                    name="result"
                    placeholder="3.85 CGPA"
                >

            </div>


            <div class="form-group full">

                <label>Description</label>

                <textarea
                    name="description"
                    placeholder="Additional education details..."
                ></textarea>

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Education
                </button>

            </div>

        </div>

    </form>


    <br>


    <div class="table-wrapper">

        <table>

            <thead>

                <tr>
                    <th>Degree</th>
                    <th>Institution</th>
                    <th>Duration</th>
                    <th>Result</th>
                    <th>Actions</th>
                </tr>

            </thead>


            <tbody>

            <?php if (!empty($education)): ?>

                <?php foreach ($education as $item): ?>

                    <tr>

                        <td>
                            <?= clean($item['degree'] ?? '') ?>
                        </td>

                        <td>
                            <?= clean($item['institution'] ?? '') ?>
                        </td>

                        <td>
                            <?= clean($item['duration'] ?? '') ?>
                        </td>

                        <td>
                            <?= clean($item['result'] ?? '') ?>
                        </td>

                        <td>

                            <div class="action-buttons">

                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="update_education"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="degree"
                                        value="<?= clean($item['degree'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="institution"
                                        value="<?= clean($item['institution'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="duration"
                                        value="<?= clean($item['duration'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="result"
                                        value="<?= clean($item['result'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="description"
                                        value="<?= clean($item['description'] ?? '') ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-secondary"
                                    >
                                        Update
                                    </button>

                                </form>


                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="type"
                                        value="education"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Delete this education record?')"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5" class="empty">
                        No education added yet.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>


<!-- =====================================================
     HIGHLIGHTS
====================================================== -->

<section class="section" id="highlights">

    <div class="section-title">

        <div>
            <h2>Highlights</h2>
            <p>Manage achievements and important highlights.</p>
        </div>

    </div>


    <form method="POST">

        <input
            type="hidden"
            name="action"
            value="add_highlight"
        >


        <div class="form-grid">

            <div class="form-group">

                <label>Icon</label>

                <input
                    type="text"
                    name="icon"
                    value="✦"
                >

            </div>


            <div class="form-group">

                <label>Title</label>

                <input
                    type="text"
                    name="title"
                    placeholder="Academic Achievement"
                    required
                >

            </div>


            <div class="form-group full">

                <label>Description</label>

                <textarea
                    name="description"
                    placeholder="Describe the achievement..."
                ></textarea>

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Highlight
                </button>

            </div>

        </div>

    </form>


    <br>


    <div class="table-wrapper">

        <table>

            <thead>

                <tr>
                    <th>Icon</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>

            </thead>


            <tbody>

            <?php if (!empty($highlights)): ?>

                <?php foreach ($highlights as $item): ?>

                    <tr>

                        <td>
                            <?= clean($item['icon'] ?? '✦') ?>
                        </td>

                        <td>
                            <?= clean($item['title'] ?? '') ?>
                        </td>

                        <td>
                            <?= clean($item['description'] ?? '') ?>
                        </td>

                        <td>

                            <div class="action-buttons">

                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="update_highlight"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="icon"
                                        value="<?= clean($item['icon'] ?? '✦') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="title"
                                        value="<?= clean($item['title'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="description"
                                        value="<?= clean($item['description'] ?? '') ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-secondary"
                                    >
                                        Update
                                    </button>

                                </form>


                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="type"
                                        value="highlight"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Delete this highlight?')"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="4" class="empty">
                        No highlights added yet.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>


<!-- =====================================================
     LANGUAGES
====================================================== -->

<section class="section" id="languages">

    <div class="section-title">

        <div>
            <h2>Languages</h2>
            <p>Manage languages and proficiency levels.</p>
        </div>

    </div>


    <form method="POST">

        <input
            type="hidden"
            name="action"
            value="add_language"
        >


        <div class="form-grid">

            <div class="form-group">

                <label>Language</label>

                <input
                    type="text"
                    name="language"
                    placeholder="English"
                    required
                >

            </div>


            <div class="form-group">

                <label>Level</label>

                <input
                    type="text"
                    name="level"
                    placeholder="Fluent"
                >

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Language
                </button>

            </div>

        </div>

    </form>


    <br>


    <div class="table-wrapper">

        <table>

            <thead>

                <tr>
                    <th>Language</th>
                    <th>Level</th>
                    <th>Actions</th>
                </tr>

            </thead>


            <tbody>

            <?php if (!empty($languages)): ?>

                <?php foreach ($languages as $item): ?>

                    <tr>

                        <td>
                            <?= clean($item['language'] ?? '') ?>
                        </td>

                        <td>
                            <?= clean($item['level'] ?? '') ?>
                        </td>

                        <td>

                            <div class="action-buttons">

                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="update_language"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="language"
                                        value="<?= clean($item['language'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="level"
                                        value="<?= clean($item['level'] ?? '') ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-secondary"
                                    >
                                        Update
                                    </button>

                                </form>


                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="type"
                                        value="language"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Delete this language?')"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="3" class="empty">
                        No languages added yet.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>


<!-- =====================================================
     CERTIFICATES
====================================================== -->

<section class="section" id="certificates">

    <div class="section-title">

        <div>
            <h2>Certificates & Achievements</h2>
            <p>Manage your certifications.</p>
        </div>

    </div>


    <form method="POST">

        <input
            type="hidden"
            name="action"
            value="add_certificate"
        >


        <div class="form-grid">

            <div class="form-group">

                <label>Icon</label>

                <input
                    type="text"
                    name="icon"
                    placeholder="🏆"
                >

            </div>


            <div class="form-group">

                <label>Certificate Title</label>

                <input
                    type="text"
                    name="title"
                    placeholder="Web Development Certificate"
                    required
                >

            </div>


            <div class="form-group">

                <label>Issuer</label>

                <input
                    type="text"
                    name="issuer"
                    placeholder="Coursera"
                >

            </div>


            <div class="form-group">

                <label>Year</label>

                <input
                    type="text"
                    name="year"
                    placeholder="2026"
                >

            </div>


            <div class="form-group full">

                <label>Description</label>

                <textarea
                    name="description"
                    placeholder="Certificate details..."
                ></textarea>

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Certificate
                </button>

            </div>

        </div>

    </form>


    <br>


    <div class="table-wrapper">

        <table>

            <thead>

                <tr>
                    <th>Icon</th>
                    <th>Certificate</th>
                    <th>Issuer</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>

            </thead>


            <tbody>

            <?php if (!empty($certificates)): ?>

                <?php foreach ($certificates as $item): ?>

                    <tr>

                        <td>
                            <?= clean($item['icon'] ?? '') ?>
                        </td>

                        <td>
                            <?= clean($item['title'] ?? '') ?>
                        </td>

                        <td>
                            <?= clean($item['issuer'] ?? '') ?>
                        </td>

                        <td>
                            <?= clean($item['year'] ?? '') ?>
                        </td>

                        <td>

                            <div class="action-buttons">

                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="update_certificate"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="icon"
                                        value="<?= clean($item['icon'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="title"
                                        value="<?= clean($item['title'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="issuer"
                                        value="<?= clean($item['issuer'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="year"
                                        value="<?= clean($item['year'] ?? '') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="description"
                                        value="<?= clean($item['description'] ?? '') ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-secondary"
                                    >
                                        Update
                                    </button>

                                </form>


                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="type"
                                        value="certificate"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Delete this certificate?')"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5" class="empty">
                        No certificates added yet.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>


<!-- =====================================================
     CONTACT
====================================================== -->

<section class="section" id="contact">

    <div class="section-title">

        <div>
            <h2>Contact Information</h2>
            <p>Update your contact and social links.</p>
        </div>

    </div>


    <form method="POST">

        <input
            type="hidden"
            name="action"
            value="save_contact"
        >


        <div class="form-grid">

            <div class="form-group">

                <label>Gmail / Email</label>

                <input
                    type="email"
                    name="email"
                    value="<?= clean($email) ?>"
                    placeholder="example@gmail.com"
                >

            </div>


            <div class="form-group">

                <label>LinkedIn</label>

                <input
                    type="url"
                    name="linkedin"
                    value="<?= clean($linkedin) ?>"
                    placeholder="https://linkedin.com/in/username"
                >

            </div>


            <div class="form-group">

                <label>Instagram</label>

                <input
                    type="url"
                    name="instagram"
                    value="<?= clean($instagram) ?>"
                    placeholder="https://instagram.com/username"
                >

            </div>


            <div class="form-group">

                <label>GitHub</label>

                <input
                    type="url"
                    name="github"
                    value="<?= clean($github) ?>"
                    placeholder="https://github.com/username"
                >

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Save Contact Information
                </button>

            </div>

        </div>

    </form>

</section>


<!-- =====================================================
     PASSWORD
====================================================== -->

<section class="section" id="password">

    <div class="section-title">

        <div>
            <h2>Change Password</h2>
            <p>Update your admin account password.</p>
        </div>

    </div>


    <form method="POST">

        <input
            type="hidden"
            name="action"
            value="change_password"
        >


        <div class="form-grid">

            <div class="form-group full">

                <label>Current Password</label>

                <input
                    type="password"
                    name="current_password"
                    placeholder="Current password"
                    required
                >

            </div>


            <div class="form-group">

                <label>New Password</label>

                <input
                    type="password"
                    name="new_password"
                    placeholder="Minimum 6 characters"
                    minlength="6"
                    required
                >

            </div>


            <div class="form-group">

                <label>Confirm New Password</label>

                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Repeat new password"
                    minlength="6"
                    required
                >

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Change Password
                </button>

            </div>

        </div>

    </form>

</section>


<div class="footer">

    Shahbaz Portfolio Admin Dashboard

</div>


</div>

</main>


</body>

</html>