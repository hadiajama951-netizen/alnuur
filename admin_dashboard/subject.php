<?php
session_start();
include('conn.php'); 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Marka la gujiyo badhanka Save Subject
if (isset($_POST['save_subject'])) {
    $subject_code = mysqli_real_escape_string($conn, $_POST['subject_code']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $teacher_name = mysqli_real_escape_string($conn, $_POST['teacher_name']);

    $check_subject = mysqli_query($conn, "SELECT * FROM subjects WHERE subject_code='$subject_code'");
    if (mysqli_num_rows($check_subject) > 0) {
        $error = "Subject Code-kan horay ayaa loo diwaangeliyey sxb!";
    } else {
        // Hadda koodhku wuxuu si toos ah ugu dhacayaa khaanaddii 'class' ee aan database-ka ku darnay!
        $insert_query = "INSERT INTO subjects (subject_code, subject_name, class) VALUES ('$subject_code', '$subject_name', '$teacher_name')";
        
        if (mysqli_query($conn, $insert_query)) {
            $success = "Maaddada si guul leh ayaa loo kaydiyey sxb!";
        } else {
            $error = "Cilad database: " . mysqli_error($conn);
        }
    }
}

$subject_result = mysqli_query($conn, "SELECT * FROM subjects ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Manage Subjects | Alnuur School</title>
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
        .form-box { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 500px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; font-size: 14px; color: #333; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        .btn-save { background: var(--primary-blue); color: white; border: none; padding: 14px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; box-shadow: 0 3px 6px rgba(0,0,0,0.1); }
        .btn-save:hover { background: var(--dark-blue); }
        .table-section { background: var(--white); padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 15px; display: none; animation: fadeIn 0.4s ease; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 14px; border-bottom: 1px solid #e0e0e0; text-align: left; font-size: 15px; }
        th { background: #f5f5f5; color: #333; font-weight: 600; }
        .badge-class { background: #e3f2fd; color: #0d47a1; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: bold; }
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
    <h2 style="color: var(--primary-blue); margin-bottom: 10px; text-align: center;">Subjects Control Center</h2>
    
    <div class="control-center">
        <button class="action-btn btn-blue" onclick="toggleSection('formSection')">➕ Add Subject</button>
        <button class="action-btn btn-white" onclick="toggleSection('subjectSection')">📊 View Subjects</button>
        <button class="action-btn btn-dark-blue" onclick="closeAllSections()">✖ Close View</button>
    </div>

    <div id="formSection" class="form-container" <?php if(isset($error) || isset($success)) { echo 'style="display:flex;"'; } ?>>
        <div class="form-box">
            <h3 style="margin-top:0; color: var(--primary-blue); text-align: center; margin-bottom: 25px;">Register New Subject</h3>
            <?php if(isset($error)) { echo '<p style="color:red; font-weight:bold; text-align:center; margin-bottom:20px;">'.$error.'</p>'; } ?>
            <?php if(isset($success)) { echo '<p style="color:green; font-weight:bold; text-align:center; margin-bottom:20px;">'.$success.'</p>'; } ?>
            
            <form action="subject.php" method="POST">
                <div class="form-group">
                    <label>Subject Code:</label>
                    <input type="text" name="subject_code" placeholder="E.g., MAT101" required>
                </div>
                <div class="form-group">
                    <label>Subject Name:</label>
                    <input type="text" name="subject_name" placeholder="E.g., Mathematics" required>
                </div>
                <div class="form-group">
                    <label>Assign Teacher:</label>
                    <input type="text" name="teacher_name" placeholder="E.g., Mr. Ahmed" required>
                </div>
                <button type="submit" name="save_subject" class="btn-save">Save Subject</button>
            </form>
        </div>
    </div>

    <div id="subjectSection" class="table-section">
        <h3 style="margin-top:0; color: var(--primary-blue);">School Subjects List</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                    <th>Subject Code</th>
                    <th>Assigned Teacher</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($subject_result) > 0) { 
                    while($row = mysqli_fetch_assoc($subject_result)) { ?>
                    <tr>
                        <td><strong>#<?php echo htmlspecialchars($row['id']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                        <td><strong><?php echo htmlspecialchars($row['subject_code']); ?></strong></td>
                        <td><span class="badge-class"><?php echo htmlspecialchars($row['class']); ?></span></td>
                        <td>
                            <a href="edit_subject.php?id=<?php echo $row['id']; ?>" style="color: #1565c0; font-weight: bold; text-decoration: none; margin-right: 10px;">Edit</a>
                            <span style="color: #ddd;">|</span>
                            <a href="delete_subject.php?id=<?php echo $row['id']; ?>" style="color: #c62828; font-weight: bold; text-decoration: none;" onclick="return confirm('Ma hubtaa sxb?')">Delete</a>
                        </td>
                    </tr>
                <?php } } else { echo "<tr><td colspan='5' style='text-align:center; color:#999;'>Wax maaddo ah weli lama diwaangelin.</td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleSection(sectionId) {
    document.getElementById('formSection').style.display = 'none';
    document.getElementById('subjectSection').style.display = 'none';
    var target = document.getElementById(sectionId);
    if(sectionId === 'formSection') { target.style.display = 'flex'; } else { target.style.display = 'block'; }
}
function closeAllSections() {
    document.getElementById('formSection').style.display = 'none';
    document.getElementById('subjectSection').style.display = 'none';
}
</script>
</body>
</html>