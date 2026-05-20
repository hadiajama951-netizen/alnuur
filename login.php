<?php
session_start();
include('user/conn.php');

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username' AND status='Active'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Hubi password-ka la siriyeey
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];

            // Halkan u kala kaxee haddii uu yahay Admin ama Arday (User)
            if ($row['role'] == 'Admin') {
                header("Location: admin_dashboard/admin_dashboard.php");
            } else {
                header("Location: user/user_dashboard.php"); // Bogga ardayga oo kaliya u gaarka ah
            }
            exit();
        } else {
            $error = "Password-ka aad gelisay waa khaldan yahay!";
        }
    } else {
        $error = "Student ID / Username-kan ma jiro nidaamka!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SPMS - Login</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); width: 350px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-login { background: #1a237e; color: white; padding: 12px; border: none; border-radius: 8px; width: 100%; font-weight: bold; cursor: pointer; }
        .error-msg { color: red; background: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align: center; color: #1a237e;">SPMS Portal</h2>
        <?php if(!empty($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="E.g. 105" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="********" required>
            </div>
            <button type="submit" name="login" class="btn-login">Soodag (Login)</button>
        </form>
    </div>
</body>
</html>