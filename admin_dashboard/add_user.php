<?php
session_start();
include('conn.php'); 

// 1. Hubi in qofka soo galay uu yahay Admin ama Macallin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

$success = null; 
$error = null;

// AUTO-SCAN DATABASE: Baar khaanadaha rasmiga ah ee miiskaaga users si looga badbaado Unknown Column Error
$col_check = mysqli_query($conn, "SHOW COLUMNS FROM users");
$db_cols = [];
if($col_check) {
    while($c = mysqli_fetch_assoc($col_check)) {
        $db_cols[] = $c['Field'];
    }
}

// 2. Marka la isku dayo in la kaydiyo isticmaale cusub (Register New User)
if (isset($_POST['register_user'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Hubi haddii email-ku uu horey u diwaangashnaa
    $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check_email) > 0) {
        $error = "Cilad: Email-kan horey ayaa loogu isticmaalay nidaamka sxb!";
    } else {
        $insert_columns = ["email", "password", "role"];
        $insert_values  = ["'$email'", "'$password'", "'$role'"];

        if ($role === 'student' && !empty($_POST['class_val'])) {
            $class_val = mysqli_real_escape_string($conn, $_POST['class_val']);
            if (in_array('class', $db_cols)) {
                $insert_columns[] = "class";
                $insert_values[]  = "'$class_val'";
            } elseif (in_array('class_name', $db_cols)) {
                $insert_columns[] = "class_name";
                $insert_values[]  = "'$class_val'";
            }
        }

        if (in_array('name', $db_cols) && !empty($_POST['name'])) {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $insert_columns[] = "name";
            $insert_values[]  = "'$name'";
        }

        $sql_insert = "INSERT INTO users (" . implode(', ', $insert_columns) . ") VALUES (" . implode(', ', $insert_values) . ")";
        
        if (mysqli_query($conn, $sql_insert)) {
            $success = "Akoonka cusub si guul leh ayaa loo diwaangeliyey sxb!";
        } else {
            $error = "Cilad ka dhacday kaydinta: " . mysqli_error($conn);
        }
    }
}

// 3. Soo jiid xogta miisaska (Queries)
$students_query = mysqli_query($conn, "SELECT * FROM users WHERE role='student' ORDER BY id DESC");
$staff_query    = mysqli_query($conn, "SELECT * FROM users WHERE role='teacher' OR role='admin' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Users Control Center | Alnuur School</title>
    <style>
        :root { 
            --primary-blue: #1a237e; 
            --dark-blue: #0d145a; 
            --white: #ffffff; 
            --bg-light: #f8f9fa; 
            --green: #2e7d32;
            --red: #c62828;
            --dark-red: #b71c1c;
        }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        
        /* Sidebar Navigation */
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; z-index: 10; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar a:hover { background: var(--dark-blue); }
        
        /* Main Layout */
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        
        /* Top Navigation Buttons */
        .top-nav { display: flex; gap: 15px; justify-content: center; margin-bottom: 30px; position: relative; z-index: 5; }
        .nav-btn { 
            padding: 14px 24px; border-radius: 6px; font-weight: bold; cursor: pointer; 
            font-size: 15px; border: 2px solid transparent; transition: all 0.3s ease; 
        }
        .btn-blue { background: #2196f3; color: white; }
        .btn-blue:hover { background: #1976d2; }
        .btn-outline-green { background: white; color: var(--green); border: 2px solid var(--green); }
        .btn-outline-green:hover { background: var(--green); color: white; }
        .btn-dark { background: var(--primary-blue); color: white; }
        .btn-dark:hover { background: var(--dark-blue); }
        
        /* Badhanka Cusub ee Close All */
        .btn-red { background: var(--red); color: white; }
        .btn-red:hover { background: var(--dark-red); }
        
        /* Form & Panels */
        .content-panel { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; display: none; }
        .active-panel { display: block; }
        
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 8px; font-weight: bold; font-size: 14px; color: #333; }
        .form-group input, .form-group select { padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        
        /* Tables styling */
        .table-section { background: var(--white); border-radius: 8px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; }
        th, td { padding: 14px; border-bottom: 1px solid #e2e8f0; text-align: left; font-size: 15px; word-wrap: break-word; }
        th { background: #f8f9fa; color: #475569; font-weight: bold; }
        
        .badge-student { background: #e8f5e9; color: var(--green); padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-staff { background: #e0f2fe; color: #0284c7; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
    </style>
    <script>
        // Shaqada badamada u ogolaanaysa inay si professional ah isu badalaan
        function switchPanel(panelId) {
            // Qari dhammaan qaybaha
            document.getElementById('register-panel').style.display = 'none';
            document.getElementById('student-panel').style.display = 'none';
            document.getElementById('staff-panel').style.display = 'none';
            
            // Haddii la soo diray ID panel gaar ah, muuji qeybtaas kaliya
            if(panelId !== '') {
                document.getElementById(panelId).style.display = 'block';
            }
        }
        
        // Muuji ama qari khaanadda Class-ka marka la dooranayo Role-ka
        function toggleClassInput() {
            var role = document.getElementById('user_role').value;
            var classGroup = document.getElementById('class-input-group');
            if(role === 'student') {
                classGroup.style.display = 'flex';
            } else {
                classGroup.style.display = 'none';
            }
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
    <a href="../logout.php" style="margin-top: 50px; border-top: 1px solid rgba(255,255,255,0.2);">Log Out</a>
</div>

<div class="main-content">
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="color: var(--primary-blue); margin-bottom: 5px;">Users Control Center</h2>
        <p style="color: #666; margin-top: 0;">Dooro ficilka aad rabto inaad fuliso sxb.</p>
    </div>
    
    <div class="top-nav">
        <button type="button" class="nav-btn btn-blue" onclick="switchPanel('register-panel')">➕ Register New User</button>
        <button type="button" class="nav-btn btn-outline-green" onclick="switchPanel('student-panel')">📊 View Students List</button>
        <button type="button" class="nav-btn btn-dark" onclick="switchPanel('staff-panel')">💼 View Staff List</button>
        <button type="button" class="nav-btn btn-red" onclick="switchPanel('')">❌ Close View</button>
    </div>
    
    <?php if($error) { echo '<p style="color:red; font-weight:bold; background:#ffebee; padding:12px; border-radius:5px; text-align:center;">'.$error.'</p>'; } ?>
    <?php if($success) { echo '<p style="color:green; font-weight:bold; background:#e8f5e9; padding:12px; border-radius:5px; text-align:center;">'.$success.'</p>'; } ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'deleted') { echo '<p style="color:green; font-weight:bold; background:#e8f5e9; padding:12px; border-radius:5px; text-align:center;">Akoonka sxb si guul leh ayaa nidaamka looga tirtiray!</p>'; } ?>

    <div id="register-panel" class="content-panel active-panel">
        <h3 style="color: #1976d2; margin-top:0; margin-bottom:20px; border-bottom: 2px solid #e0e0e0; padding-bottom: 8px;">Diwaangeli Akoon Cusub</h3>
        <form action="add_user.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name (Optional):</label>
                    <input type="text" name="name" placeholder="e.g. Axmed Cali">
                </div>
                <div class="form-group">
                    <label>User Email Address:</label>
                    <input type="email" name="email" placeholder="e.g. user@gmail.com" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" placeholder="Gali Password adag" required>
                </div>
                <div class="form-group">
                    <label>User Role:</label>
                    <select name="role" id="user_role" onchange="toggleClassInput()" required>
                        <option value="student">Student (Arday)</option>
                        <option value="teacher">Teacher (Macallin)</option>
                        <option value="admin">Admin (Maamule)</option>
                    </select>
                </div>
                <div class="form-group" id="class-input-group">
                    <label>Class / Semester:</label>
                    <input type="text" name="class_val" placeholder="e.g. Semester 2">
                </div>
            </div>
            <button type="submit" name="register_user" style="background: #2196f3; color: white; border: none; padding: 14px 30px; margin-top: 20px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px;">💾 Save User Account</button>
        </form>
    </div>

    <div id="student-panel" class="content-panel">
        <div class="table-section">
            <h3 style="color: var(--green); margin-top:0; margin-bottom: 15px;">Students Database Accounts</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">ID No</th>
                        <th style="width: 35%;">User Email</th>
                        <th style="width: 15%;">Role</th>
                        <th style="width: 15%;">Class</th>
                        <th style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if($students_query && mysqli_num_rows($students_query) > 0) {
                        while($row = mysqli_fetch_assoc($students_query)) { ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><span class="badge-student"><?php echo htmlspecialchars($row['role']); ?></span></td>
                            <td><strong style="color: #3f51b5;"><?php echo htmlspecialchars($row['class'] ?? $row['class_name'] ?? '-'); ?></strong></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $row['id']; ?>" style="color: #2196f3; font-weight: bold; text-decoration: none; margin-right: 15px;">✏️ Edit</a>
                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" style="color: #f44336; font-weight: bold; text-decoration: none;" onclick="return confirm('Ma hubaalbaa inaad tirtirto akoonkan ardayga sxb?');">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php } } else { echo "<tr><td colspan='5' style='text-align:center; padding: 20px;'>Weli wax arday ah lagama helin.</td></tr>"; } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="staff-panel" class="content-panel">
        <div class="table-section">
            <h3 style="color: var(--primary-blue); margin-top:0; margin-bottom: 15px;">Staff & Admin Accounts</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">ID No</th>
                        <th style="width: 45%;">User Email</th>
                        <th style="width: 20%;">Role</th>
                        <th style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if($staff_query && mysqli_num_rows($staff_query) > 0) {
                        while($row = mysqli_fetch_assoc($staff_query)) { ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><span class="badge-staff"><?php echo htmlspecialchars($row['role']); ?></span></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $row['id']; ?>" style="color: #2196f3; font-weight: bold; text-decoration: none; margin-right: 15px;">✏️ Edit</a>
                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" style="color: #f44336; font-weight: bold; text-decoration: none;" onclick="return confirm('Ma hubaalbaa inaad tirtirto akoonkan sxb?');">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php } } else { echo "<tr><td colspan='4' style='text-align:center; padding: 20px;'>Weli wax maamul/macalimiin ah lagama helin.</td></tr>"; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>