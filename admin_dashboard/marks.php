<?php
session_start();
include('conn.php'); 

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

$success = null; 
$error = null;

// MARKA LA RIIXO SAVE MARKS
if (isset($_POST['save_marks'])) {
    $student_email = mysqli_real_escape_string($conn, $_POST['student_email']);
    $subject_input = mysqli_real_escape_string($conn, $_POST['subject']);
    
    $attendance = isset($_POST['attendance']) ? floatval($_POST['attendance']) : 0;
    $assignment = isset($_POST['assignment']) ? floatval($_POST['assignment']) : 0;
    $mid_exam   = isset($_POST['mid']) ? floatval($_POST['mid']) : 0;
    $final_exam = isset($_POST['final']) ? floatval($_POST['final']) : 0;

    $student_id = 0;

    // STEP 1: Si looga baaqsado "Unknown column 'email'", waxaynu marka hore hubinaynaa khaanadaha miiska 'users'
    $check_users_cols = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'email'");
    if (mysqli_num_rows($check_users_cols) > 0) {
        $user_query = mysqli_query($conn, "SELECT id FROM users WHERE email = '$student_email'");
    } else {
        // Haddii miiska users uu leeyahay student_email ama username
        $check_users_sub = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'student_email'");
        if (mysqli_num_rows($check_users_sub) > 0) {
            $user_query = mysqli_query($conn, "SELECT id FROM users WHERE student_email = '$student_email'");
        } else {
            $user_query = mysqli_query($conn, "SELECT id FROM users WHERE username = '$student_email'");
        }
    }

    // Haddii laga helay miiska users
    if($user_query && mysqli_num_rows($user_query) > 0) {
        $u_row = mysqli_fetch_assoc($user_query);
        $student_id = $u_row['id'];
    } else {
        // Haddii laga waayo users, ka raadi miiska 'students'
        $check_stud_cols = mysqli_query($conn, "SHOW COLUMNS FROM students LIKE 'student_email'");
        if (mysqli_num_rows($check_stud_cols) > 0) {
            $st_query = mysqli_query($conn, "SELECT id FROM students WHERE student_email = '$student_email'");
        } else {
            $st_query = mysqli_query($conn, "SELECT id FROM students WHERE email = '$student_email'");
        }

        if($st_query && mysqli_num_rows($st_query) > 0) {
            $s_row = mysqli_fetch_assoc($st_query);
            $student_id = $s_row['id'];
        }
    }

    // STEP 2: Soo saar Subject ID adigoo isticmaalaya khaanadda saxda ah ee 'subject_name'
    $subject_id = 0;
    $sub_query = mysqli_query($conn, "SELECT id FROM subjects WHERE subject_name = '$subject_input'");
    if($sub_query && mysqli_num_rows($sub_query) > 0) {
        $sub_row = mysqli_fetch_assoc($sub_query);
        $subject_id = $sub_row['id'];
    }

    // STEP 3: Haddii la helay Student iyo Subject, hadda u geli nidaamka si nabad ah
    if ($student_id > 0 && $subject_id > 0) {
        // Maadaama total_marks uu yahay STORED GENERATED database-kaaga dhexdiisa, halkan kuma dhex dareyno INSERT-ka
        $sql_insert = "INSERT INTO marks (student_id, subject_id, attendance, assignment, mid_exam, final_exam) 
                       VALUES ('$student_id', '$subject_id', '$attendance', '$assignment', '$mid_exam', '$final_exam')";
        
        if (mysqli_query($conn, $sql_insert)) {
            $success = "Dhibcihii ardayga si guul leh ayaa loo kaydiyey sxb!";
        } else {
            $error = "Cilad kaydinta ah: " . mysqli_error($conn);
        }
    } else {
        if ($student_id == 0) {
            $error = "Cilad: Email-kan ($student_email) kama jiro miisaska Users ama Students!";
        } else {
            $error = "Cilad: Maaddadan ($subject_input) kama jirto miiska Subjects!";
        }
    }
}

// Soo jiid xogta si loogu muujiyo miiska hoose (View Marks)
$query_text = "SELECT m.id AS mark_id, m.mid_exam, m.final_exam, m.total_marks,
               COALESCE(u.email, 'Unknown Student') AS student_email, 
               COALESCE(s.subject_name, 'Unknown Subject') AS subject_name
               FROM marks m
               LEFT JOIN users u ON m.student_id = u.id
               LEFT JOIN subjects s ON m.subject_id = s.id";
$marks_query = mysqli_query($conn, $query_text);
?>

<!DOCTYPE html>
<html lang="so">
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
        
        .top-nav { display: flex; gap: 15px; justify-content: center; margin-bottom: 30px; }
        .nav-btn { padding: 14px 24px; border-radius: 6px; font-weight: bold; cursor: pointer; border: 2px solid transparent; }
        .btn-blue { background: #2196f3; color: white; }
        .btn-outline-blue { background: white; color: var(--primary-blue); border: 2px solid var(--primary-blue); }
        .btn-red { background: var(--red); color: white; }
        
        .content-panel { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; display: none; }
        .active-panel { display: block; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 8px; font-weight: bold; font-size: 14px; }
        .form-group input { padding: 12px; border: 1px solid #ccc; border-radius: 6px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; }
        th, td { padding: 14px; border-bottom: 1px solid #e2e8f0; text-align: left; font-size: 14px; }
        th { background: #f8f9fa; color: #475569; font-weight: bold; }
        .badge-total { background: #e8f5e9; color: var(--green); padding: 4px 10px; border-radius: 4px; font-weight: bold; }
        .btn-action { text-decoration: none; font-weight: bold; padding: 4px 8px; border-radius: 4px; }
    </style>
    <script>
        function switchPanel(panelId) {
            document.getElementById('add-marks-panel').style.display = 'none';
            document.getElementById('view-marks-panel').style.display = 'none';
            if(panelId !== '') { document.getElementById(panelId).style.display = 'block'; }
        }
    </script>
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
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="color: var(--primary-blue);">Student Marks Control Panel</h2>
        <p style="color: #666;">Gali ama ka tiri natiijooyinka imtikaanka ardayda sxb.</p>
    </div>
    
    <div class="top-nav">
        <button class="nav-btn btn-blue" onclick="switchPanel('add-marks-panel')">➕ Add Marks</button>
        <button class="nav-btn btn-outline-blue" onclick="switchPanel('view-marks-panel')">📊 View Marks</button>
        <button class="nav-btn btn-red" onclick="switchPanel('')">❌ Close View</button>
    </div>
    
    <?php if($error) { echo '<p style="color:var(--red); background:#ffebee; padding:12px; border-radius:5px; text-align:center; font-weight:bold;">'.$error.'</p>'; } ?>
    <?php if($success) { echo '<p style="color:var(--green); background:#e8f5e9; padding:12px; border-radius:5px; text-align:center; font-weight:bold;">'.$success.'</p>'; } ?>

    <div id="add-marks-panel" class="content-panel">
        <h3>Gali Dhibco Cusub</h3>
        <form action="marks.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Student Email:</label>
                    <input type="email" name="student_email" placeholder="e.g. raxma@gmail.com" required>
                </div>
                <div class="form-group">
                    <label>Subject Name:</label>
                    <input type="text" name="subject" placeholder="e.g. Physics" required>
                </div>
                <div class="form-group">
                    <label>Assignment:</label>
                    <input type="number" step="0.01" name="assignment" value="0">
                </div>
                <div class="form-group">
                    <label>Attendance:</label>
                    <input type="number" step="0.01" name="attendance" value="0">
                </div>
                <div class="form-group">
                    <label>Mid Exam:</label>
                    <input type="number" step="0.01" name="mid" value="0" required>
                </div>
                <div class="form-group">
                    <label>Final Exam:</label>
                    <input type="number" step="0.01" name="final" value="0" required>
                </div>
            </div>
            <button type="submit" name="save_marks" style="background: #2196f3; color: white; border: none; padding: 12px 24px; margin-top: 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">💾 Save Marks</button>
        </form>
    </div>

    <div id="view-marks-panel" class="content-panel active-panel">
        <h3>Dhibcihii Ugu Dambeeyey (Recent Marks)</h3>
        <table>
            <thead>
                <tr>
                    <th>Student Email</th>
                    <th>Subject</th>
                    <th>Mid</th>
                    <th>Final</th>
                    <th>Total</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($marks_query && mysqli_num_rows($marks_query) > 0) {
                    while($row = mysqli_fetch_assoc($marks_query)) { 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['student_email']); ?></td>
                        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars(floatval($row['mid_exam'])); ?></td>
                        <td><?php echo htmlspecialchars(floatval($row['final_exam'])); ?></td>
                        <td><span class="badge-total"><?php echo htmlspecialchars(floatval($row['total_marks'])); ?></span></td>
                        <td style="text-align: center;">
                            <a href="edit_marks.php?id=<?php echo $row['mark_id']; ?>" class="btn-action" style="color: #2196f3;">✏️ Edit</a> | 
                            <a href="delete_marks.php?id=<?php echo $row['mark_id']; ?>" class="btn-action" style="color: #f44336;" onclick="return confirm('Ma hubaalbaa sxb?');">🗑️ Delete</a>
                        </td>
                    </tr>
                <?php } } else { echo "<tr><td colspan='6' style='text-align:center; padding: 25px;'>Weli wax dhibco ah lagama helin kaydka.</td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>