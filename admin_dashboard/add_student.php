<?php
// add_student.php
session_start();
include('conn.php');

// Restrict access to Admins and Teachers
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student - Alnuur School</title>
    <style>
        :root { --primary-blue: #1a237e; --dark-blue: #0d145a; --white: #ffffff; --bg-light: #f8f9fa; --green: #2e7d32; --red: #c62828; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; }
        .sidebar a:hover { background: var(--dark-blue); }
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        .container-box { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 15px; }
        .form-group label { margin-bottom: 5px; font-weight: bold; font-size: 14px; color: #334155; }
        .form-group input, .form-group select { padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; }
        .btn-submit { background: var(--primary-blue); color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; font-size: 16px; }
        .btn-submit:hover { background: var(--dark-blue); }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Alnuur School</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="student.php" style="background: var(--dark-blue);">Manage Students</a>
    <a href="subject.php">Subjects</a>
    <a href="add_user.php">Manage Users</a>
    <a href="marks.php">Add Marks</a>
    <a href="reports.php">Reports</a>
    <a href="../logout.php">Log Out</a>
</div>

<div class="main-content">
    <div class="container-box">
        <h2 style="text-align: center; color: var(--primary-blue); margin-top: 0;">Register New Student</h2>
        <p style="text-align: center; color: #64748b; margin-top: -10px; margin-bottom: 20px;">Fill out the fields below to add a student to the system.</p>

        <form action="save_student.php" method="POST">
            <div class="form-group">
                <label>Student ID No:</label>
                <input type="text" name="student_id" placeholder="E.g., 7890" required>
            </div>
            
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="name" placeholder="E.g., Hadia Jama Moumin" required>
            </div>
            
            <div class="form-group">
                <label>Class / Semester:</label>
                <select name="class" required>
                    <option value="">-- Select Class --</option>
                    <option value="Semester 1">Semester 1</option>
                    <option value="Semester 2">Semester 2</option>
                    <option value="Semester 3">Semester 3</option>
                    <option value="Semester 4">Semester 4</option>
                </select>
            </div>
            
            <button type="submit" name="submit" class="btn-submit">Save Student</button>
        </form>
    </div>
</div>

</body>
</html>