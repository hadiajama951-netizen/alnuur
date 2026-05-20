<?php 
include('conn.php'); 

// 1. Soo saarista Tirada (Stats)
$total_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM users");
$total_count = ($total_res) ? mysqli_fetch_assoc($total_res)['total'] : 0;

$admin_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM users WHERE role='Admin'");
$admin_count = ($admin_res) ? mysqli_fetch_assoc($admin_res)['total'] : 0;

$student_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM users WHERE role='User'");
$student_count = ($student_res) ? mysqli_fetch_assoc($student_res)['total'] : 0;

// 2. Soo saarista xogta
$admins_result = mysqli_query($conn, "SELECT * FROM users WHERE role='Admin' ORDER BY id DESC");
$users_result = mysqli_query($conn, "SELECT * FROM users WHERE role='User' ORDER BY id DESC");

if (!$admins_result || !$users_result) {
    die("SQL Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMS - User Management</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .user-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px; border-left: 5px solid #1a237e; }
        .stat-icon { font-size: 25px; background: #f0f2ff; padding: 10px; border-radius: 10px; color: #1a237e; }

        .user-table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .user-table th { background: #1a237e; color: white; padding: 15px; text-align: left; }
        .user-table td { padding: 15px; border-bottom: 1px solid #eee; }

        .role-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .role-admin { background: #e3f2fd; color: #0d47a1; }
        .role-student { background: #f3e5f5; color: #7b1fa2; }
        
        .add-btn { background: #1a237e; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; float: right; }
        .section-title { color: #1a237e; font-size: 18px; margin: 20px 0; display: flex; align-items: center; gap: 10px; clear: both; }
        .no-data { text-align: center; color: #888; padding: 20px; font-style: italic; }
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="brand">SPMS</div>
            <nav class="side-nav">
                <a class="nav-item" href="admin_dashboard.php">🏠 Dashboard</a>
                <a class="nav-item" href="student.php">👤 Students</a>
                <a class="nav-item" href="subject.php">📚 Subjects</a>
                <a class="nav-item" href="marks.php">📝 Marks</a>
                <a class="nav-item" href="#">📊 Reports</a>
                <a class="nav-item active" href="user.php">⚙️ Users</a>
                <a class="nav-item" href="#" style="margin-top: 50px;">🚪 Logout</a>
            </nav>
        </aside>

        <main class="main">
            <header class="topbar">
                <div class="burger">☰</div>
                <div class="user-profile" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/35" style="border-radius: 50%;" alt="Admin">
                    <span>Admin ▼</span>
                </div>
            </header>

            <div class="content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">User Management</h2>
                    <a href="add_user.php" class="add-btn"><i class="fas fa-plus"></i> Add New User</a>
                </div>  

                <div class="user-stats">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div><h3><?php echo $total_count; ?></h3><p>Total Users</p></div>
                    </div>
                    <div class="stat-card" style="border-left-color: #4caf50;">
                        <div class="stat-icon" style="color: #4caf50;"><i class="fas fa-user-tie"></i></div>
                        <div><h3><?php echo $admin_count; ?></h3><p>Admins</p></div>
                    </div>
                    <div class="stat-card" style="border-left-color: #9c27b0;">
                        <div class="stat-icon" style="color: #9c27b0;"><i class="fas fa-user-graduate"></i></div>
                        <div><h3><?php echo $student_count; ?></h3><p>Students</p></div>
                    </div>
                </div>

                <div class="section-title"><i class="fas fa-shield-alt"></i> System Administrators</div>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($admins_result) > 0) {
                            while($row = mysqli_fetch_assoc($admins_result)) { 
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><span class="role-badge role-admin">Admin</span></td>
                            <td><span style="color: #4caf50;">● <?php echo htmlspecialchars($row['status']); ?></span></td>
                            <td>
                                <a href="#" style="color: #2196f3;"><i class="fas fa-edit"></i></a> | 
                                <a href="#" style="color: #f44336;"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else { 
                            echo "<tr><td colspan='5' class='no-data'>Weli wax Admin ah lama diwaangalin.</td></tr>";
                        } 
                        ?>
                    </tbody>
                </table>

                <div class="section-title"><i class="fas fa-users"></i> System Users (Students)</div>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($users_result) > 0) {
                            while($row = mysqli_fetch_assoc($users_result)) { 
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><span class="role-badge role-student">User</span></td>
                            <td><span style="color: #4caf50;">● <?php echo htmlspecialchars($row['status']); ?></span></td>
                            <td>
                                <a href="#" style="color: #2196f3;"><i class="fas fa-edit"></i></a> | 
                                <a href="#" style="color: #f44336;"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else { 
                            echo "<tr><td colspan='5' class='no-data'>Weli wax Arday ah lama diwaangalin.</td></tr>";
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>