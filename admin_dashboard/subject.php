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

// Isbeddelka marka foomka la soo gudbiyo (Save Subject)
if (isset($_POST['save_subject'])) {
    $subject_code = mysqli_real_escape_string($conn, $_POST['subject_code']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $teacher_id   = !empty($_POST['teacher_id']) ? mysqli_real_escape_string($conn, $_POST['teacher_id']) : null;

    // Baar khaanadaha rasmiga ah ee u furan miiskaaga subjects si aysan Fatal Error u dhicin
    $col_check = mysqli_query($conn, "SHOW COLUMNS FROM subjects");
    $cols = [];
    while($c = mysqli_fetch_assoc($col_check)) { $cols[] = $c['Field']; }

    // Dhisidda Query-ga iyadoo loo eegayo khaanadaha database-kaaga ka furan sxb
    $fields = ["subject_code", "subject_name"];
    $values = ["'$subject_code'", "'$subject_name'"];

    // Hubi haddii khaanadda 'teacher_id' ama 'teacher' ay jirto
    if ($teacher_id !== null) {
        if (in_array('teacher_id', $cols)) { $fields[] = 'teacher_id'; $values[] = "'$teacher_id'"; }
        elseif (in_array('teacher', $cols)) { $fields[] = 'teacher'; $values[] = "'$teacher_id'"; }
    }

    // Hubi haddii khaanadda 'class' ay jirto (Haddii ay maqan tahay Fatal Error hadda ma dhacayso!)
    if (in_array('class', $cols) && isset($_POST['class'])) {
        $class_val = mysqli_real_escape_string($conn, $_POST['class']);
        $fields[] = 'class'; $values[] = "'$class_val'";
    } elseif (in_array('class_name', $cols) && isset($_POST['class'])) {
        $class_val = mysqli_real_escape_string($conn, $_POST['class']);
        $fields[] = 'class_name'; $values[] = "'$class_val'";
    }

    $sql_query = "INSERT INTO subjects (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
    
    if (mysqli_query($conn, $sql_query)) {
        $success = "Maaddada si guul leh ayaa loo diwaangeliyey sxb!";
    } else {
        $error = "Cilad ka dhacday diwaangelinta: " . mysqli_error($conn);
    }
}

// Soo saar maaddooyinka si loogu muujiyo miiska hoose
$subjects_result = mysqli_query($conn, "SELECT * FROM subjects ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Manage Subjects | Alnuur School</title>
    <style>
        :root { --primary-blue: #1a237e; --dark-blue: #0d145a; --white: #ffffff; --bg-light: #f8f9fa; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        
        /* Sidebar Dashboard Integration */
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar a:hover { background: var(--dark-blue); }
        
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        
        /* 3-Button Control Center */
        .control-center { display: flex; justify-content: center; gap: 20px; margin-top: 20px; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #e0e0e0; }
        .action-btn { font-family: 'Segoe UI', sans-serif; font-size: 15px; font-weight: bold; border: none; padding: 16px 30px; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 10px; }
        
        .btn-blue { background: #2196f3; color: white; } .btn-blue:hover { background: #1976d2; transform: translateY(-2px); }
        .btn-white { background: #ffffff; color: #1a237e; border: 2px solid #1a237e; } .btn-white:hover { background: #f4f5fa; transform: translateY(-2px); }
        .btn-dark-blue { background: #1a237e; color: white; } .btn-dark-blue:hover { background: #0d145a; transform: translateY(-2px); }
        
        /* Form Box Section */
        .form-container { display: none; justify-content: center; margin-bottom: 40px; animation: fadeIn 0.4s ease; }
        .form-box { background: var(--white); padding: 35px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 600px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 20px; }
        .form-group label { margin-bottom: 8px; font-weight: bold; font-size: 14px; color: #333; }
        .form-group input, .form-group select { padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        
        .btn-save { background: var(--primary-blue); color: white; border: none; padding: 15px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; }
        .btn-save:hover { background: var(--dark-blue); }
        
        /* Table Section */
        .table-section { background: var(--white); padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 15px; display: none; animation: fadeIn 0.4s ease; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 14px; border-bottom: 1px solid #e0e0e0; text-align: left; font-size: 15px; }
        th { background: #f5f5f5; color: #333; }
        
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
    <h2 style="color: var(--primary-blue); margin-bottom: 5px; text-align: center;">Subjects Control Panel</h2>
    <p style="text-align:center; color: #666; margin-bottom: 30px;">Maareey, kordhi, ama ka tiri maaddooyinka nidaamka sxb.</p>
    
    <div class="control-center">
        <button class="action-btn btn-blue" onclick="toggleSection('formSection')">➕ Add Subject</button>
        <button class="action-btn btn-white" onclick="toggleSection('tableSection')">📊 View Subjects</button>
        <button class="action-btn btn-dark-blue" onclick="closeAllSections()">✖ Close View</button>
    </div>

    <div id="formSection" class="form-container" <?php if(isset($error) || isset($success)) { echo 'style="display:flex;"'; } ?>>
        <div class="form-box">
            <h3 style="text-align: center; color: var(--primary-blue); margin-bottom: 25px;">Register New Subject</h3>
            <?php if($error) { echo '<p style="color:red; font-weight:bold; text-align:center;">'.$error.'</p>'; } ?>
            <?php if($success) { echo '<p style="color:green; font-weight:bold; text-align:center;">'.$success.'</p>'; } ?>
            
            <form action="subject.php" method="POST">
                <div class="form-group">
                    <label>Subject Code:</label>
                    <input type="text" name="subject_code" placeholder="e.g., MAT101" required>
                </div>
                <div class="form-group">
                    <label>Subject Name:</label>
                    <input type="text" name="subject_name" placeholder="e.g., Mathematics" required>
                </div>
                
                <div class="form-group">
                    <label>Assign Teacher (Optional):</label>
                    <select name="teacher_id">
                        <option value="">-- Select Teacher (Optional) --</option>
                        <?php 
                        $teachers = mysqli_query($conn, "SELECT id, name FROM users WHERE role='teacher' OR role='admin'");
                        if($teachers) {
                            while($t = mysqli_fetch_assoc($teachers)) {
                                echo "<option value='".$t['id']."'>".htmlspecialchars($t['name'])."</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" name="save_subject" class="btn-save">Save Subject</button>
            </form>
        </div>
    </div>

    <div id="tableSection" class="table-section">
        <h3>Liiska Maaddooyinka (Registered Subjects)</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                    <th>Subject Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($subjects_result && mysqli_num_rows($subjects_result) > 0) { 
                    while($row = mysqli_fetch_assoc($subjects_result)) { ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                        <td><strong><?php echo htmlspecialchars($row['subject_code']); ?></strong></td>
                        <td>
                            <a href="edit_subject.php?id=<?php echo $row['id']; ?>" style="color: #2196f3; font-weight: bold; text-decoration: none; margin-right: 15px;">Edit</a>
                            <a href="delete_subject.php?id=<?php echo $row['id']; ?>" style="color: #c62828; font-weight: bold; text-decoration: none;" onclick="return confirm('Ma hubtaa sxb?')">Delete</a>
                        </td>
                    </tr>
                <?php } } else { echo "<tr><td colspan='4' style='text-align:center;'>Weli wax maaddo ah lama gelin.</td></tr>"; } ?>
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