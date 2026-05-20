<?php 
session_start(); 
include('conn.php'); 

$msg = ""; 

if (isset($_POST['save_user'])) {
    $full_name  = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $username   = mysqli_real_escape_string($conn, trim($_POST['username'])); // Username-ka Admin-ka
    $email      = mysqli_real_escape_string($conn, trim($_POST['email']));
    $role       = 'Admin'; // Si toos ah waxaa loogu dejiyey Admin
    
    $password        = mysqli_real_escape_string($conn, $_POST['password']); 
    $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Ammaan sare oo sir ah

    // Hubi in Username-kan horay loo qaatay
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "<div class='alert error'>Username kan hore ayaa loo qaatay! Fadlan dooro mid kale.</div>";
    } else {
        
        mysqli_begin_transaction($conn);

        try {
            // Ku dar shaxda Users (Maadaama uu Admin yahay, si toos ah ayuu Dashboard-ka u gelayaa)
            $sql_user = "INSERT INTO users (full_name, username, email, password, role, status) 
                         VALUES ('$full_name', '$username', '$email', '$hashed_password', '$role', 'Active')";
            mysqli_query($conn, $sql_user);

            mysqli_commit($conn);
            
            $success_msg = "Admin-ka cusub si guul leh ayaa loo diwaangeliyey!";
            header("Location: user.php?success=" . urlencode($success_msg));
            exit();

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $msg = "<div class='alert error'>Khalad ayaa dhacay: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMS - Add New Admin</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .user-form-panel { max-width: 600px; margin: 20px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-save { background-color: #1a237e; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; font-size: 16px; }
        .btn-save:hover { background-color: #0d1440; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .error { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
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
                <a class="nav-item" href="add_marks.php">📝 Marks</a>
                <a class="nav-item active" href="user.php">⚙️ Users</a>
                <a class="nav-item" href="logout.php" style="margin-top: 50px;">🚪 Logout</a>
            </nav>
        </aside>

        <main class="main">
            <div class="content">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Add New Admin Account</h2>
                    <a href="user.php" style="text-decoration: none; color: #1a237e; font-weight: bold;">← Back to List</a>
                </div>

                <div class="user-form-panel">
                    <?php echo $msg; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label><i class="fas fa-user-tie"></i> Admin Full Name (Magaca Isku-dhafka ah)</label>
                            <input type="text" name="full_name" placeholder="E.g. Ali Mohamed" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Username (Magaca uu ku gali lahaa nidaamka)</label>
                            <input type="text" name="username" placeholder="E.g. alimohamed" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" name="email" placeholder="ali@example.com">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Password</label>
                            <input type="password" name="password" placeholder="Geli password adag" required>
                        </div>

                        <button type="submit" name="save_user" class="btn-save">
                            <i class="fas fa-user-plus"></i> Register Admin Account
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>