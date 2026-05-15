<?php
include('../admin_dashboard/conn.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Soo saar xogta ardayga
$user_query = "SELECT email FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Soo saar dhibcaha dhammaystiran
$query = "SELECT m.*, s.subject_name 
          FROM marks m
          JOIN subjects s ON m.subject_id = s.id 
          WHERE m.student_id = '$user_id'";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ALNUUR SCHOOL | Grade Sheet</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .header { background: white; padding: 15px; border-bottom: 4px solid #1a237e; text-align: center; }
        .header h1 { color: #2e7d32; margin: 0; }
        
        .container { max-width: 1100px; margin: 20px auto; padding: 15px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        
        .info-bar { background-color: #81c784; padding: 15px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; font-weight: bold; margin-bottom: 15px; }
        
        .nav-blue { background: #1a237e; color: white; padding: 10px; font-weight: bold; margin-bottom: 10px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        th { background-color: #f1f1f1; border: 1px solid #ccc; padding: 10px; text-align: left; color: #1a237e; }
        td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        
        .semester-title { background: #b3e5fc; padding: 8px; font-weight: bold; border: 1px solid #ccc; text-align: left; }
    </style>
</head>
<body>

<div class="header">
    <h1>ALNUUR SCHOOL</h1>
    <p style="color:red; font-style:italic;">A Vehicle for Peace & Development</p>
</div>

<div class="container">
    <div class="nav-blue">ALNUUR student login / GRADE SHEET</div>

    <div class="info-bar">
        <div>ID Number: ANS-<?php echo 1000 + $user_id; ?></div>
        <div>Overall GPA: 3.50</div>
        <div>Level of School: High School</div>
        <div>Student Name: <?php echo strtoupper($user_data['email']); ?></div>
        <div>Department: General Science</div>
        <div>Program: Secondary</div>
    </div>

    <div class="semester-title">Semester : 1st</div>
    <table>
        <thead>
            <tr>
                <th style="text-align:left;">Course Title</th>
                <th>Credit Hour</th>
                <th>Attendance</th>
                <th>Assignment</th>
                <th>Mid Exam</th>
                <th>Final Exam</th>
                <th>Total Marks</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): 
                $total = $row['total_marks'];
                if($total >= 90) $grade = 'A';
                elseif($total >= 80) $grade = 'B';
                elseif($total >= 70) $grade = 'C';
                elseif($total >= 60) $grade = 'D';
                else $grade = 'F';
            ?>
            <tr>
                <td style="text-align:left;"><?php echo $row['subject_name']; ?></td>
                <td><?php echo $row['credit_hours']; ?></td>
                <td><?php echo $row['attendance']; ?></td>
                <td><?php echo $row['assignment']; ?></td>
                <td><?php echo $row['mid_exam']; ?></td>
                <td><?php echo $row['final_exam']; ?></td>
                <td><strong><?php echo $total; ?></strong></td>
                <td style="font-weight:bold; color:#1a237e;"><?php echo $grade; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>