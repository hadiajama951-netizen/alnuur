<?php
session_start();
include('conn.php'); 

// Hubi in qofka soo galay uu yahay Admin ama Macallin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

$success = null; 
$error = null;

// Isbeddelka marka foomka la soo gudbiyo (Keydi Natiijada)
if (isset($_POST['save_marks'])) {
    $student_email = mysqli_real_escape_string($conn, $_POST['student_email']);
    $subject_name  = mysqli_real_escape_string($conn, $_POST['subject']);
    $attendance    = floatval($_POST['attendance']);
    $assignment    = floatval($_POST['assignment']);
    $mid           = floatval($_POST['mid']);
    $final         = floatval($_POST['final']);
    
    // Total-ka xisaabi sxb
    $total = $attendance + $assignment + $mid + $final;

    // 1. HUBI ARDAYGA (STUDENT FOREIGN KEY CHECK)
    $user_check = mysqli_query($conn, "SELECT id FROM users WHERE email='$student_email'");
    
    // 2. HUBI MAADDADA (SUBJECT FOREIGN KEY CHECK) - Tani waxay soo saartaa ID-ga rasmiga ah ee maaddada sxb
    $sub_check = mysqli_query($conn, "SELECT id FROM subjects WHERE subject_name='$subject_name'");

    if (mysqli_num_rows($user_check) == 0) {
        $error = "Cilad: Email-ka ardayga aad gelisay nidaamka kama jiro sxb! Fadlan hubi email-ka.";
    } elseif (mysqli_num_rows($sub_check) == 0) {
        $error = "Cilad: Maaddada aad dooratay lagama helin database-ka sxb!";
    } else {
        // La soo bax ID-yada rasmiga ah si looga badbaado Foreign Key Failure
        $user_data = mysqli_fetch_assoc($user_check);
        $student_id = $user_data['id'];

        $sub_data = mysqli_fetch_assoc($sub_check);
        $subject_id = $sub_data['id'];

        // Baar khaanadaha rasmiga ah ee miiskaaga marks si loo ogaado qaab-dhismeedka rasmiga ah
        $col_check = mysqli_query($conn, "SHOW COLUMNS FROM marks");
        $db_cols = [];
        while($c = mysqli_fetch_assoc($col_check)) { $db_cols[] = $c['Field']; }

        $fields = [];
        $values = [];

        // Hubi habka uu u kaydiyo Ardayga (ID ama Email)
        if (in_array('student_id', $db_cols)) { $fields[] = 'student_id'; $values[] = "'$student_id'"; }
        if (in_array('student_email', $db_cols)) { $fields[] = 'student_email'; $values[] = "'$student_email'"; }
        elseif (in_array('email', $db_cols)) { $fields[] = 'email'; $values[] = "'$student_email'"; }

        // Hubi habka uu u kaydiyo Maaddada (subject_id ama subject_name) - XALLINTA CILADDA CUSUB!
        if (in_array('subject_id', $db_cols)) { $fields[] = 'subject_id'; $values[] = "'$subject_id'"; }
        if (in_array('subject', $db_cols)) { $fields[] = 'subject'; $values[] = "'$subject_name'"; }
        elseif (in_array('subject_name', $db_cols)) { $fields[] = 'subject_name'; $values[] = "'$subject_name'"; }

        // Dhibcaha kale ee caadiga ah
        if (in_array('attendance', $db_cols)) { $fields[] = 'attendance'; $values[] = "'$attendance'"; }
        if (in_array('assignment', $db_cols)) { $fields[] = 'assignment'; $values[] = "'$assignment'"; }
        
        if (in_array('mid', $db_cols)) { $fields[] = 'mid'; $values[] = "'$mid'"; }
        elseif (in_array('mid_exam', $db_cols)) { $fields[] = 'mid_exam'; $values[] = "'$mid'"; }

        if (in_array('final', $db_cols)) { $fields[] = 'final'; $values[] = "'$final'"; }
        elseif (in_array('final_exam', $db_cols)) { $fields[] = 'final_exam'; $values[] = "'$final'"; }
        elseif (in_array('final_marks', $db_cols)) { $fields[] = 'final_marks'; $values[] = "'$final'"; }

        if (in_array('total', $db_cols)) { $fields[] = 'total'; $values[] = "'$total'"; }

        // Hadda fuli query-ga isagoo ammaan ah gabi ahaanba
        if (!empty($fields)) {
            $sql_query = "INSERT INTO marks (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
            if (mysqli_query($conn, $sql_query)) {
                $success = "Natiijada si guul leh ayaa loo keydiyey sxb!";
            } else {
                $error = "Cilad ka dhacday keydinta: " . mysqli_error($conn);
            }
        } else {
            $error = "Database-kaaga miiska 'marks' qaab-dhismeedkiisa lama aqoonsan karo sxb.";
        }
    }
}

// Soo saar dhibcihii ugu dambeeyey (Waxaan ku darnay JOIN si magacyada ardayda iyo maaddooyinka loogu soo saaro si sax ah miiska hoose)
$marks_query = "SELECT m.*, u.email as user_email, s.subject_name as sub_title 
                FROM marks m 
                LEFT JOIN users u ON m.student_id = u.id 
                LEFT JOIN subjects s ON m.subject_id = s.id 
                ORDER BY m.id DESC LIMIT 10";
$marks_result = mysqli_query($conn, $marks_query);

// Haddii ay cilad ku dhacdo JOIN-ka kore, u isticmaal nidaamka fudud si uusan nidaamku u istaagin
if (!$marks_result) {
    $marks_result = mysqli_query($conn, "SELECT * FROM marks ORDER BY id DESC LIMIT 10");
}
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Student Marks Control Panel | Alnuur School</title>
    <style>
        :root { --primary-blue: #1a237e; --dark-blue: #0d145a; --white: #ffffff; --bg-light: #f8f9fa; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar a:hover { background: var(--dark-blue); }
        
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        
        .control-center { display: flex; justify-content: center; gap: 20px; margin-top: 20px; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #e0e0e0; }
        .action-btn { font-family: 'Segoe UI', sans-serif; font-size: 15px; font-weight: bold; border: none; padding: 16px 30px; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 10px; }
        
        .btn-blue { background: #2196f3; color: white; } .btn-blue:hover { background: #1976d2; transform: translateY(-2px); }
        .btn-white { background: #ffffff; color: #1a237e; border: 2px solid #1a237e; } .btn-white:hover { background: #f4f5fa; transform: translateY(-2px); }
        .btn-dark-blue { background: #1a237e; color: white; } .btn-dark-blue:hover { background: #0d145a; transform: translateY(-2px); }
        
        .form-container { display: none; justify-content: center; margin-bottom: 40px; animation: fadeIn 0.4s ease; }
        .form-box { background: var(--white); padding: 35px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 800px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 8px; font-weight: bold; font-size: 14px; color: #333; }
        .form-group input, .form-group select { padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        
        .btn-submit { background: var(--primary-blue); color: white; border: none; padding: 15px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; margin-top: 10px; }
        .btn-submit:hover { background: var(--dark-blue); }
        
        .table-section { background: var(--white); padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 15px; display: none; animation: fadeIn 0.4s ease; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 14px; border-bottom: 1px solid #e0e0e0; text-align: left; font-size: 15px; }
        th { background: #f5f5f5; color: #333; }
        .badge-total { background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 4px; font-weight: bold; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
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
    <a href="../logout.php" style="margin-top: 50px; border-top: 1px solid rgba(255,255,255,0.2);">Log Out</a>
</div>

<div class="main-content">
    <h2 style="color: var(--primary-blue); margin-bottom: 5px; text-align: center;">Student Marks Control Panel</h2>
    <p style="text-align:center; color: #666; margin-bottom: 30px;">Gali ama ka tiri natiijooyinka imtixaanka ardayda sxb.</p>
    
    <div class="control-center">
        <button class="action-btn btn-blue" onclick="toggleSection('formSection')">➕ Add Marks</button>
        <button class="action-btn btn-white" onclick="toggleSection('tableSection')">📊 View Marks</button>
        <button class="action-btn btn-dark-blue" onclick="closeAllSections()">✖ Close View</button>
    </div>

    <div id="formSection" class="form-container" <?php if(isset($error) || isset($success)) { echo 'style="display:flex;"'; } ?>>
        <div class="form-box">
            <h3 style="color: var(--primary-blue); margin-bottom: 25px; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px;">Gali Natiijada Cusub (Add Student Marks)</h3>
            
            <?php if($error) { echo '<p style="color:red; font-weight:bold; text-align:center; padding: 10px; background: #ffebee; border-radius: 4px;">'.$error.'</p>'; } ?>
            <?php if($success) { echo '<p style="color:green; font-weight:bold; text-align:center; padding: 10px; background: #e8f5e9; border-radius: 4px;">'.$success.'</p>'; } ?>

            <form action="marks.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Student Email Address:</label>
                        <input type="email" name="student_email" placeholder="e.g. raxma@gmail.com" required>
                    </div>
                    <div class="form-group">
                        <label>Maaddada (Subject):</label>
                        <select name="subject" required>
                            <option value="">-- Dooro Maaddada --</option>
                            <?php 
                            $sub_q = mysqli_query($conn, "SELECT subject_name FROM subjects");
                            if($sub_q) {
                                while($s = mysqli_fetch_assoc($sub_q)) {
                                    echo "<option value='".htmlspecialchars($s['subject_name'])."'>".htmlspecialchars($s['subject_name'])."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Attendance (10):</label>
                        <input type="number" step="0.01" name="attendance" min="0" max="10" required>
                    </div>
                    <div class="form-group">
                        <label>Assignment (10):</label>
                        <input type="number" step="0.01" name="assignment" min="0" max="10" required>
                    </div>
                    <div class="form-group">
                        <label>Mid Exam (30):</label>
                        <input type="number" step="0.01" name="mid" min="0" max="30" required>
                    </div>
                    <div class="form-group">
                        <label>Final Exam (50):</label>
                        <input type="number" step="0.01" name="final" min="0" max="50" required>
                    </div>
                </div>
                <button type="submit" name="save_marks" class="btn-submit">Keydi Natiijada</button>
            </form>
        </div>
    </div>

    <div id="tableSection" class="table-section">
        <h3>Dhibcihii Ugu Dambeeyey (Recent Marks)</h3>
        <table>
            <thead>
                <tr>
                    <th>Student Email</th>
                    <th>Subject</th>
                    <th>Mid</th>
                    <th>Final</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($marks_result && mysqli_num_rows($marks_result) > 0) { 
                    while($row = mysqli_fetch_assoc($marks_result)) { ?>
                    <tr>
                        <td>
                            <?php 
                            if(isset($row['user_email'])) { echo htmlspecialchars($row['user_email']); }
                            elseif(isset($row['student_email'])) { echo htmlspecialchars($row['student_email']); }
                            else { echo "Student ID: " . htmlspecialchars($row['student_id'] ?? 'N/A'); }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if(isset($row['sub_title'])) { echo htmlspecialchars($row['sub_title']); }
                            elseif(isset($row['subject'])) { echo htmlspecialchars($row['subject']); }
                            else { echo "Subject ID: " . htmlspecialchars($row['subject_id'] ?? 'N/A'); }
                            ?>
                        </td>
                        <td><?php echo isset($row['mid']) ? htmlspecialchars($row['mid']) : (isset($row['mid_exam']) ? htmlspecialchars($row['mid_exam']) : '0'); ?></td>
                        <td><?php echo isset($row['final']) ? htmlspecialchars($row['final']) : (isset($row['final_exam']) ? htmlspecialchars($row['final_exam']) : '0'); ?></td>
                        <td><span class="badge-total"><?php echo isset($row['total']) ? htmlspecialchars($row['total']) : '0'; ?></span></td>
                    </tr>
                <?php } } else { echo "<tr><td colspan='5' style='text-align:center;'>Weli wax dhibco ah lama keydin.</td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleSection(sectionId) {
    document.getElementById('formSection').style.display = 'none';
    document.getElementById('tableSection').style.display = 'none';
    var target = document.getElementById(sectionId);
    if(sectionId === 'formSection') { target.style.display = 'flex'; } else { target.style.display = 'block'; }
}
function closeAllSections() {
    document.getElementById('formSection').style.display = 'none';
    document.getElementById('tableSection').style.display = 'none';
}
</script>
</body>
</html>