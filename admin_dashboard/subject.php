<?php
// subject.php
session_start();
include('conn.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

$success = null;
$error = null;

// Handle Adding a Subject
if (isset($_POST['add_subject'])) {
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);

    $sql = "INSERT INTO subjects (subject_name, class) VALUES ('$subject_name', '$class')";
    if (mysqli_query($conn, $sql)) {
        $success = "Subject added successfully!";
    } else {
        $error = "Error adding subject: " . mysqli_error($conn);
    }
}

// Fetch existing subjects
$subjects_res = mysqli_query($conn, "SELECT * FROM subjects ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Subjects - Alnuur School</title>
    <style>
        :root { --primary-blue: #1a237e; --dark-blue: #0d145a; --white: #ffffff; --bg-light: #f8f9fa; --green: #2e7d32; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; }
        .sidebar a:hover { background: var(--dark-blue); }
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        .container-box { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 15px; }
        .form-group label { margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .btn-submit { background: #2196f3; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8f9fa; }
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
    <?php if($success) echo '<p style="color:var(--green);">'.$success.'</p>'; ?>
    
    <div class="container-box">
        <h3>📚 Add New Subject</h3>
        <form action="subject.php" method="POST">
            <div class="form-group">
                <label>Subject Name:</label>
                <input type="text" name="subject_name" required>
            </div>
            <div class="form-group">
                <label>Class / Semester:</label>
                <select name="class" required>
                    <option value="Semester 1">Semester 1</option>
                    <option value="Semester 2">Semester 2</option>
                    <option value="Semester 3">Semester 3</option>
                    <option value="Semester 4">Semester 4</option>
                </select>
            </div>
            <button type="submit" name="add_subject" class="btn-submit">Add Subject</button>
        </form>
    </div>

    <div class="container-box">
        <h3>Existing Subjects</h3>
        <table>
            <thead>
                <tr>
                    <th>Subject ID</th>
                    <th>Subject Name</th>
                    <th>Class</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($subjects_res)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['class']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>