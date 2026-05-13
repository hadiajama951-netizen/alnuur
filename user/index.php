<?php
session_start();
include('../conn.php'); // Path to your connection file

if(isset($_POST['login_btn'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    
    $query = "SELECT * FROM students WHERE student_id = '$student_id'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $_SESSION['student_id'] = $student_id;
        header("Location: student_portal.php");
        exit();
    } else {
        $error = "Student ID not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Login</title>
    <link rel="stylesheet" href="../style.css"> <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; font-family: sans-serif; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #1a237e; color: white; border: none; cursor: pointer; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>ALnour Student Portal</h2>
        <form method="POST">
            <input type="text" name="student_id" placeholder="Enter Student ID" required>
            <button type="submit" name="login_btn">Login</button>
        </form>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </div>
</body>
</html>