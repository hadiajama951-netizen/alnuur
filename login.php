<?php
// Macluumaadka Server-ka
$servername = "localhost";
$username = "root";       // Username-ka caadiga ah ee XAMPP
$password = "";           // Password-ka XAMPP badanaa waa maran
$dbname = "alnuur";      // Hubi in magacani sax yahay

// Abuurista xiriirka
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Hubi haddii uu xiriirku jiro
if (!$conn) {
    die("Xiriirka database-ka waa uu guuldareystay: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMS - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #3f51b5; --dark: #1a237e; }
        body { 
            margin: 0; height: 100vh; display: flex; align-items: center; justify-content: center;
            background: #f0f2f5; font-family: 'Segoe UI', sans-serif;
        }
        .login-box {
            background: white; width: 350px; padding: 40px; border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center;
        }
        .login-box h1 { color: var(--main); margin-bottom: 10px; font-size: 28px; }
        .login-box p { color: #666; margin-bottom: 30px; font-size: 14px; }
        .input-group { margin-bottom: 20px; position: relative; text-align: left; }
        .input-group i { position: absolute; left: 12px; top: 38px; color: #999; }
        .input-group label { font-size: 13px; font-weight: 600; color: #444; display: block; margin-bottom: 5px; }
        .input-group input {
            width: 100%; padding: 12px 10px 12px 35px; border: 1px solid #ddd;
            border-radius: 8px; box-sizing: border-box; outline: none;
        }
        .input-group input:focus { border-color: var(--main); }
        .btn-login {
            width: 100%; padding: 13px; background: var(--main); color: white;
            border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px;
        }
        .btn-login:hover { background: var(--dark); }
        .error-msg { background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; }
    </style>
</head>
<body>

<div class="login-box">
    <h1>SPMS</h1>
    <p>Gali macluumaadkaaga si aad u gashid</p>

    <?php if(isset($_GET['error'])): ?>
        <div class="error-msg"><?php echo $_GET['error']; ?></div>
    <?php endif; ?>

    <form action="auth.php" method="POST">
        <div class="input-group">
            <label>Username</label>
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Tusaale: admin_ali" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="********" required>
        </div>

        <button type="submit" name="login" class="btn-login">Gali System-ka</button>
    </form>
</div>

</body>
</html>