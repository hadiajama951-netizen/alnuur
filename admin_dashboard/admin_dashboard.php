<?php
// admin_dashboard.php
session_start();
include('conn.php');

// Protect the page - Only allow logged-in admin or teacher
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

// Quick counters for dashboard metrics
$student_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM students"))['total'];
$subject_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM subjects"))['total'];
$user_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Alnuur School</title>
    <style>
        :root { --primary-blue: #1a237e; --dark-blue: #0d145a; --white: #ffffff; --bg-light: #f8f9fa; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        
        /* Fixed Sidebar Navigation Links */
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; font-size: 15px; font-weight: 500; }
        .sidebar a:hover { background: var(--dark-blue); }
        
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        .welcome-box { background: var(--white); padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        
        /* Stat Cards Configuration */
        .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
        .card { background: var(--white); padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; flex-direction: column; }
        .card h3 { margin: 0; color: #64748b; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
        .card .value { font-size: 32px; font-weight: bold; color: var(--primary-blue); margin: 10px 0 5px 0; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Alnuur School</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="student.php">Manage Students</a>
    <a href="subject.php">Subjects</a>
    <a href="add_user.php">Manage Users</a>
    <a href="marks.php">Add Marks</a>
    <a href="reports.php">Reports</a>
    <a href="../logout.php">Log Out</a>
</div>

<div class="main-content">
    <div class="welcome-box">
        <h1 style="margin: 0; color: var(--primary-blue);">Welcome to ALnour System Dashboard</h1>
        <p style="margin: 5px 0 0 0; color: #64748b;">Logged in as: <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong></p>
    </div>

    <div class="grid-stats">
        <div class="card">
            <h3>Total Registered Students</h3>
            <div class="value"><?php echo $student_count; ?></div>
        </div>
        <div class="card">
            <h3>Active Subjects</h3>
            <div class="value"><?php echo $subject_count; ?></div>
        </div>
        <div class="card">
            <h3>System Users</h3>
            <div class="value"><?php echo $user_count; ?></div>
        </div>
    </div>
</div>

</body>
</html>