<?php
session_start();
include('conn.php'); 

// Hubi in qofka soo galay uu yahay Admin ama Macallin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

// XALLINTA CILADDA: Waxaan isticmaalaynaa LEFT JOIN si magacyada rasmiga ah loo soo saaro halkii ay N/A u soo bixi lahayd
$report_query = "SELECT m.*, u.email as student_email, s.subject_name as subject_title 
                 FROM marks m 
                 LEFT JOIN users u ON m.student_id = u.id 
                 LEFT JOIN subjects s ON m.subject_id = s.id 
                 ORDER BY m.id DESC";

$report_result = mysqli_query($conn, $report_query);

// Haddii qaab-dhismeedka database-ka uu ka duwan yahay, nidaamkan gurmadka ah ha fuliyo
if (!$report_result) {
    $report_result = mysqli_query($conn, "SELECT * FROM marks ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Reports Control Panel | Alnuur School</title>
    <style>
        :root { --primary-blue: #1a237e; --dark-blue: #0d145a; --white: #ffffff; --bg-light: #f8f9fa; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--primary-blue); color: var(--white); position: fixed; }
        .sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin: 0; }
        .sidebar a { display: block; color: var(--white); padding: 15px 25px; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar a:hover { background: var(--dark-blue); }
        
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        
        .control-center { display: flex; justify-content: center; gap: 20px; margin-top: 20px; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #e0e0e0; }
        .action-btn { font-family: 'Segoe UI', sans-serif; font-size: 15px; font-weight: bold; border: none; padding: 16px 30px; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 10px; }
        
        .btn-blue { background: #2196f3; color: white; } .btn-blue:hover { background: #1976d2; transform: translateY(-2px); }
        .btn-green { background: #4caf50; color: white; } .btn-green:hover { background: #388e3c; transform: translateY(-2px); }
        .btn-dark-blue { background: #1a237e; color: white; } .btn-dark-blue:hover { background: #0d145a; transform: translateY(-2px); }
        
        .table-section { background: var(--white); padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 15px; animation: fadeIn 0.4s ease; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 14px; border-bottom: 1px solid #e0e0e0; text-align: left; font-size: 15px; }
        th { background: var(--primary-blue); color: white; }
        .badge-total { background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 4px; font-weight: bold; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @media print { .sidebar, .control-center { display: none; } .main-content { margin-left: 0; width: 100%; padding: 0; } th { background: #1a237e !important; color: white !important; -webkit-print-color-adjust: exact; } }
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
    <h2 style="color: var(--primary-blue); margin-bottom: 5px; text-align: center;">Reports Control Panel</h2>
    <p style="text-align:center; color: #666; margin-bottom: 30px;">Eeg warbixinta natiijooyinka guud ama u soo degso qaabka aad rabto sxb.</p>
    
    <div class="control-center">
        <button class="action-btn btn-blue" onclick="document.getElementById('reportSection').style.display='block';">📊 View Reports</button>
        <button class="action-btn btn-green" onclick="window.print();">📥 Download/Print Report ▼</button>
        <button class="action-btn btn-dark-blue" onclick="document.getElementById('reportSection').style.display='none';">✖ Close View</button>
    </div>

    <div id="reportSection" class="table-section">
        <h3 style="color: var(--primary-blue); text-align: center; margin-bottom: 20px;">Warbixinta Natiijooyinka Ardayda - Alnuur System</h3>
        <table>
            <thead>
                <tr>
                    <th>Student Email</th>
                    <th>Subject</th>
                    <th>Att. (10)</th>
                    <th>Ass. (10)</th>
                    <th>Mid (30)</th>
                    <th>Final (50)</th>
                    <th>Total (100)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($report_result && mysqli_num_rows($report_result) > 0) { 
                    while($row = mysqli_fetch_assoc($report_result)) { ?>
                    <tr>
                        <td>
                            <?php 
                            if(!empty($row['student_email'])) { echo htmlspecialchars($row['student_email']); }
                            elseif(!empty($row['email'])) { echo htmlspecialchars($row['email']); }
                            else { echo "ID: " . htmlspecialchars($row['student_id'] ?? 'N/A'); }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if(!empty($row['subject_title'])) { echo htmlspecialchars($row['subject_title']); }
                            elseif(!empty($row['subject'])) { echo htmlspecialchars($row['subject']); }
                            else { echo "ID: " . htmlspecialchars($row['subject_id'] ?? 'N/A'); }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['attendance'] ?? '0'); ?></td>
                        <td><?php echo htmlspecialchars($row['assignment'] ?? '0'); ?></td>
                        <td><?php echo isset($row['mid']) ? htmlspecialchars($row['mid']) : (isset($row['mid_exam']) ? htmlspecialchars($row['mid_exam']) : '0'); ?></td>
                        <td><?php echo isset($row['final']) ? htmlspecialchars($row['final']) : (isset($row['final_exam']) ? htmlspecialchars($row['final_exam']) : '0'); ?></td>
                        <td><span class="badge-total"><?php echo isset($row['total']) ? htmlspecialchars($row['total']) : '0'; ?></span></td>
                    </tr>
                <?php } } else { echo "<tr><td colspan='7' style='text-align:center;'>Weli wax natiijo ah lagama helin nidaamka.</td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>