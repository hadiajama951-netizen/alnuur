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
$student = null;

// 1. Soo qabo xogta ardayga la rabo in la edit-gareeyo
if (isset($_GET['id'])) {
    $student_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Baar miiska users ama students midka aad u isticmaasho xogta ardayda
    $query = mysqli_query($conn, "SELECT * FROM users WHERE id='$student_id' AND role='student'");
    if(mysqli_num_rows($query) == 0){
        // Haddii aad miis gooni ah u isticmaasho ardayda:
        $query = mysqli_query($conn, "SELECT * FROM students WHERE id='$student_id'");
    }
    
    if (mysqli_num_rows($query) > 0) {
        $student = mysqli_fetch_assoc($query);
    } else {
        $error = "Cilad: Ardayga aad raadinayso lagama helin nidaamka sxb!";
    }
} else {
    header("Location: student.php");
    exit();
}

// 2. Marka foomka isbeddelka la soo gudbiyo (Update)
if (isset($_POST['update_student'])) {
    $id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Baar khaanadaha jira si uusan nidaamku u jabin sxb
    $col_check = mysqli_query($conn, "SHOW COLUMNS FROM users");
    $db_cols = [];
    if($col_check){
        while($c = mysqli_fetch_assoc($col_check)) { $db_cols[] = $c['Field']; }
    }

    // Dynamic Update Query
    $update_fields = [];
    if (in_array('email', $db_cols)) { $update_fields[] = "email='$email'"; }
    
    // Haddii foomkaaga ay ku jiraan khaanado kale sida name ama phone ku dar halkan:
    if (isset($_POST['name']) && in_array('name', $db_cols)) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $update_fields[] = "name='$name'";
    }
    if (isset($_POST['username']) && in_array('username', $db_cols)) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $update_fields[] = "username='$username'";
    }

    if (!empty($update_fields)) {
        $sql_update = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id='$id'";
        
        if (mysqli_query($conn, $sql_update)) {
            $success = "Xogta ardayga si guul leh ayaa loo cusboonaysiiyey sxb!";
            // Dib u soo cusboonaysii xogta shaashadda taala
            $query = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
            $student = mysqli_fetch_assoc($query);
        } else {
            $error = "Cilad ka dhacday update-ka: " . mysqli_error($conn);
        }
    } else {
        // Haddii ardayda miis kale lagu kaydiyo (students table)
        $sql_update_student = "UPDATE students SET email='$email' WHERE id='$id'";
        if (mysqli_query($conn, $sql_update_student)) {
            $success = "Xogta ardayga si guul leh ayaa loo cusboonaysiiyey sxb!";
            $query = mysqli_query($conn, "SELECT * FROM students WHERE id='$id'");
            $student = mysqli_fetch_assoc($query);
        } else {
            $error = "Cilad ka dhacday keydinta miiska kale: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Edit Student | Alnuur School</title>
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
        .form-group input { padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        
        .btn-submit { background: #4caf50; color: white; border: none; padding: 15px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; }
        .btn-submit:hover { background: #388e3c; }
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
        <h3 style="color: var(--primary-blue); margin-bottom: 25px; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; text-align: center;">Wax ka beddel Xogta Ardayga (Edit Student)</h3>
        
        <?php if($error) { echo '<p style="color:red; font-weight:bold; text-align:center; padding: 10px; background: #ffebee; border-radius: 4px;">'.$error.'</p>'; } ?>
        <?php if($success) { echo '<p style="color:green; font-weight:bold; text-align:center; padding: 10px; background: #e8f5e9; border-radius: 4px;">'.$success.'</p>'; } ?>

        <?php if($student): ?>
        <form action="edit_student.php?id=<?php echo $student['id']; ?>" method="POST">
            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
            
            <?php if(isset($student['name'])): ?>
            <div class="form-group">
                <label>Magaca Ardayga (Name):</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
            </div>
            <?php elseif(isset($student['username'])): ?>
            <div class="form-group">
                <label>Username-ka Ardayga:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($student['username']); ?>" required>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Email-ka Ardayga (Email Address):</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>" required>
            </div>

            <button type="submit" name="update_student" class="btn-submit">💾 Update Student Info</button>
        </form>
        <?php endif; ?>
        
        <a href="student.php" class="btn-back">⬅️ Ku noqo Maareynta Ardayda (Back)</a>
    </div>
</div>

</body>
</html>