<?php
// student.php - Combined Student Panel with Custom Customizations
session_start();
include('conn.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

$success = null;
$error = null;

// Handle Adding a New Student via Form (Including New Section Column)
if (isset($_POST['save_student'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);

    $check_id = mysqli_query($conn, "SELECT student_id FROM students WHERE student_id = '$student_id'");
    if (mysqli_num_rows($check_id) > 0) {
        $error = "Error: This Student ID is already registered!";
    } else {
        $sql = "INSERT INTO students (student_id, name, class, section) VALUES ('$student_id', '$name', '$class', '$section')";
        if (mysqli_query($conn, $sql)) {
            $success = "Student profile saved successfully!";
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    }
}

// Handle Deleting a Student
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM students WHERE student_id = '$delete_id'");
    header("Location: student.php");
    exit();
}

// Fetch list of students
$students_res = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students Portal - Alnuur School</title>
    <style>
        :root { 
            --primary-blue: #1a237e; 
            --dark-blue: #0d145a; 
            --white: #ffffff; 
            --bg-light: #f8f9fa; 
            --light-blue: #2196f3;
            --red: #c62828; 
            --green: #2e7d32;
        }
        
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; }
        .sidebar a:hover { background: var(--dark-blue); }
        
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        .container-box { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; border: 1px solid #e2e8f0; }
        
        .action-bar { display: flex; gap: 15px; margin-bottom: 25px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; transition: 0.2s; }
        
        .btn-add { background: var(--light-blue); color: var(--white); }
        .btn-view { background: var(--primary-blue); color: var(--white); }
        .btn-close { background: var(--dark-blue); color: var(--white); margin-left: auto; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }
        
        .form-group { display: flex; flex-direction: column; margin-bottom: 15px; }
        .form-group label { margin-bottom: 5px; font-weight: bold; font-size: 14px; color: #334155; }
        .form-group input, .form-group select { padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; background: var(--white); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: var(--white); }
        th, td { padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; color: var(--dark-blue); font-weight: bold; }
        
        .panel { display: none; }
        .panel.active { display: block; }
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
        <h2 style="color: var(--primary-blue); margin:0;">Student Administration Center</h2>
        <p style="color: #64748b; margin: 5px 0 0 0;">Register new students or look up operational records instantly.</p>
    </div>

    <?php if($success) { echo '<p style="color:var(--green); background:#e8f5e9; padding:12px; border-radius:6px; font-weight:bold;">'.$success.'</p>'; } ?>
    <?php if($error) { echo '<p style="color:var(--red); background:#ffebee; padding:12px; border-radius:6px; font-weight:bold;">'.$error.'</p>'; } ?>

    <div class="action-bar">
        <button class="btn btn-add" onclick="switchPanel('add-panel')">➕ Register Student</button>
        <button class="btn btn-view" onclick="switchPanel('view-panel')">👁️ View Student List</button>
        <button class="btn btn-close" onclick="closeAllPanels()">❌ Close Panel</button>
    </div>

    <div id="add-panel" class="container-box panel <?php echo (!$success && !$error) ? '' : 'active'; ?>">
        <h3 style="margin-top:0; color: var(--dark-blue);">Register New Student Profile</h3>
        <form action="student.php" method="POST" style="max-width: 500px;">
            <div class="form-group">
                <label>Student ID Number:</label>
                <input type="text" name="student_id" placeholder="E.g., 7890" required>
            </div>
            <div class="form-group">
                <label>Full Legal Name:</label>
                <input type="text" name="name" placeholder="E.g., Hadia Jama Moumin" required>
            </div>
            
            <div class="form-group">
                <label>Class / Assigned Form:</label>
                <select name="class" required>
                    <option value="">-- Choose Option --</option>
                    <option value="Form 1">Form 1</option>
                    <option value="Form 2">Form 2</option>
                    <option value="Form 3">Form 3</option>
                    <option value="Form 4">Form 4</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Section:</label>
                <input type="text" name="section" placeholder="E.g., A, B, C" required>
            </div>
            
            <button type="submit" name="save_student" class="btn btn-add" style="width:100%; justify-content:center; background: var(--primary-blue);">Save Profile Data</button>
        </form>
    </div>

    <div id="view-panel" class="container-box panel <?php echo (!$success && !$error) ? 'active' : ''; ?>">
        <h3 style="margin-top:0; color: var(--dark-blue);">Active Registered Students</h3>
        <table>
            <thead>
                <tr>
                    <th>System ID</th>
                    <th>Student Name</th>
                    <th>Class / Form</th>
                    <th>Section</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($students_res) > 0) {
                    while($row = mysqli_fetch_assoc($students_res)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['class']); ?></td>
                            <td><?php echo htmlspecialchars(isset($row['section']) ? $row['section'] : 'N/A'); ?></td>
                            
                            <td style="position: relative; z-index: 10; pointer-events: auto;">
                                <a href="edit_student.php?student_id=<?php echo urlencode($row['student_id']); ?>" 
                                   style="color: var(--light-blue); text-decoration: none; font-weight: bold; margin-right: 15px; display: inline-block; position: relative; z-index: 20; pointer-events: auto;">
                                   ✏️ Edit
                                </a>
                                
                                <a href="student.php?delete_id=<?php echo urlencode($row['student_id']); ?>" 
                                   style="color: var(--red); text-decoration: none; font-weight: bold; display: inline-block; position: relative; z-index: 20; pointer-events: auto;" 
                                   onclick="return confirm('Are you sure you want to completely erase this student file?');">
                                   🗑️ Delete
                                </a>
                            </td>
                        </tr>
                <?php } } else { echo "<tr><td colspan='5' style='text-align:center; padding:20px;'>No active student accounts found.</td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function switchPanel(panelId) {
    closeAllPanels();
    document.getElementById(panelId).classList.add('active');
}

function closeAllPanels() {
    let panels = document.querySelectorAll('.panel');
    panels.forEach(p => p.classList.remove('active'));
}
</script>

</body>
</html>