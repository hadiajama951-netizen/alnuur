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
$user_data = null;

// AUTO-SCAN DATABASE: Baar khaanadaha rasmiga ah ee miiskaaga users si looga badbaado Unknown Column Error
$col_check = mysqli_query($conn, "SHOW COLUMNS FROM users");
$db_cols = [];
if($col_check) {
    while($c = mysqli_fetch_assoc($col_check)) {
        $db_cols[] = $c['Field'];
    }
}

// 2. Soo jiid xogta isticmaalaha la rabo in la beddelo (Edit) via ID
if (isset($_GET['id'])) {
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);
    $get_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
    
    if (mysqli_num_rows($get_user) > 0) {
        $user_data = mysqli_fetch_assoc($get_user);
    } else {
        die("<p style='color:red; text-align:center; margin-top:50px; font-weight:bold;'>Cilad: Isticmaalaha aad raadinayso lagama helin nidaamka sxb!</p>");
    }
} else {
    header("Location: add_user.php");
    exit();
}

// 3. Marka la gujiyo badanka Update (Update User)
if (isset($_POST['update_user'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role  = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Hubi haddii Email-ka la beddelay uu qof kale leeyahay
    $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND id != '$user_id'");
    if (mysqli_num_rows($check_email) > 0) {
        $error = "Cilad: Email-kan waxaa horey u qaatay isticmaale kale sxb!";
    } else {
        // Dhisidda Query-ga Update-ka
        $update_fields = [
            "email = '$email'",
            "role = '$role'"
        ];

        // Haddii Password cusub la soo galiyey, beddel. Haddii kale iska daa kii hore
        if (!empty($_POST['password'])) {
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $update_fields[] = "password = '$password'";
        }

        // Haddii uu yahay arday oo la soo diray Class/Semester
        if ($role === 'student' && isset($_POST['class_val'])) {
            $class_val = mysqli_real_escape_string($conn, $_POST['class_val']);
            if (in_array('class', $db_cols)) {
                $update_fields[] = "class = '$class_val'";
            } elseif (in_array('class_name', $db_cols)) {
                $update_fields[] = "class_name = '$class_val'";
            }
        }

        // Haddii khaanadda 'name' ay miiska users ku jirto
        if (in_array('name', $db_cols) && isset($_POST['name'])) {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $update_fields[] = "name = '$name'";
        }

        $sql_update = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = '$user_id'";
        
        if (mysqli_query($conn, $sql_update)) {
            $success = "Xogta isticmaalaha si guul leh ayaa loo cusboonaysiiyey sxb!";
            // Dib u soo kici xogta cusub si ay foomka uga muuqato
            $get_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
            $user_data = mysqli_fetch_assoc($get_user);
        } else {
            $error = "Cilad ka dhacday cusboonaysiinta: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Edit User Info | Alnuur School</title>
    <style>
        :root { 
            --primary-blue: #1a237e; 
            --dark-blue: #0d145a; 
            --white: #ffffff; 
            --bg-light: #f8f9fa; 
            --green: #2e7d32;
        }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        
        /* Sidebar Navigation */
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; z-index: 10; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar a:hover { background: var(--dark-blue); }
        
        /* Main Layout */
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        
        .content-panel { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 20px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 8px; font-weight: bold; font-size: 14px; color: #333; }
        .form-group input, .form-group select { padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        
        .btn-update { background: #2e7d32; color: white; border: none; padding: 14px 30px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px; transition: background 0.3s; }
        .btn-update:hover { background: #1b5e20; }
        .btn-back { background: #757575; color: white; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: bold; font-size: 15px; margin-left: 15px; display: inline-block; text-align: center; }
        .btn-back:hover { background: #616161; }
    </style>
    <script>
        function toggleClassInput() {
            var role = document.getElementById('user_role').value;
            var classGroup = document.getElementById('class-input-group');
            if(role === 'student') {
                classGroup.style.display = 'flex';
            } else {
                classGroup.style.display = 'none';
            }
        }
        // Marka uu boggu kiciyo, iska hubi haddii arday yahay in khaanadda Class-ku furan tahay
        window.onload = function() {
            toggleClassInput();
        };
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
    <div style="margin-bottom: 25px;">
        <h2 style="color: var(--primary-blue); margin-bottom: 5px;">Cusboonaysii Xogta Isticmaalaha</h2>
        <p style="color: #666; margin-top: 0;">Waxaad wax ka beddelaysaa akoonka: <strong style="color:#2196f3;"><?php echo htmlspecialchars($user_data['email']); ?></strong></p>
    </div>
    
    <?php if($error) { echo '<p style="color:red; font-weight:bold; background:#ffebee; padding:12px; border-radius:5px; text-align:center;">'.$error.'</p>'; } ?>
    <?php if($success) { echo '<p style="color:green; font-weight:bold; background:#e8f5e9; padding:12px; border-radius:5px; text-align:center;">'.$success.'</p>'; } ?>

    <div class="content-panel">
        <form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">
            <div class="form-grid">
                <?php if (in_array('name', $db_cols)): ?>
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user_data['name'] ?? ''); ?>" placeholder="Magaqa rasmiga ah">
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label>User Email Address:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Password (Iska dhaaf haddaanad beddelayn):</label>
                    <input type="password" name="password" placeholder="Gali Password cusub ama iska daa">
                </div>
                
                <div class="form-group">
                    <label>User Role:</label>
                    <select name="role" id="user_role" onchange="toggleClassInput()" required>
                        <option value="student" <?php echo ($user_data['role'] === 'student') ? 'selected' : ''; ?>>Student (Arday)</option>
                        <option value="teacher" <?php echo ($user_data['role'] === 'teacher') ? 'selected' : ''; ?>>Teacher (Macallin)</option>
                        <option value="admin" <?php echo ($user_data['role'] === 'admin') ? 'selected' : ''; ?>>Admin (Maamule)</option>
                    </select>
                </div>
                
                <div class="form-group" id="class-input-group">
                    <label>Class / Semester:</label>
                    <input type="text" name="class_val" value="<?php echo htmlspecialchars($user_data['class'] ?? $user_data['class_name'] ?? ''); ?>" placeholder="e.g. Semester 2">
                </div>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" name="update_user" class="btn-update">✨ Update User Info</button>
                <a href="add_user.php" class="btn-back">⬅️ Ku laabo Bogga Hore</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>