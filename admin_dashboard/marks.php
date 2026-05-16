<?php
// marks.php
session_start();
include('conn.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

$success = null;
$error = null;

// Handle saving marks
if (isset($_POST['save_marks'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $subject_id = mysqli_real_escape_string($conn, $_POST['subject_id']);
    $assignment = mysqli_real_escape_string($conn, $_POST['assignment']);
    $attendance = mysqli_real_escape_string($conn, $_POST['attendance']);
    $mid_exam   = mysqli_real_escape_string($conn, $_POST['mid_exam']);
    $final_exam = mysqli_real_escape_string($conn, $_POST['final_exam']);

    // Check if the student exists in the students table
    $check_student = mysqli_query($conn, "SELECT student_id FROM students WHERE student_id = '$student_id'");
    
    if (mysqli_num_rows($check_student) > 0) {
        // Check if marks already exist for this subject and student
        $check_duplicate = mysqli_query($conn, "SELECT id FROM marks WHERE student_id = '$student_id' AND subject_id = '$subject_id'");
        
        if (mysqli_num_rows($check_duplicate) > 0) {
            $error = "Marks already exist for this student in this subject!";
        } else {
            $sql = "INSERT INTO marks (student_id, subject_id, assignment, attendance, mid_exam, final_exam) 
                    VALUES ('$student_id', '$subject_id', '$assignment', '$attendance', '$mid_exam', '$final_exam')";
            if (mysqli_query($conn, $sql)) {
                $success = "Marks saved successfully!";
            } else {
                $error = "Error saving marks: " . mysqli_error($conn);
            }
        }
    } else {
        $error = "Error: Student ID does not exist! Register the student first.";
    }
}

// Fetch subjects and marks
$subjects_res = mysqli_query($conn, "SELECT id, subject_name, class FROM subjects");

$marks_query = "SELECT m.*, s.name AS student_name, sub.subject_name 
                FROM marks m 
                INNER JOIN students s ON m.student_id = s.student_id 
                INNER JOIN subjects sub ON m.subject_id = sub.id 
                ORDER BY m.id DESC";
$marks_res = mysqli_query($conn, $marks_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Marks Control Panel</title>
    <style>
        :root { --primary-blue: #1a237e; --dark-blue: #0d145a; --white: #ffffff; --bg-light: #f8f9fa; --green: #2e7d32; --red: #c62828; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; }
        .sidebar a:hover { background: var(--dark-blue); }
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        .container-box { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 15px; }
        .form-group label { margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        .form-group input, .form-group select { padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8f9fa; color: #475569; }
        .badge { background: #e8f5e9; color: var(--green); padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        .btn-submit { background: #2196f3; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; margin-top: 15px; }
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
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="color: var(--primary-blue); margin:0;">Student Marks Control Panel</h2>
        <p style="color: #64748b;">Manage and view student examination results below.</p>
    </div>

    <?php if($success) { echo '<p style="color:var(--green); background:#e8f5e9; padding:12px; border-radius:6px; font-weight:bold; text-align:center;">'.$success.'</p>'; } ?>
    <?php if($error) { echo '<p style="color:var(--red); background:#ffebee; padding:12px; border-radius:6px; font-weight:bold; text-align:center;">'.$error.'</p>'; } ?>

    <div class="container-box">
        <h3 style="margin-top:0;">➕ Add New Marks</h3>
        <form action="marks.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Student ID No:</label>
                    <input type="text" name="student_id" placeholder="E.g. 7890" required>
                </div>
                <div class="form-group">
                    <label>Subject:</label>
                    <select name="subject_id" required>
                        <option value="">-- Select Subject --</option>
                        <?php while($sub = mysqli_fetch_assoc($subjects_res)) { ?>
                            <option value="<?php echo $sub['id']; ?>"><?php echo htmlspecialchars($sub['subject_name'] . " (" . $sub['class'] . ")"); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Assign. (10):</label>
                    <input type="number" step="0.01" name="assignment" min="0" max="10" value="0" required>
                </div>
                <div class="form-group">
                    <label>Attend. (10):</label>
                    <input type="number" step="0.01" name="attendance" min="0" max="10" value="0" required>
                </div>
                <div class="form-group">
                    <label>Mid Exam (30):</label>
                    <input type="number" step="0.01" name="mid_exam" min="0" max="30" value="0" required>
                </div>
                <div class="form-group">
                    <label>Final Exam (50):</label>
                    <input type="number" step="0.01" name="final_exam" min="0" max="50" value="0" required>
                </div>
            </div>
            <button type="submit" name="save_marks" class="btn-submit">💾 Save Marks</button>
        </form>
    </div>

    <div class="container-box">
        <h3 style="margin-top:0;">Recent Marks</h3>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Subject</th>
                    <th>Assign.</th>
                    <th>Attend.</th>
                    <th>Mid</th>
                    <th>Final</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($marks_res && mysqli_num_rows($marks_res) > 0) {
                    while($row = mysqli_fetch_assoc($marks_res)) { 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                        <td><strong><?php echo htmlspecialchars($row['student_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['assignment']); ?></td>
                        <td><?php echo htmlspecialchars($row['attendance']); ?></td>
                        <td><?php echo htmlspecialchars($row['mid_exam']); ?></td>
                        <td><?php echo htmlspecialchars($row['final_exam']); ?></td>
                        <td><span class="badge"><?php echo htmlspecialchars($row['total_marks']); ?></span></td>
                    </tr>
                <?php } } else { echo "<tr><td colspan='8' style='text-align:center; padding:20px;'>No marks recorded yet.</td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>