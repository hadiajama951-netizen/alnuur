<?php
session_start();
include('conn.php');

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Baar xogta isticmaalaha
    $sql = "SELECT * FROM users WHERE username = '$username' AND status = 'Active' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Hubi password-ka (Sawirkaaga password-ka ku jira waa mid bayaan ah '12345')
        if ($password == $user['password'] || password_verify($password, $user['password'])) {
            
            // Keydi Session-ka
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];

            // Kala saar Admin iyo User sida database-kaaga ku xusan
            if ($user['role'] == 'Admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            header("Location: login.php?error=Password-ka waa khalad!");
            exit();
        }
    } else {
        header("Location: login.php?error=Username-kan ma jiro ama waa laga xannibay system-ka!");
        exit();
    }
}
?>