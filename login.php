<?php
// 1. Ku xir database-ka
include('admin_dashboard/conn.php'); 
session_start();

// 2. Hubi haddii badhanka Login la taabto
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = $_POST['role'];

    // Query hubinaya Email, Password iyo Role
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password' AND role='$role'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];

        if ($role == 'admin') {
            header("Location: admin_dashboard/admin_dashboard.php");
        } else {
            header("Location: user/student_portal.php");
        }
        exit();
    } else {
        $error = "Email, Password ama Role-ka aad dooratay waa khaldan!";
    }
}
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alnuur School | Login</title>
    <style>
        :root { --primary-color: #1a237e; --error-bg: #ffebee; --error-text: #c62828; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .login-card h2 { color: var(--primary-color); margin-bottom: 25px; font-size: 28px; }
        .error-msg { background: var(--error-bg); color: var(--error-text); padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; border: 1px solid #ffcdd2; }
        .form-group { text-align: left; margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 7px; font-weight: 600; color: #555; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 15px; transition: 0.3s; }
        .form-group input:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 5px rgba(26, 35, 126, 0.2); }
        .btn-login { width: 100%; padding: 14px; background-color: var(--primary-color); color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-login:hover { background-color: #0d145a; transform: translateY(-1px); }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Alnuur School</h2>

    <?php if(isset($error)): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Login As:</label>
            <select name="role" required>
                <option value="student">Student</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label>Email Address:</label>
            <input type="email" name="email" placeholder="tusaale@alnuur.com" autocomplete="off" required>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" name="login" class="btn-login">Gudaha Gal</button>
    </form>
</div>

</body>
</html>