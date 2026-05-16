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
$subject = null;

// 1. Soo qabo xogta maaddada la rabo in la beddelo (Edit)
if (isset($_GET['id'])) {
    $subject_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = mysqli_query($conn, "SELECT * FROM subjects WHERE id='$subject_id'");
    if (mysqli_num_rows($query) > 0) {
        $subject = mysqli_fetch_assoc($query);
    } else {
        $error = "Cilad: Maaddada aad raadinayso lagama helin nidaamka sxb!";
    }
} else {
    header("Location: subject.php");
    exit();
}

// 2. Marka foomka isbeddelka la soo gudbiyo (Update Subject)
if (isset($_POST['update_subject'])) {
    $id           = mysqli_real_escape_string($conn, $_POST['id']);
    $subject_code = mysqli_real_escape_string($conn, $_POST['subject_code']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $teacher_id   = !empty($_POST['teacher_id']) ? mysqli_real_escape_string($conn, $_POST['teacher_id']) : null;

    // AUTO-SCAN: Baar khaanadaha rasmiga ah ee miiskaaga subjects si aysan Fatal Error u dhicin
    $col_check = mysqli_query($conn, "SHOW COLUMNS FROM subjects");
    $cols = [];
    while($c = mysqli_fetch_assoc($col_check)) { $cols[] = $c['Field']; }

    // Dhisidda Query-ga cusboonaysiinta (Update)
    $update_fields = [
        "subject_code='$subject_code'",
        "subject_name='$subject_name'"
    ];

    // Hubi haddii khaanadda teacher_id ama teacher ay jirto
    if (in_array('teacher_id', $cols)) {
        $teacher_val = ($teacher_id !== null) ? "'$teacher_id'" : "NULL";
        $update_fields[] = "teacher_id=$teacher_val";
    } elseif (in_array('teacher', $cols)) {
        $teacher_val = ($teacher_id !== null) ? "'$teacher_id'" : "NULL";
        $update_fields[] = "teacher=$teacher_val";
    }

    // Hubi haddii khaanadda class ama class_name ay jirto
    if (isset($_POST['class'])) {
        $class_val = mysqli_real_escape_string($conn, $_POST['class']);
        if (in_array('class', $cols)) {
            $update_fields[] = "class='$class_val'";
        } elseif (in_array('class_name', $cols)) {
            $update_fields[] = "class_name='$class_val'";
        }
    }

    $sql_update = "UPDATE subjects SET " . implode(', ', $update_fields) . " WHERE id='$id'";
    
    if (mysqli_query($conn, $sql_update)) {
        $success = "Xogta maaddada si guul leh ayaa loo cusboonaysiiyey sxb!";
        // Dib u soo aqri xogta cusub si loogu muujiyo foomka
        $query = mysqli_query($conn, "SELECT * FROM subjects WHERE id='$id'");
        $subject = mysqli_fetch_assoc($query);
    } else {
        $error = "Cilad ka dhacday cusboonaysiinta: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Edit Subject | Alnuur School</title>
    <style>
        :root { --primary-blue: #1a237e; --dark-blue: #0d145a; --white: #ffffff; --bg-light: #f8f9fa; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar a:hover { background: var(--dark-blue); }
        
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; display: flex; flex-direction: column; align-items: center; }
        
        .form-box { background: var(--white); padding: 35px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 600px; margin-top: 20px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 20px; }
        .form-group label { margin-bottom: 8px; font-weight: bold; font-size: 14px; color: #333; }
        .form-group input, .form-group select { padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        
        .btn-submit { background: #2196f3; color: white; border: none; padding: 15px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; }
        .btn-submit:hover { background: #1976d2; }
        .btn-back { display: block; text-align: center; margin-top: 15px; color: var(--primary-blue); text-decoration: none; font-weight: bold; }
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
    <div class="form-box">
        <h3 style="color: var(--primary-blue); margin-bottom: 25px; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; text-align: center;">Wax ka beddel Maaddada (Edit Subject)</h3>
        
        <?php if($error) { echo '<p style="color:red; font-weight:bold; text-align:center; padding: 10px; background: #ffebee; border-radius: 4px;">'.$error.'</p>'; } ?>
        <?php if($success) { echo '<p style="color:green; font-weight:bold; text-align:center; padding: 10px; background: #e8f5e9; border-radius: 4px;">'.$success.'</p>'; } ?>

        <?php if($subject): ?>
        <form action="edit_subject.php?id=<?php echo $subject['id']; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $subject['id']; ?>">
            
            <div class="form-group">
                <label>Subject Code:</label>
                <input type="text" name="subject_code" value="<?php echo htmlspecialchars($subject['subject_code'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Subject Name:</label>
                <input type="text" name="subject_name" value="<?php echo htmlspecialchars($subject['subject_name'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Assign Teacher (Optional):</label>
                <select name="teacher_id">
                    <option value="">-- Dooro Macallinka (Optional) --</option>
                    <?php 
                    $teachers = mysqli_query($conn, "SELECT id, name FROM users WHERE role='teacher' OR role='admin'");
                    $current_teacher = $subject['teacher_id'] ?? ($subject['teacher'] ?? '');
                    if($teachers) {
                        while($t = mysqli_fetch_assoc($teachers)) {
                            $selected = ($t['id'] == $current_teacher) ? "selected" : "";
                            echo "<option value='".$t['id']."' $selected>".htmlspecialchars($t['name'])."</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <button type="submit" name="update_subject" class="btn-submit">💾 Update Subject Info</button>
        </form>
        <?php endif; ?>
        
        <a href="subject.php" class="btn-back">⬅️ Ku noqo Maaddooyinka (Back)</a>
    </div>
</div>

</body>
</html>