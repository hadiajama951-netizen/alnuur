<?php
include('conn.php'); 
session_start();

// Hubi in qofka soo galay uu yahay User
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// 1. Soo saar dhibcaha ardayga
$query = "SELECT * FROM marks WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);

// 2. Xisaabi wadarta dhibcaha
$total_full = 0;
$total_obtained = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Performance - SPMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Halkan ku hay style-kaagii hore ee aad soo dirtay */
        :root { --primary-blue: #3f51b5; --dark-sidebar: #2c3e50; --light-bg: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: var(--light-bg); display: flex; }
        .sidebar { width: 240px; background: var(--dark-sidebar); height: 100vh; color: white; position: fixed; }
        .sidebar-header { padding: 20px; background: var(--primary-blue); text-align: center; font-weight: bold; }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu li { padding: 15px 20px; cursor: pointer; display: flex; align-items: center; gap: 10px; }
        .sidebar-menu li.active { background: var(--primary-blue); }
        .sidebar-menu li a { color: white; text-decoration: none; }
        .main-content { margin-left: 240px; width: calc(100% - 240px); }
        .top-header { background: var(--primary-blue); padding: 10px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .content-body { padding: 30px; }
        .performance-card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .student-details-bar { background: #e8f0fe; padding: 15px; border-radius: 5px; display: grid; grid-template-columns: 1fr 1fr; margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        .total-row { background: #f9f9f9; font-weight: bold; color: #2e7d32; }
        .grade-a { color: #2e7d32; font-weight: bold; }
        .grade-fail { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">ALnour Performance</div>
        <ul class="sidebar-menu">
            <li><i class="fas fa-home"></i> <a href="dashboard.php">Dashboard</a></li>
            <li class="active"><i class="fas fa-chart-line"></i> <a href="#">My Performance</a></li>
            <li><i class="fas fa-sign-out-alt"></i> <a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-header">
            <i class="fas fa-bars"></i>
            <div style="display:flex; align-items:center; gap:10px;">
                <span><?php echo $full_name; ?></span>
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" width="30">
            </div>
        </div>

        <div class="content-body">
            <div class="performance-card">
                <h2>Natiijada Imtixaanka</h2>

                <div class="student-details-bar">
                    <div>
                        <div><b>Magaca:</b> <?php echo $full_name; ?></div>
                        <div><b>ID-ga:</b> STU-<?php echo $user_id; ?></div>
                    </div>
                    <div>
                        <div><b>Taariikhda:</b> <?php echo date("d-M-Y"); ?></div>
                        <div><b>Heerka:</b> Active Student</div>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Maadada (Subject)</th>
                            <th>Dhibcaha Buuxa</th>
                            <th>Dhibcaha la helay</th>
                            <th>Darajada (Grade)</th>
                            <th>Faallo (Remark)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $total_full += $row['full_marks'];
                                $total_obtained += $row['marks_obtained'];
                                $grade_class = ($row['grade'] == 'A') ? 'grade-a' : (($row['grade'] == 'F') ? 'grade-fail' : '');
                                
                                echo "<tr>
                                    <td>{$row['subject_name']}</td>
                                    <td>{$row['full_marks']}</td>
                                    <td>{$row['marks_obtained']}</td>
                                    <td class='{$grade_class}'>{$row['grade']}</td>
                                    <td>{$row['remark']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center;'>Wax natiijo ah lama helin.</td></tr>";
                        }
                        ?>
                        <tr class="total-row">
                            <td>Wadarta Guud (Total)</td>
                            <td><?php echo $total_full; ?></td>
                            <td><?php echo $total_obtained; ?></td>
                            <td colspan="2">Perc: <?php echo ($total_full > 0) ? round(($total_obtained/$total_full)*100, 1) : 0; ?>%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>