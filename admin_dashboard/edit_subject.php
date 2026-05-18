<?php
// edit_subject.php - Update form script for curriculum courses
session_start();
include('conn.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

$error = null;
$subject = null;

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = mysqli_query($conn, "SELECT * FROM subjects WHERE id = '$id'");
    if (mysqli_num_rows($query) > 0) {
        $subject = mysqli_fetch_assoc($query);
    } else {
        $error = "Subject entry details not found.";
    }
}

if (isset($_POST['update_subject'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);

    $sql = "UPDATE subjects SET subject_name = '$subject_name', class = '$class' WHERE id = '$id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: subject.php");
        exit();
    } else {
        $error = "Database Modification Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Subject Profile - Alnuur School</title>
    <style>
        :root { --primary-blue: #1a237e; --dark-blue: #0d145a; --white: #ffffff; --bg-light: #f8f9fa; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; }
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        .container-box { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
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
    <a href="subject.php" style="background: var(--dark-blue);">Subjects</a>
    <a href="add_user.php">Manage Users</a>
    <a href="marks.php">Add Marks</a>
    <a href="reports.php">Reports</a>
    <a href="../logout.php">Log Out</a>
</div>

<div class="main-content">
    <div style="margin-bottom: 30px;">
        <h2 style="color: var(--primary-blue); margin:0;">Modify Subject</h2>
        <p style="color: #64748b; margin: 5px 0 0 0;">Adjust specific subject entries details seamlessly here.</p>
    </div>

    <?php if($subject): ?>
    <div class="container-box" style="max-width: 500px;">
        <form action="edit_subject.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($subject['id']); ?>">

            <div class="form-group">
                <label>Subject Name:</label>
                <input type="text" name="subject_name" value="<?php echo htmlspecialchars($subject['subject_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Class:</label>
                <select name="class" required>
                    <option value="Form 1" <?php if($subject['class'] == 'Form 1') echo 'selected'; ?>>Form 1</option>
                    <option value="Form 2" <?php if($subject['class'] == 'Form 2') echo 'selected'; ?>>Form 2</option>
                    <option value="Form 3" <?php if($subject['class'] == 'Form 3') echo 'selected'; ?>>Form 3</option>
                    <option value="Form 4" <?php if($subject['class'] == 'Form 4') echo 'selected'; ?>>Form 4</option>
                </select>
            </div>
            
            <div class="btn-group">
                <button type="submit" name="update_subject" class="btn btn-save">Update Subject Data</button>
                <a href="subject.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

</body>
</html>