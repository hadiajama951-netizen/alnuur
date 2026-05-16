<?php
session_start();
include('conn.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$success = null;
$error = null;

if (isset($_POST['update_marks'])) {
    $attendance = floatval($_POST['attendance']);
    $assignment = floatval($_POST['assignment']);
    $mid_exam   = floatval($_POST['mid_exam']);
    $final_exam = floatval($_POST['final_exam']);

    $update_sql = "UPDATE marks SET attendance='$attendance', assignment='$assignment', mid_exam='$mid_exam', final_exam='$final_exam' WHERE id='$id'";
    if (mysqli_query($conn, $update_sql)) {
        header("Location: marks.php");
        exit();
    } else {
        $error = "Cilad cusboonaysiinta: " . mysqli_error($conn);
    }
}

// Soo jiid xogtii hore ee la beddelayey
$select_sql = "SELECT m.*, u.email AS student_email, s.subject_name 
               FROM marks m 
               LEFT JOIN users u ON m.student_id = u.id 
               LEFT JOIN subjects s ON m.subject_id = s.id 
               WHERE m.id = '$id'";
$result = mysqli_query($conn, $select_sql);
$mark_data = mysqli_fetch_assoc($result);

if (!$mark_data) {
    die("Dhibcahan nidaamka lagama helin sxb.");
}
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Wax ka beddel Dhibcaha</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; padding: 50px; display: flex; justify-content: center; }
        .edit-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 500px; }
        h3 { color: #1a237e; margin-top: 0; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 15px; }
        .form-group label { font-weight: bold; margin-bottom: 5px; font-size: 14px; }
        .form-group input { padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .btn-submit { background: #2196f3; color: white; border: none; padding: 12px; border-radius: 4px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<div class="edit-box">
    <h3>Wax ka beddel Dhibcaha Ardayga</h3>
    <p><strong>Ardayga:</strong> <?php echo htmlspecialchars($mark_data['student_email']); ?></p>
    <p><strong>Maaddada:</strong> <?php echo htmlspecialchars($mark_data['subject_name']); ?></p>
    <hr>
    
    <?php if($error) { echo '<p style="color:red;">'.$error.'</p>'; } ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Attendance:</label>
            <input type="number" step="0.01" name="attendance" value="<?php echo htmlspecialchars($mark_data['attendance']); ?>">
        </div>
        <div class="form-group">
            <label>Assignment:</label>
            <input type="number" step="0.01" name="assignment" value="<?php echo htmlspecialchars($mark_data['assignment']); ?>">
        </div>
        <div class="form-group">
            <label>Mid Exam:</label>
            <input type="number" step="0.01" name="mid_exam" value="<?php echo htmlspecialchars($mark_data['mid_exam']); ?>">
        </div>
        <div class="form-group">
            <label>Final Exam:</label>
            <input type="number" step="0.01" name="final_exam" value="<?php echo htmlspecialchars($mark_data['final_exam']); ?>">
        </div>
        <button type="submit" name="update_marks" class="btn-submit">💾 Update Marks</button>
        <a href="marks.php" style="display:block; text-align:center; margin-top:15px; color:#666; text-decoration:none;">Cancel</a>
    </form>
</div>

</body>
</html>