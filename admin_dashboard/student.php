<?php
session_start();
include('conn.php'); 

// Hubi in qofka soo galay uu yahay Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 1. Marka foomka 'Add Student' la gujiyo si arday cusub loo kaydiyo
if (isset($_POST['save_student'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']); // ID-ga foomka laga soo geliyo
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $class = mysqli_real_escape_string($conn, $_POST['class']); 
    $role = 'student';

    // Hubi haddii uu ID-gan ama Email-kan horay u jiray
    $check_user = mysqli_query($conn, "SELECT * FROM users WHERE id='$student_id' OR email='$email'");
    if (mysqli_num_rows($check_user) > 0) {
        $error = "ID Number-kan ama Email-kan arday kale ayaa leh sxb!";
    } else {
        // Waxaa si toos ah loo gelinayaa ID-gii aad adigu gacanta ku soo qorto
        $insert_query = "INSERT INTO users (id, email, password, role, class) VALUES ('$student_id', '$email', '$password', '$role', '$class')";
        if (mysqli_query($conn, $insert_query)) {
            $success = "Ardayga si guul leh ayaa loo kaydiyey!";
        } else {
            $error = "Cilad farsamo: " . mysqli_error($conn);
        }
    }
}

// 2. Kala soo bax database-ka dhamaan ardayda si loogu muujiyo miiska
$student_result = mysqli_query($conn, "SELECT id, email, role, class FROM users WHERE role='student' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Manage Students | Alnuur School</title>
    <style>
        :root { --primary-blue: #1a237e; --dark-blue: #0d145a; --white: #ffffff; --bg-light: #f8f9fa; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        
        /* Sidebar Styling */
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar a:hover { background: var(--dark-blue); }
        
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        
        /* 3-Button Control Center (Blue, White, Dark Blue) */
        .control-center { display: flex; justify-content: center; gap: 20px; margin-top: 20px; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #e0e0e0; }
        .action-btn { font-family: 'Segoe UI', sans-serif; font-size: 15px; font-weight: bold; border: none; padding: 16px 30px; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 10px; }
        
        /* Badhanka 1aad: Blue */
        .btn-blue { background: #2196f3; color: white; } 
        .btn-blue:hover { background: #1976d2; transform: translateY(-2px); }
        
        /* Badhanka 2aad: White (Dhexda) */
        .btn-white { background: #ffffff; color: #1a237e; border: 2px solid #1a237e; } 
        .btn-white:hover { background: #f4f5fa; transform: translateY(-2px); }
        
        /* Badhanka 3aad: Dark Blue */
        .btn-dark-blue { background: #1a237e; color: white; } 
        .btn-dark-blue:hover { background: #0d145a; transform: translateY(-2px); }
        
        /* Centered Form Layout (Hidden by Default) */
        .form-container { display: none; justify-content: center; margin-bottom: 40px; animation: fadeIn 0.4s ease; }
        .form-box { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 500px; }
        
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: bold; font-size: 14px; color: #333; }
        .form-group input, .form-group select { width: 100%; padding: 11px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 14px; }
        
        .btn-save { background: var(--primary-blue); color: white; border: none; padding: 13px; width: 100%; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 15px; }
        .btn-save:hover { background: var(--dark-blue); }
        
        /* Table Section (Hidden by Default) */
        .table-section { background: var(--white); padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 15px; display: none; animation: fadeIn 0.4s ease; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 14px; border-bottom: 1px solid #e0e0e0; text-align: left; font-size: 15px; }
        th { background: #f5f5f5; color: #333; font-weight: 600; }
        
        .badge { padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-student { background: #e8f5e9; color: #2e7d32; }
        
        .action-links a { text-decoration: none; font-weight: bold; font-size: 14px; }
        .edit-link { color: #1565c0; margin-right: 10px; }
        .delete-link { color: #c62828; }

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
    <h2 style="color: var(--primary-blue); margin-bottom: 10px; text-align: center;">Students Control Center</h2>
    <p style="text-align:center; color: #666; margin-bottom: 30px;">Maamul ardayda nidaamka Alnuur sxb.</p>
    
    <div class="control-center">
        <button class="action-btn btn-blue" onclick="toggleSection('formSection')">➕ Add New Student</button>
        <button class="action-btn btn-white" onclick="toggleSection('studentSection')">📊 View Student Directory</button>
        <button class="action-btn btn-dark-blue" onclick="closeAllSections()">✖ Close View</button>
    </div>

    <div id="formSection" class="form-container" <?php if(isset($error) || isset($success)) { echo 'style="display:flex;"'; } ?>>
        <div class="form-box">
            <h3 style="margin-top:0; color: var(--primary-blue); text-align: center;">Register New Student</h3>
            <?php if(isset($error)) { echo '<p style="color:red; font-weight:bold; text-align:center;">'.$error.'</p>'; } ?>
            <?php if(isset($success)) { echo '<p style="color:green; font-weight:bold; text-align:center;">'.$success.'</p>'; } ?>
            
            <form action="student.php" method="POST">
                <div class="form-group">
                    <label>Student ID No (Gacanta ku geli):</label>
                    <input type="text" name="student_id" placeholder="E.g., 1222" required>
                </div>
                <div class="form-group">
                    <label>Full Name / Email:</label>
                    <input type="email" name="email" placeholder="E.g., student@alnuur.com" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" placeholder="Enter student password" required>
                </div>
                <div class="form-group">
                    <label>Class / Semester:</label>
                    <select name="class" required>
                        <option value="">-- Select Class --</option>
                        <option value="Semester 1">Semester 1</option>
                        <option value="Semester 2">Semester 2</option>
                        <option value="Semester 3">Semester 3</option>
                        <option value="Semester 4">Semester 4</option>
                    </select>
                </div>
                <button type="submit" name="save_student" class="btn-save">Save Student</button>
            </form>
        </div>
    </div>

    <div id="studentSection" class="table-section">
        <h3 style="margin-top:0; color: var(--primary-blue);">Student Directory List</h3>
        <table>
            <thead>
                <tr>
                    <th>ID No</th>
                    <th>Full Name / Email</th>
                    <th>Class</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($student_result) > 0) { 
                    while($row = mysqli_fetch_assoc($student_result)) { ?>
                    <tr>
                        <td><strong>#<?php echo htmlspecialchars($row['id']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><span class="badge badge-student"><?php echo htmlspecialchars($row['class']); ?></span></td>
                        <td class="action-links">
                            <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="edit-link">Edit</a>
                            <span style="color: #ddd;">|</span>
                            <a href="delete_student.php?id=<?php echo $row['id']; ?>" class="delete-link" onclick="return confirm('Ma hubtaa inaad tirtirto ardaygan?')">Delete</a>
                        </td>
                    </tr>
                <?php } } else { echo "<tr><td colspan='4' style='text-align:center; color:#999;'>Wax arday ah weli lama diwaangelin.</td></tr>"; } ?>
            </tbody>
        </table>
    </div>

</div>

<script>
function toggleSection(sectionId) {
    // Wada qari dhamaan marka hore
    document.getElementById('formSection').style.display = 'none';
    document.getElementById('studentSection').style.display = 'none';
    
    // Muuji qaybta la rabo
    var target = document.getElementById(sectionId);
    if(sectionId === 'formSection') {
        target.style.display = 'flex'; 
    } else {
        target.style.display = 'block'; 
    }
}

// Badhanka sadexaad (Unview / Close View) ee lagu xirayo dhamaan wixii furan
function closeAllSections() {
    document.getElementById('formSection').style.display = 'none';
    document.getElementById('studentSection').style.display = 'none';
}
</script>

</body>
</html>