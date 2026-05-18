<?php 
include('conn.php'); 

$msg = ""; 

if (isset($_POST['save_user'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username  = mysqli_real_escape_string($conn, $_POST['username']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $password  = mysqli_real_escape_string($conn, $_POST['password']); 
    $role      = mysqli_real_escape_string($conn, $_POST['role']);

    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "<div class='alert error'>Username-kan hore ayaa loo qaatay!</div>";
    } else {
        $sql = "INSERT INTO users (full_name, username, email, password, role, status) 
                VALUES ('$full_name', '$username', '$email', '$password', '$role', 'Active')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: user.php?success=1");
            exit();
        } else {
            $msg = "<div class='alert error'>Khalad: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMS - Add User</title>
    <link rel="stylesheet" href="style.css"> <!-- Hubi in style.css uu jiro -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Wixii dheeraad ah oo foomka khuseeya */
        .user-form-panel {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
        .form-group input, .form-group select {
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box;
        }
        .btn-save {
            background-color: #1a237e; color: white; border: none; padding: 12px 25px;
            border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; font-size: 16px;
        }
        .btn-save:hover { background-color: #0d1440; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .error { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    </style>
</head>
<body>
    <div class="app">
        <!-- Sidebar -->
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
            <!-- Header -->
            <header class="topbar">
                <div class="burger">☰</div>
                <div class="user-profile" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/35" style="border-radius: 50%;" alt="Admin">
                    <span>Admin ▼</span>
                </div>
            </header>

            <div class="content">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Add New System User</h2>
                    <a href="user.php" style="text-decoration: none; color: #1a237e; font-weight: bold;">← Back to List</a>
                </div>

                <div class="user-form-panel">
                    <?php echo $msg; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" name="full_name" placeholder="E.g. Ahmed Mohamed" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-at"></i> Username</label>
                            <input type="text" name="username" placeholder="E.g. ahmed123" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" name="email" placeholder="ahmed@example.com">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Password</label>
                            <input type="password" name="password" placeholder="********" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user-tag"></i> User Role (Darajada)</label>
                            <select name="role" required>
                                <option value="User">Student (Normal User)</option>
                                <option value="Admin">Teacher (Administrator)</option>
                            </select>
                        </div>

                        <button type="submit" name="save_user" class="btn-save">
                            <i class="fas fa-save"></i> Save User Account
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>