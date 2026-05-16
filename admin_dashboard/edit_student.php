<?php
// edit_student.php - Modernized Edit Script to Match Combined System
session_start();
include('conn.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

$success = null;
$error = null;
$student = null;

// 1. Fetch the existing student data using the ID passed from the URL link
if (isset($_GET['student_id'])) {
    $student_id = mysqli_real_escape_string($conn, $_GET['student_id']);
    $query = mysqli_query($conn, "SELECT * FROM students WHERE student_id = '$student_id'");
    
    if (mysqli_num_rows($query) > 0) {
        $student = mysqli_fetch_assoc($query);
    } else {
        $error = "Student profile not found in system layers.";
    }
}

// 2. Process the Form Submission when the user clicks "Update Profile Data"
if (isset($_POST['update_student'])) {
    $old_student_id = mysqli_real_escape_string($conn, $_POST['old_student_id']);
    $new_student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);

    // Update query handling the name, class, and your new manual section field safely
    $sql = "UPDATE students 
            SET student_id = '$new_student_id', name = '$name', class = '$class', section = '$section' 
            WHERE student_id = '$old_student_id'";

    if (mysqli_query($conn, $sql)) {
        // Redirect back to main students panel immediately upon successful database save
        header("Location: student.php");
        exit();
    } else {
        $error = "Database Update Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student Profile - Alnuur School</title>
    <style>
        :root { 
            --primary-blue: #1a237e; 
            --dark-blue: #0d145a; 
            --white: #ffffff; 
            --bg-light: #f8f9fa; 
            --light-blue: #2196f3;
            --red: #c62828; 
        }
        
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; }
        .sidebar a:hover { background: var(--dark-blue); }
        
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        .container-box { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        
        .form-group { display: flex; flex-direction: column; margin-bottom: 15px; }
        .form-group label { margin-bottom: 5px; font-weight: bold; font-size: 14px; color: #334155; }
        .form-group input, .form-group select { padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; background: var(--white); }
        
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-save { background: var(--primary-blue); color: var(--white); flex: 1; }
        .btn-cancel { background: #e2e8f0; color: #334155; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Alnuur School</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="student.php" style="background: var(--dark-blue);">Manage Students</a>
    <a href="subject.php">Subjects</a>
    <a href="add_user.php">Manage Users</a>
    <a href="marks.php">Add Marks</a>
    <a href="reports.php">Reports</a>
    <a href="../logout.php">Log Out</a>
</div>

<div class="main-content">
    <div style="margin-bottom: 30px;">
        <h2 style="color: var(--primary-blue); margin:0;">Edit Student Profile</h2>
        <p style="color: #64748b; margin: 5px 0 0 0;">Modify records safely here. Saving will return you to the dashboard panel instantly.</p>
    </div>

    <?php if($error) { echo '<p style="color:var(--red); background:#ffebee; padding:12px; border-radius:6px; font-weight:bold;">'.$error.'</p>'; } ?>

    <?php if($student): ?>
    <div class="container-box" style="max-width: 500px;">
        <form action="edit_student.php" method="POST">
            <input type="hidden" name="old_student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">

            <div class="form-group">
                <label>Student ID Number:</label>
                <input type="text" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Full Legal Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Class / Assigned Form:</label>
                <select name="class" required>
                    <option value="Form 1" <?php if($student['class'] == 'Form 1') echo 'selected'; ?>>Form 1</option>
                    <option value="Form 2" <?php if($student['class'] == 'Form 2') echo 'selected'; ?>>Form 2</option>
                    <option value="Form 3" <?php if($student['class'] == 'Form 3') echo 'selected'; ?>>Form 3</option>
                    <option value="Form 4" <?php if($student['class'] == 'Form 4') echo 'selected'; ?>>Form 4</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Section:</label>
                <input type="text" name="section" value="<?php echo htmlspecialchars(isset($student['section']) ? $student['section'] : ''); ?>" required>
            </div>
            
            <div class="btn-group">
                <button type="submit" name="update_student" class="btn btn-save">Update Profile Data</button>
                <a href="student.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

</body>
</html>s