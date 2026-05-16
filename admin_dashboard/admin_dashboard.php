<?php
session_start();
include('conn.php'); 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 1. Tirinta Ardayda rasmiga ah
$count_students = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='student'");
$student_data = mysqli_fetch_assoc($count_students);

// 2. Tirinta Dhammaan Maaddooyinka (Subjects)
$count_subjects = mysqli_query($conn, "SELECT COUNT(*) as total FROM subjects");
$subjects_data = mysqli_fetch_assoc($count_subjects);

// 3. Tirinta Dhammaan Dhibcooyinka la geliyey (Marks)
$count_marks = mysqli_query($conn, "SELECT COUNT(*) as total FROM marks");
$marks_data = mysqli_fetch_assoc($count_marks);

// 4. Tirinta Shaqaalaha (Admins & Teachers) oo kaliya
$count_users = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role IN ('admin', 'teacher')");
$users_data = mysqli_fetch_assoc($count_users);
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Admin | Alnuur School</title>
    <style>
        /* Midabada Guud */
        :root {
            --primary-blue: #1a237e;
            --dark-blue: #0d145a;
            --white: #ffffff;
            --bg-light: #f8f9fa;
        }

        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); }
        
        /* Sidebar - Buluug madow iyo qoraal cadaan ah */
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; box-shadow: 2px 0 10px rgba(0,0,0,0.1); }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar a:hover { background: var(--dark-blue); }
        
        /* Main Content */
        .main-content { margin-left: 260px; padding: 0; width: 100%; }
        .header { background: var(--white); padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; }
        .header h1 { color: var(--primary-blue); margin: 0; font-size: 24px; }
        
        /* Cards Layout */
        .content-body { padding: 40px; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 25px; }
        .card { background: var(--white); padding: 30px; border-radius: 4px; border: 1px solid #eee; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .card h3 { color: #666; margin: 0; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .card p { font-size: 38px; font-weight: bold; color: var(--primary-blue); margin: 15px 0 0 0; }
        
        /* Quick Action Section */
        .action-section { margin-top: 35px; background: var(--white); padding: 25px; border-radius: 4px; border: 1px solid #eee; }
        .action-section h3 { color: var(--primary-blue); margin-top: 0; margin-bottom: 20px; }
        .btn-group { display: flex; gap: 15px; flex-wrap: wrap; }
        
        /* Button Style */
        .btn-action { 
            background: var(--primary-blue); 
            color: var(--white); 
            padding: 12px 25px; 
            border: none; 
            border-radius: 4px; 
            text-decoration: none; 
            font-weight: bold;
            display: inline-block;
            transition: 0.3s;
        }
        .btn-action:hover { background: var(--dark-blue); }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Alnuur School</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="student.php">Manage Students</a>
    <a href="subject.php">Subjects</a> <a href="add_user.php">Manage Users</a> <a href="marks.php">Add Marks</a>
    <a href="reports.php">Reports</a>
    <a href="../logout.php" style="margin-top: 50px; border-top: 1px solid rgba(255,255,255,0.2);">Log Out</a>
</div>

<div class="main-content">
    <div class="header">
        <h1>Admin Control Panel</h1>
        <div class="user-info">Welcome, Admin</div>
    </div>

    <div class="content-body">
        <div class="cards">
            <div class="card">
                <h3>Students Registered</h3>
                <p><?php echo $student_data['total']; ?></p>
            </div>
            <div class="card">
                <h3>Total Subjects</h3>
                <p><?php echo $subjects_data['total']; ?></p>
            </div>
            <div class="card">
                <h3>Marks Records</h3>
                <p><?php echo $marks_data['total']; ?></p>
            </div>
            <div class="card">
                <h3>System Users</h3>
                <p><?php echo $users_data['total']; ?></p>
            </div>
        </div>

        <div class="action-section">
            <h3>Quick Actions</h3>
            <div class="btn-group">
                <a href="marks.php" class="btn-action">Enter New Marks</a>
                <a href="student.php" class="btn-action" style="background: #2e7d32;">Manage Students</a>
                <a href="add_user.php" class="btn-action" style="background: #e65100;">Add New Staff</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>