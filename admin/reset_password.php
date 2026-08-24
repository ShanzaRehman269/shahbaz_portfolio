<?php 
require_once '../includes/config.php';
$error = ""; $success = "";

// Token valid hai ya nahi check karo
if(!isset($_GET['token'])){
    die("<div style='text-align:center;margin-top:100px;color:red;'>Invalid Request. <a href='forgot-password.php'>Go Back</a></div>");
}

$token = $_GET['token'];
$stmt = $conn->prepare("SELECT * FROM admins WHERE reset_token = ? AND reset_expiry > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if(!$admin){
    die("<div style='text-align:center;margin-top:100px;color:red;'>This link is Invalid or Expired. <a href='forgot-password.php'>Request new link</a></div>");
}

// Password update
if(isset($_POST['update_pass'])){
    $new_pass = trim($_POST['password']);
    $confirm_pass = trim($_POST['confirm_password']);
    
    if(empty($new_pass)){
        $error = "Password cannot be empty";
    } elseif($new_pass !== $confirm_pass){
        $error = "Passwords do not match";
    } elseif(strlen($new_pass) < 6){
        $error = "Password must be at least 6 characters";
    } else {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE admins SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        $stmt->bind_param("si", $hashed_pass, $admin['id']);
        
        if($stmt->execute()){
            $success = "Password Updated Successfully! <a href='login.php' style='color:#1e90ff;font-weight:700;'>Click here to Login</a>";
        } else {
            $error = "Database Error. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Reset Password - Shahbaz Portfolio</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
.box{width:420px;margin:120px auto;background:white;padding:40px;border-radius:16px;box-shadow:0 10px 30px rgba(30,144,255,0.25);}
.box h2{color:#1e90ff;text-align:center;margin-bottom:25px;font-size:24px;}
.box label{font-weight:600;color:#334155;}
.box input{width:100%;padding:12px;margin:8px 0 15px;border:2px solid #dbeafe;border-radius:8px;font-size:15px;}
.box input:focus{outline:none;border-color:#38bdf8;}
.box .btn{width:100%;border:none;cursor:pointer;padding:14px;font-size:16px;}
.alert{padding:12px;border-radius:8px;margin-bottom:15px;text-align:center;font-weight:600;}
.alert-success{background:#e0f2fe;color:#0369a1;}
.alert-error{background:#fee2e2;color:#dc2626;}
</style>
</head>
<body style="background:linear-gradient(135deg,#1e90ff,#38bdf8);">
<div class="box">
  <h2><i class="fas fa-key"></i> Reset Your Password</h2>
  
  <?php if($error) echo "<div class='alert alert-error'>$error</div>"; ?>
  <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
  
  <?php if(!$success): ?>
  <form method="POST">
    <label>New Password</label>
    <input type="password" name="password" placeholder="Enter new password" required>
    
    <label>Confirm Password</label>
    <input type="password" name="confirm_password" placeholder="Re-enter password" required>
    
    <button name="update_pass" class="btn"><i class="fas fa-check"></i> Update Password</button>
  </form>
  <?php endif; ?>
  
  <p style="text-align:center;margin-top:20px;">
    <a href="login.php" style="color:#1e90ff;text-decoration:none;">← Back to Login</a>
  </p>
</div>
</body></html>