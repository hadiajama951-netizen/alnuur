<?php
include('admin_dashboard/conn.php');
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Halkan ayaa laga soo qabanayaa dropdown-ka

    $query = "SELECT * FROM users WHERE email='$email' AND password='$password' AND role='$role'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] == 'admin') {
            header("Location: admin_dashboard/admin_dashboard.php");
        } else {
            header("Location: user/student_portal.php");
        }
    } else {
        $error = "Email, Password ama Role-ka aad dooratay waa khalad!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Alnuur School Management</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); width: 100%; max-width: 350px; text-align: center; }
        .login-card h2 { color: #1a237e; margin-bottom: 25px; }
        .input-group { text-align: left; margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn-login { width: 100%; padding: 12px; background: #1a237e; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-login:hover { background: #0d1440; }
        .error-msg { color: #d32f2f; background: #ffcdd2; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Alnuur School</h2>
    <?php if(isset($error)) echo "<div class='error-msg'>$error</div>"; ?>
    
    <form method="POST">
        <div class="input-group">
            <label>Login As:</label>
            <select name="role" required>
                <option value="student">Student</option>
                <option value="admin">Administrator</option>
            </select>
        </div>

        <div class="input-group">
            <label>Email Address:</label>
            <input type="email" name="email" placeholder="tusaale@alnuur.com" required>
        </div>

        <div class="input-group">
            <label>Password:</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" name="login" class="btn-login">Gudaha Gal</button>
    </form>
</div>

</body>
</html>