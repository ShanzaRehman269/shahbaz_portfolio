<?php
/**
 * SHAHBAZ PORTFOLIO
 * Database / application functions
 * File: admin/includes/functions.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';


/* =========================================================
   HELPER
========================================================= */

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}


/* =========================================================
   LOGIN
========================================================= */

function checkLogin(): void
{
    if (empty($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit;
    }
}


/* =========================================================
   PROFILE / INFO
========================================================= */

function getInfo()
{
    global $conn;

    $result = $conn->query(
        "SELECT * FROM info ORDER BY id ASC LIMIT 1"
    );

    return $result ? $result->fetch_assoc() : null;
}


function updateInfo(
    $name,
    $title,
    $bio,
    $profile_pic = null,
    $cv_file = null
): bool {

    global $conn;

    $current = getInfo();

    if ($current) {

        if ($profile_pic === null || $profile_pic === '') {
            $profile_pic = $current['profile_pic'] ?? '';
        }

        if ($cv_file === null || $cv_file === '') {
            $cv_file = $current['cv_file'] ?? '';
        }

        $stmt = $conn->prepare(
            "UPDATE info
             SET name = ?,
                 title = ?,
                 bio = ?,
                 profile_pic = ?,
                 cv_file = ?
             WHERE id = ?"
        );

        if (!$stmt) {
            return false;
        }

        $id = (int)$current['id'];

        $stmt->bind_param(
            "sssssi",
            $name,
            $title,
            $bio,
            $profile_pic,
            $cv_file,
            $id
        );

    } else {

        $profile_pic = $profile_pic ?? '';
        $cv_file = $cv_file ?? '';

        $stmt = $conn->prepare(
            "INSERT INTO info
             (name, title, bio, profile_pic, cv_file)
             VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "sssss",
            $name,
            $title,
            $bio,
            $profile_pic,
            $cv_file
        );
    }

    return $stmt->execute();
}


/* =========================================================
   SKILLS
========================================================= */

function getSkills(): array
{
    global $conn;

    $rows = [];

    $result = $conn->query(
        "SELECT * FROM skills ORDER BY id ASC"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    return $rows;
}


function addSkill($skill_name): bool
{
    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO skills (skill_name)
         VALUES (?)"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("s", $skill_name);

    return $stmt->execute();
}


function updateSkill($id, $skill_name): bool
{
    global $conn;

    $stmt = $conn->prepare(
        "UPDATE skills
         SET skill_name = ?
         WHERE id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $id = (int)$id;

    $stmt->bind_param(
        "si",
        $skill_name,
        $id
    );

    return $stmt->execute();
}


/* =========================================================
   PROJECTS
========================================================= */

function getProjects(): array
{
    global $conn;

    $rows = [];

    $result = $conn->query(
        "SELECT * FROM projects ORDER BY id DESC"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    return $rows;
}


function addProject(
    $icon,
    $title,
    $description,
    $tags
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO projects
         (icon, title, description, tags)
         VALUES (?, ?, ?, ?)"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "ssss",
        $icon,
        $title,
        $description,
        $tags
    );

    return $stmt->execute();
}


function updateProject(
    $id,
    $icon,
    $title,
    $description,
    $tags
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "UPDATE projects
         SET icon = ?,
             title = ?,
             description = ?,
             tags = ?
         WHERE id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $id = (int)$id;

    $stmt->bind_param(
        "ssssi",
        $icon,
        $title,
        $description,
        $tags,
        $id
    );

    return $stmt->execute();
}


/* =========================================================
   EXPERIENCE
========================================================= */

function getExperience(): array
{
    global $conn;

    $rows = [];

    $result = $conn->query(
        "SELECT * FROM experience ORDER BY id ASC"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    return $rows;
}


function addExperience(
    $date,
    $role,
    $company,
    $description
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO experience
         (date, role, company, description)
         VALUES (?, ?, ?, ?)"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "ssss",
        $date,
        $role,
        $company,
        $description
    );

    return $stmt->execute();
}


function updateExperience(
    $id,
    $date,
    $role,
    $company,
    $description
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "UPDATE experience
         SET date = ?,
             role = ?,
             company = ?,
             description = ?
         WHERE id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $id = (int)$id;

    $stmt->bind_param(
        "ssssi",
        $date,
        $role,
        $company,
        $description,
        $id
    );

    return $stmt->execute();
}


/* =========================================================
   EDUCATION
========================================================= */

function getEducation(): array
{
    global $conn;

    $rows = [];

    $result = $conn->query(
        "SELECT * FROM education ORDER BY id ASC"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    return $rows;
}


function addEducation(
    $degree,
    $institution,
    $duration,
    $result = '',
    $description = ''
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO education
         (degree, institution, duration, result, description)
         VALUES (?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "sssss",
        $degree,
        $institution,
        $duration,
        $result,
        $description
    );

    return $stmt->execute();
}


function updateEducation(
    $id,
    $degree,
    $institution,
    $duration,
    $result = '',
    $description = ''
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "UPDATE education
         SET degree = ?,
             institution = ?,
             duration = ?,
             result = ?,
             description = ?
         WHERE id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $id = (int)$id;

    $stmt->bind_param(
        "sssssi",
        $degree,
        $institution,
        $duration,
        $result,
        $description,
        $id
    );

    return $stmt->execute();
}


/* =========================================================
   HIGHLIGHTS
========================================================= */

function getHighlights(): array
{
    global $conn;

    $rows = [];

    $result = $conn->query(
        "SELECT * FROM highlights ORDER BY id ASC"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    return $rows;
}


function addHighlight(
    $title,
    $description,
    $icon = '✦'
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO highlights
         (title, description, icon)
         VALUES (?, ?, ?)"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "sss",
        $title,
        $description,
        $icon
    );

    return $stmt->execute();
}


function updateHighlight(
    $id,
    $title,
    $description,
    $icon = '✦'
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "UPDATE highlights
         SET title = ?,
             description = ?,
             icon = ?
         WHERE id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $id = (int)$id;

    $stmt->bind_param(
        "sssi",
        $title,
        $description,
        $icon,
        $id
    );

    return $stmt->execute();
}


/* =========================================================
   LANGUAGES
========================================================= */

function getLanguages(): array
{
    global $conn;

    $rows = [];

    $result = $conn->query(
        "SELECT * FROM languages ORDER BY id ASC"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    return $rows;
}


function addLanguage(
    $language,
    $level = ''
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO languages
         (language, level)
         VALUES (?, ?)"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "ss",
        $language,
        $level
    );

    return $stmt->execute();
}


function updateLanguage(
    $id,
    $language,
    $level = ''
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "UPDATE languages
         SET language = ?,
             level = ?
         WHERE id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $id = (int)$id;

    $stmt->bind_param(
        "ssi",
        $language,
        $level,
        $id
    );

    return $stmt->execute();
}


/* =========================================================
   CERTIFICATES & ACHIEVEMENTS
========================================================= */

function getCertificates(): array
{
    global $conn;

    $rows = [];

    $result = $conn->query(
        "SELECT * FROM certificates ORDER BY id DESC"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    return $rows;
}


function addCertificate(
    $icon,
    $title,
    $issuer = '',
    $year = '',
    $description = ''
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "INSERT INTO certificates
         (icon, title, issuer, year, description)
         VALUES (?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "sssss",
        $icon,
        $title,
        $issuer,
        $year,
        $description
    );

    return $stmt->execute();
}


function updateCertificate(
    $id,
    $icon,
    $title,
    $issuer = '',
    $year = '',
    $description = ''
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "UPDATE certificates
         SET icon = ?,
             title = ?,
             issuer = ?,
             year = ?,
             description = ?
         WHERE id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $id = (int)$id;

    $stmt->bind_param(
        "sssssi",
        $icon,
        $title,
        $issuer,
        $year,
        $description,
        $id
    );

    return $stmt->execute();
}


/* =========================================================
   CONTACT
========================================================= */

function getContact(): array
{
    global $conn;

    $rows = [];

    $result = $conn->query(
        "SELECT * FROM contact ORDER BY id ASC"
    );

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    return $rows;
}


function updateContact(
    $type,
    $value,
    $link = ''
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "SELECT id
         FROM contact
         WHERE type = ?
         LIMIT 1"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "s",
        $type
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result && ($row = $result->fetch_assoc())) {

        $id = (int)$row['id'];

        $update = $conn->prepare(
            "UPDATE contact
             SET value = ?,
                 link = ?
             WHERE id = ?"
        );

        if (!$update) {
            return false;
        }

        $update->bind_param(
            "ssi",
            $value,
            $link,
            $id
        );

        return $update->execute();
    }

    $insert = $conn->prepare(
        "INSERT INTO contact
         (type, value, link)
         VALUES (?, ?, ?)"
    );

    if (!$insert) {
        return false;
    }

    $insert->bind_param(
        "sss",
        $type,
        $value,
        $link
    );

    return $insert->execute();
}


/* =========================================================
   DELETE
========================================================= */

function deleteData(
    $table,
    $id
): bool {

    global $conn;

    /*
     * Only these tables can be deleted through dashboard.
     * This prevents arbitrary table names being used.
     */
    $allowedTables = [
        'skills',
        'projects',
        'experience',
        'education',
        'highlights',
        'languages',
        'certificates',
        'contact'
    ];

    if (!in_array($table, $allowedTables, true)) {
        return false;
    }

    $id = (int)$id;

    $stmt = $conn->prepare(
        "DELETE FROM {$table}
         WHERE id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "i",
        $id
    );

    return $stmt->execute();
}


/* =========================================================
   ADMIN PASSWORD
========================================================= */

function checkPassword(
    $username,
    $password
): bool {

    global $conn;

    $stmt = $conn->prepare(
        "SELECT id, password
         FROM admin
         WHERE username = ?
         LIMIT 1"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "s",
        $username
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if (!$result || !$result->num_rows) {
        $stmt->close();
        return false;
    }

    $admin = $result->fetch_assoc();

    $stmt->close();

    $storedPassword = $admin['password'] ?? '';


    /* Modern password_hash() password */

    if (password_verify(
        $password,
        $storedPassword
    )) {
        return true;
    }


    /* Old MD5 password support */

    if (
        strlen($storedPassword) === 32 &&
        md5($password) === $storedPassword
    ) {

        $newHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $update = $conn->prepare(
            "UPDATE admin
             SET password = ?
             WHERE id = ?"
        );

        if ($update) {

            $id = (int)$admin['id'];

            $update->bind_param(
                "si",
                $newHash,
                $id
            );

            $update->execute();
            $update->close();
        }

        return true;
    }

    return false;
}


function updatePassword(
    $username,
    $newPassword
): bool {

    global $conn;

    $hash = password_hash(
        $newPassword,
        PASSWORD_DEFAULT
    );

    $stmt = $conn->prepare(
        "UPDATE admin
         SET password = ?
         WHERE username = ?"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "ss",
        $hash,
        $username
    );

    $success = $stmt->execute();

    $stmt->close();

    return $success;
}


/* =========================================================
   SECURE PASSWORD RESET
========================================================= */

/**
 * Get admin account using the registered email.
 *
 * The email should be stored in the admin table.
 */
function getAdminByEmail($email)
{
    global $conn;

    $stmt = $conn->prepare(
        "SELECT id, username, email
         FROM admin
         WHERE email = ?
         LIMIT 1"
    );

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param(
        "s",
        $email
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if (!$result || $result->num_rows !== 1) {
        $stmt->close();
        return null;
    }

    $admin = $result->fetch_assoc();

    $stmt->close();

    return $admin;
}


/**
 * Create a secure password-reset token.
 *
 * Returns the plain token which is used only in the email link.
 * Only its SHA-256 hash is stored in the database.
 */
function createPasswordResetToken(
    $adminId,
    $expiryMinutes = 30
)
{
    global $conn;

    $adminId = (int)$adminId;

    if ($adminId <= 0) {
        return false;
    }

    /*
     * Cryptographically secure random token.
     */
    $token = bin2hex(
        random_bytes(32)
    );

    /*
     * Never store the usable token directly.
     */
    $tokenHash = hash(
        'sha256',
        $token
    );

    /*
     * Token expires after the specified number of minutes.
     */
    $expires = date(
        'Y-m-d H:i:s',
        time() + ((int)$expiryMinutes * 60)
    );

    $stmt = $conn->prepare(
        "UPDATE admin
         SET reset_token = ?,
             reset_expires = ?
         WHERE id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "ssi",
        $tokenHash,
        $expires,
        $adminId
    );

    if (!$stmt->execute()) {
        $stmt->close();
        return false;
    }

    $stmt->close();

    /*
     * Return the original token.
     * This is sent through the reset URL.
     */
    return $token;
}


/**
 * Find an admin using a reset token.
 *
 * The supplied token is hashed before comparing it
 * with the hash stored in the database.
 */
function getAdminByResetToken($token)
{
    global $conn;

    if (!is_string($token) || $token === '') {
        return null;
    }

    /*
     * Hash token received from the URL.
     */
    $tokenHash = hash(
        'sha256',
        $token
    );

    $stmt = $conn->prepare(
        "SELECT id, username, email
         FROM admin
         WHERE reset_token = ?
           AND reset_expires IS NOT NULL
           AND reset_expires > NOW()
         LIMIT 1"
    );

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param(
        "s",
        $tokenHash
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if (!$result || $result->num_rows !== 1) {
        $stmt->close();
        return null;
    }

    $admin = $result->fetch_assoc();

    $stmt->close();

    return $admin;
}


/**
 * Reset password using a valid reset token.
 *
 * The token is invalidated immediately after successful reset.
 */
function resetPasswordWithToken(
    $token,
    $newPassword
): bool {

    global $conn;

    if (!is_string($token) || $token === '') {
        return false;
    }

    if (!is_string($newPassword) || $newPassword === '') {
        return false;
    }

    /*
     * Check the token first.
     */
    $admin = getAdminByResetToken($token);

    if (!$admin) {
        return false;
    }

    /*
     * Hash the new password securely.
     */
    $passwordHash = password_hash(
        $newPassword,
        PASSWORD_DEFAULT
    );

    if ($passwordHash === false) {
        return false;
    }

    /*
     * Change password and invalidate token.
     *
     * reset_token = NULL
     * reset_expires = NULL
     *
     * This makes the reset link single-use.
     */
    $stmt = $conn->prepare(
        "UPDATE admin
         SET password = ?,
             reset_token = NULL,
             reset_expires = NULL
         WHERE id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $adminId = (int)$admin['id'];

    $stmt->bind_param(
        "si",
        $passwordHash,
        $adminId
    );

    $success = $stmt->execute();

    $stmt->close();

    return $success;
}


/**
 * Remove an existing password-reset token.
 *
 * Useful when you want to manually invalidate a token.
 */
function invalidatePasswordResetToken(
    $adminId
): bool {

    global $conn;

    $adminId = (int)$adminId;

    if ($adminId <= 0) {
        return false;
    }

    $stmt = $conn->prepare(
        "UPDATE admin
         SET reset_token = NULL,
             reset_expires = NULL
         WHERE id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "i",
        $adminId
    );

    $success = $stmt->execute();

    $stmt->close();

    return $success;
}

?>