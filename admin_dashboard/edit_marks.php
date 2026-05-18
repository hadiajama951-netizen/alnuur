<?php
// edit_marks.php - Modify assessment data scores records
session_start();
include('conn.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

$error = null;
$mark_record = null;

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = mysqli_query($conn, "SELECT * FROM marks WHERE id = '$id'");
    if (mysqli_num_rows($query) > 0) {
        $mark_record = mysqli_fetch_assoc($query);
    } else {
        $error = "Assessment record information not found.";
    }
}

if (isset($_POST['update_marks'])) {
    $id          = mysqli_real_escape_string($conn, $_POST['id']);
    $class       = mysqli_real_escape_string($conn, $_POST['class']);
    $section     = mysqli_real_escape_string($conn, $_POST['section']);
    $assignment  = mysqli_real_escape_string($conn, $_POST['assignment']);
    $attendance  = mysqli_real_escape_string($conn, $_POST['attendance']);
    $mid_exam    = mysqli_real_escape_string($conn, $_POST['mid_exam']);
    $final_exam  = mysqli_real_escape_string($conn, $_POST['final_exam']);

    $sql = "UPDATE marks SET 
            class = '$class', 
            section = '$section', 
            assignment = '$assignment', 
            attendance = '$attendance', 
            mid_exam = '$mid_exam', 
            final_exam = '$final_exam' 
            WHERE id = '$id'";
            
    if (mysqli_query($conn, $sql)) {
        header("Location: marks.php");
        exit();
    } else {
        $error = "Modification Failure Statement: " . mysqli_error($conn);
    }
}

$subjects_list = mysqli_query($conn, "SELECT * FROM subjects ORDER BY subject_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Performance Metric - Alnuur School</title>
    <style>
        :root { --primary-blue: #1a237e; --dark-blue: #0d145a; --white: #ffffff; --bg-light: #f8f9fa; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; }
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        .container-box { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 15px; }
        .form-group label { margin-bottom: 5px; font-weight: bold; font-size: 14px; color: #334155; }
        .form-group input, .form-group select { padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-save { background: var(--primary-blue); color: var(--white); flex: 1; }
        .btn-cancel { background: #e2e8f0; color: #334155; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Alnuur School</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="student.php">Manage Students</a>
    <a href="subject.php">Subjects</a>
    <a href="add_user.php">Manage Users</a>
    <a href="marks.php" style="background: var(--dark-blue);">Add Marks</a>
    <a href="reports.php">Reports</a>
    <a href="../logout.php">Log Out</a>
</div>

<div class="main-content">
    <div style="margin-bottom: 30px;">
        <h2 style="color: var(--primary-blue); margin:0;">Modify Performance Metrics</h2>
        <p style="color: #64748b; margin: 5px 0 0 0;">Update student grades values carefully.</p>
    </div>

    <?php if($mark_record): ?>
    <div class="container-box">
        <form action="edit_marks.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($mark_record['id']); ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Student ID No:</label>
                    <input type="text" value="<?php echo htmlspecialchars($mark_record['student_id']); ?>" disabled style="background:#f1f5f9; color:#64748b;">
                </div>
                
                <div class="form-group">
                    <label>Class:</label>
                    <select name="class" required>
                        <option value="Form 1" <?php if($mark_record['class'] == 'Form 1') echo 'selected'; ?>>Form 1</option>
                        <option value="Form 2" <?php if($mark_record['class'] == 'Form 2') echo 'selected'; ?>>Form 2</option>
                        <option value="Form 3" <?php if($mark_record['class'] == 'Form 3') echo 'selected'; ?>>Form 3</option>
                        <option value="Form 4" <?php if($mark_record['class'] == 'Form 4') echo 'selected'; ?>>Form 4</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Section:</label>
                    <input type="text" name="section" value="<?php echo htmlspecialchars($mark_record['section']); ?>" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Assignment:</label>
                    <input type="number" step="0.01" name="assignment" min="0" max="10" value="<?php echo htmlspecialchars($mark_record['assignment']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Attendance:</label>
                    <input type="number" step="0.01" name="attendance" min="0" max="10" value="<?php echo htmlspecialchars($mark_record['attendance']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Mid Exam:</label>
                    <input type="number" step="0.01" name="mid_exam" min="0" max="30" value="<?php echo htmlspecialchars($mark_record['mid_exam']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Final Exam:</label>
                    <input type="number" step="0.01" name="final_exam" min="0" max="50" value="<?php echo htmlspecialchars($mark_record['final_exam']); ?>" required>
                </div>
            </div>
            
            <div class="btn-group">
                <button type="submit" name="update_marks" class="btn btn-save">Update Marks Ledger Record</button>
                <a href="marks.php" class="btn btn-cancel">Cancel Modification</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
