<?php
// reports.php
session_start();
include('conn.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

// Fetch all marks grouped by student using correct INNER JOINs
$reports_query = "SELECT m.*, s.name AS student_name, s.class AS student_class, sub.subject_name 
                  FROM marks m 
                  INNER JOIN students s ON m.student_id = s.student_id 
                  INNER JOIN subjects sub ON m.subject_id = sub.id 
                  ORDER BY s.student_id ASC";
$reports_res = mysqli_query($conn, $reports_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Academic Reports - Alnuur School</title>
    <style>
        :root { --primary-blue: #1a237e; --dark-blue: #0d145a; --white: #ffffff; --bg-light: #f8f9fa; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; }
        .sidebar a:hover { background: var(--dark-blue); }
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        .container-box { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8f9fa; }
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
    <div class="container-box">
        <h2>📊 Overall Student Performance Reports</h2>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Subject</th>
                    <th>Total Marks (100)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($reports_res) > 0) {
                    while($row = mysqli_fetch_assoc($reports_res)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['student_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['student_class']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['total_marks']); ?></strong></td>
                        </tr>
                <?php } } else { echo "<tr><td colspan='5' style='text-align:center;'>No academic reports compiled yet.</td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>