<?php
include('admin_dashboard/conn.php'); 
session_start();

if (isset($_POST['login'])) {
    $user_input = mysqli_real_escape_string($conn, $_POST['user_input']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = $_POST['role'];

    // Haddii uu yahay Administrator wuxuu ku galayaa Email, haddii uu yahay Student-na ID Code
    if ($role == 'admin') {
        $query = "SELECT * FROM users WHERE email='$user_input' AND password='$password' AND role='admin'";
    } else {
        $query = "SELECT * FROM users WHERE student_id_code='$user_input' AND password='$password' AND role='student'";
    }

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
        $error = "Xogta aad gelisay waa khaldan tahay!";
    }
}
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Alnuur School | Login</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 400px; text-align: center; }
        h2 { color: #1a237e; margin-bottom: 25px; }
        select, input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; font-size: 15px; }
        label { display: block; text-align: left; font-weight: bold; color: #555; margin-top: 10px; }
        button { width: 100%; padding: 12px; background: #1a237e; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-top: 20px; font-size: 16px; }
        button:hover { background: #0d145a; }
        .error { color: red; background: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Alnuur School</h2>
    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
    
    <form action="" method="POST">
        <label>Login As:</label>
        <select name="role" required>
            <option value="student">Student</option>
            <option value="admin">Administrator</option>
        </select>

        <label>User ID / Email:</label>
        <input type="text" name="user_input" placeholder="Geli ID-ga ama Email-ka" required>

        <label>Password:</label>
        <input type="password" name="password" placeholder="••••••••" required>

        <button type="submit" name="login">Gudaha Gal</button>
    </form>
</div>

</body>
</html>