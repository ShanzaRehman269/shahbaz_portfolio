<?php
// FIX: Check karo session pehle se hai ya nahi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php'; // is file me $conn hona chahiye

// 1. Check Login
function checkLogin(){
    if(!isset($_SESSION['admin_login'])){
        header("Location: login.php");
        exit();
    }
}

// 2. Get Info - Profile
function getInfo($conn){
    $result = $conn->query("SELECT * FROM info WHERE id=1 LIMIT 1");
    if($result->num_rows > 0){
        return $result->fetch_assoc();
    } else {
        return ['name'=>'', 'title'=>'', 'bio'=>'', 'profile_pic'=>'', 'cv_file'=>''];
    }
}

// 3. Update Info
function updateInfo($conn, $name, $title, $bio, $pic, $cv){
    $stmt = $conn->prepare("UPDATE info SET name=?, title=?, bio=?, profile_pic=?, cv_file=? WHERE id=1");
    $stmt->bind_param("sssss", $name, $title, $bio, $pic, $cv);
    return $stmt->execute();
}

// 4. Skills
function getSkills($conn){ 
    return $conn->query("SELECT * FROM skills ORDER BY id DESC"); 
}
function addSkill($conn, $skill){
    $stmt = $conn->prepare("INSERT INTO skills (skill_name) VALUES (?)");
    $stmt->bind_param("s", $skill);
    return $stmt->execute();
}

// 5. Projects
function getProjects($conn){ 
    return $conn->query("SELECT * FROM projects ORDER BY id DESC"); 
}
function addProject($conn, $icon, $title, $desc, $tags){
    $stmt = $conn->prepare("INSERT INTO projects (icon, title, description, tags) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $icon, $title, $desc, $tags);
    return $stmt->execute();
}

// 6. Experience
function getExperience($conn){ 
    return $conn->query("SELECT * FROM experience ORDER BY id DESC"); 
}
function addExperience($conn, $date, $role, $desc){
    $stmt = $conn->prepare("INSERT INTO experience (date, role, description) VALUES (?,?,?)");
    $stmt->bind_param("sss", $date, $role, $desc);
    return $stmt->execute();
}

// 7. Delete Data
function deleteData($conn, $table, $id){
    // security: sirf allowed tables delete hon
    $allowed = ['skills', 'projects', 'experience'];
    if(!in_array($table, $allowed)) return false;
    
    $stmt = $conn->prepare("DELETE FROM $table WHERE id=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// 8. Password Check
function checkPassword($conn, $user, $pass){
    $stmt = $conn->prepare("SELECT password FROM admin WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $user); 
    $stmt->execute(); 
    $res = $stmt->get_result()->fetch_assoc();
    if($res){
        return password_verify($pass, $res['password']);
    }
    return false;
}

// 9. Update Password
function updatePassword($conn, $user, $new){
    $hash = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE admin SET password=? WHERE username=?");
    $stmt->bind_param("ss", $hash, $user);
    return $stmt->execute();
}
// 10. Contact
function getContact($conn){ 
    return $conn->query("SELECT * FROM contact ORDER BY id ASC"); 
}
function updateContact($conn, $id, $value, $link){
    $stmt = $conn->prepare("UPDATE contact SET value=?, link=? WHERE id=?");
    $stmt->bind_param("ssi", $value, $link, $id);
    return $stmt->execute();
}
?>