<?php
include('conn.php');
session_start();

if (isset($_POST['register_student'])) {
    // Collect data from form
    $student_id_code = mysqli_real_escape_string($conn, $_POST['student_id_code']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'student';

    // Check if ID or Email already exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' OR student_id_code='$student_id_code'");
    
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Error: This Student ID or Email is already taken!');</script>";
    } else {
        // Save with the ID included
        $sql = "INSERT INTO users (student_id_code, email, password, role) 
                VALUES ('$student_id_code', '$email', '$password', '$role')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Student [$student_id_code] registered successfully!'); window.location.href='marks.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alnuur | Register Student</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; justify-content: center; padding-top: 50px; }
        .reg-box { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { color: #1a237e; margin-top: 0; text-align: center; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #444; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: #1a237e; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn:hover { background: #0d1440; }
        .link { display: block; text-align: center; margin-top: 15px; color: #1a237e; text-decoration: none; }
    </style>
</head>
<body>

<div class="reg-box">
    <h2>Register New Student</h2>
    <form method="POST">
        <label>Student ID (e.g. STD-2026):</label>
        <input type="text" name="student_id_code" placeholder="Enter Unique ID" required>

        <label>Student Email:</label>
        <input type="email" name="email" placeholder="student@alnuur.com" required>
        
        <label>Set Password:</label>
        <input type="password" name="password" placeholder="••••••••" required>
        
        <button type="submit" name="register_student" class="btn">Register Student</button>
    </form>
    
    <a href="marks.php" class="link">← Back to Marks Entry</a>
</div>

</body>
</html>