<?php
include('conn.php'); 
session_start();

// Amniga
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// SQL Query-ga la saxay (Halkan ayay cilladdu ahayd)
$query = "SELECT u.student_id_code, u.email, m.attendance, m.assignment, m.mid_exam, m.final_exam, 
          (m.attendance + m.assignment + m.mid_exam + m.final_exam) as total_score 
          FROM users u 
          JOIN marks m ON u.id = m.student_id 
          WHERE u.role = 'student'";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Reports | Alnuur School</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f8f9fa; margin: 0; display: flex; }
        .sidebar { width: 260px; height: 100vh; background: #1a237e; color: white; position: fixed; }
        .sidebar a { display: block; color: white; padding: 15px; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar a:hover { background: #0d145a; }
        .main-content { margin-left: 260px; padding: 40px; width: 100%; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #1a237e; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2 style="text-align: center;">Alnuur School</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="student.php">Manage Students</a>
    <a href="marks.php">Add Marks</a>
    <a href="reports.php">Reports</a>
    <a href="../logout.php">Log Out</a>
</div>

<div class="main-content">
    <h1>Warbixinta Natiijooyinka Ardayda</h1>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Email/Name</th>
                <th>Att.</th>
                <th>Ass.</th>
                <th>Mid</th>
                <th>Final</th>
                <th>Total (100)</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['student_id_code']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['attendance']; ?></td>
                <td><?php echo $row['assignment']; ?></td>
                <td><?php echo $row['mid_exam']; ?></td>
                <td><?php echo $row['final_exam']; ?></td>
                <td style="font-weight:bold; color:#1a237e;"><?php echo $row['total_score']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>