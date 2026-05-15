<?php
include('../admin_dashboard/conn.php'); 
session_start();

// 1. Hubi haddii ardaygu Login yahay
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id']; 

// 2. Soo saar xogta ardayga (Email, ID Code, iyo Class)
$student_query = "SELECT email, student_id_code, class FROM users WHERE id = '$user_id'";
$student_result = mysqli_query($conn, $student_query);
$student_data = mysqli_fetch_assoc($student_result);

// 3. Soo saar dhibcaha iyo Credit Hours
$marks_query = "SELECT * FROM marks WHERE student_id = '$user_id'";
$marks_result = mysqli_query($conn, $marks_query);
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alnuur | Student Portal</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 1000px; background: white; margin: auto; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header-section { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #1a237e; padding-bottom: 15px; margin-bottom: 25px; }
        h2 { color: #1a237e; margin: 0; font-size: 24px; }
        .logout-btn { background: #c62828; color: white; padding: 8px 18px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px; transition: 0.3s; }
        .logout-btn:hover { background: #b71c1c; }
        
        .student-details { display: grid; grid-template-columns: repeat(3, 1fr); background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 5px solid #1a237e; }
        .detail-box strong { display: block; color: #555; font-size: 13px; text-transform: uppercase; }
        .detail-box span { font-size: 16px; font-weight: 600; color: #1a237e; }

        table { width: 100%; border-collapse: collapse; background: white; }
        th { background-color: #1a237e; color: white; padding: 15px; text-align: left; font-size: 14px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #eee; font-size: 15px; color: #444; }
        tr:hover { background-color: #fcfcfc; }
        .total-font { font-weight: bold; color: #1a237e; background: #f0f2f5; }
        .status-pass { color: green; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <h2>Natiijada Imtixaanka (Grade Sheet)</h2>
        <a href="logout.php" class="logout-btn">Ka Bax</a>
    </div>
    
    <div class="student-details">
        <div class="detail-box">
            <strong>Magaca Ardayga</strong>
            <span><?php echo $student_data['email']; ?></span>
        </div>
        <div class="detail-box">
            <strong>Class-ka Ardayga</strong>
            <span><?php echo !empty($student_data['class']) ? $student_data['class'] : "Lama Qeexin"; ?></span>
        </div>
        <div class="detail-box">
            <strong>Student ID</strong>
            <span><?php echo $student_data['student_id_code']; ?></span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Credit Hours</th>
                <th>Attendance (10)</th>
                <th>Assignment (10)</th>
                <th>Mid Exam (30)</th>
                <th>Final Exam (50)</th>
                <th>Total (100)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($marks_result) > 0) {
                while($row = mysqli_fetch_assoc($marks_result)) {
                    $total = $row['attendance'] + $row['assignment'] + $row['mid_exam'] + $row['final_exam'];
                    echo "<tr>
                            <td>{$row['credit_hours']}</td>
                            <td>" . number_format($row['attendance'], 2) . "</td>
                            <td>" . number_format($row['assignment'], 2) . "</td>
                            <td>" . number_format($row['mid_exam'], 2) . "</td>
                            <td>" . number_format($row['final_exam'], 2) . "</td>
                            <td class='total-font'>" . number_format($total, 2) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center; padding: 30px; color: #999;'>Wali wax dhibco ah looma diwaangelin ardaygan.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>