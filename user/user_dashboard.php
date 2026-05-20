<?php
include('conn.php'); 
session_start();

// 1. AMNIGA: Hubi in qofka nidaamka soo galay uu yahay User caadi ah
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../login.php"); // Hubi wadada (path-ka) login.php uu u jiro
    exit();
}

$user_id = $_SESSION['user_id'];
$roll_no = $_SESSION['username']; // Roll number-ka ardayga ee uu ku login gareeyay
$full_name = $_SESSION['full_name']; 

// 2. SOO AKHRISASHADA XOGTA JADWALKA STUDENT_MARKS (Hadda waxaan ku raadinaynaa Roll Number)
$marks = null;
if (!empty($roll_no)) {
    $roll_no_clean = mysqli_real_escape_string($conn, trim($roll_no));
    
    // Raadi adoo isticmaalaya roll_no halkii aad magac ka isticmaali lahayd
    $query = "SELECT * FROM student_marks WHERE roll_no = '$roll_no_clean' LIMIT 1";
    $result = mysqli_query($conn, $query) or die("Khalad dhanka Database-ka: " . mysqli_error($conn));
    $marks = mysqli_fetch_assoc($result);
}

// Qaybta raadinta maadooyinka (Search Filter)
$search_value = "";
if (isset($_POST['search_btn'])) {
    $search_value = trim(strtolower($_POST['search_text']));
}

// 3. DIYAARINTA 10-KA MAADO EE JADWALKAAGA KU JIRAY
$subjects_list = [
    'Math' => isset($marks['math']) ? intval($marks['math']) : 0,
    'English' => isset($marks['english']) ? intval($marks['english']) : 0,
    'Science' => isset($marks['science']) ? intval($marks['science']) : 0,
    'Somali' => isset($marks['somali']) ? intval($marks['somali']) : 0,
    'History' => isset($marks['history']) ? intval($marks['history']) : 0,
    'Geography' => isset($marks['geography']) ? intval($marks['geography']) : 0,
    'Arabic' => isset($marks['arabic']) ? intval($marks['arabic']) : 0,
    'Islamic' => isset($marks['islamic']) ? intval($marks['islamic']) : 0,
    'Chemistry' => isset($marks['chemistry']) ? intval($marks['chemistry']) : 0,
    'Physics' => isset($marks['physics']) ? intval($marks['physics']) : 0,
];

// Xisaabinta wadarta guud ee dhibcaha uu keenay ardaygu
$total_obtained = array_sum($subjects_list);

// Function lagu xisaabinayo Grade-ka iyo Remark-ga dhibco kasta
function calculateGrade($score) {
    if ($score >= 90) return ['grade' => 'A+', 'class' => 'grade-a', 'remark' => 'Excellent'];
    if ($score >= 80) return ['grade' => 'A', 'class' => 'grade-a', 'remark' => 'Very Good'];
    if ($score >= 70) return ['grade' => 'B', 'class' => 'grade-a', 'remark' => 'Good'];
    if ($score >= 60) return ['grade' => 'C', 'class' => 'grade-a', 'remark' => 'Pass'];
    if ($score >= 50) return ['grade' => 'D', 'class' => 'grade-a', 'remark' => 'Satisfactory'];
    return ['grade' => 'F', 'class' => 'grade-f', 'remark' => 'Fail'];
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - SPMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #3f51b5;
            --dark-sidebar: #2c3e50;
            --light-bg: #f4f7f6;
        }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: var(--light-bg); display: flex; }
        .sidebar { width: 240px; background-color: var(--dark-sidebar); height: 100vh; color: white; position: fixed; }
        .sidebar-header { padding: 20px; font-size: 22px; font-weight: bold; background: var(--primary-blue); text-align: center; }
        .sidebar-menu { list-style: none; padding: 0; margin-top: 20px; }
        .sidebar-menu li { padding: 15px 20px; display: flex; align-items: center; gap: 10px; transition: 0.3s; cursor: pointer; }
        .sidebar-menu li:hover, .sidebar-menu li.active { background-color: var(--primary-blue); }
        .sidebar-menu li a { color: white; text-decoration: none; width: 100%; display: block; }
        .main-content { margin-left: 240px; width: calc(100% - 240px); box-sizing: border-box; }
        .top-header { background: var(--primary-blue); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .content-body { padding: 30px; }
        .performance-card { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 30px; }
        .student-details-bar { background-color: #e8f0fe; padding: 15px; border-radius: 5px; display: grid; grid-template-columns: 1fr 1fr; margin-bottom: 25px; font-size: 14px; gap: 10px; }
        .detail-item { padding: 5px 0; }
        .detail-item b { color: var(--primary-blue); text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; }
        table th { text-align: left; padding: 12px; border-bottom: 2px solid #eee; background: #fafafa; color: var(--dark-sidebar); }
        table td { padding: 12px; border-bottom: 1px solid #eee; color: #333; }
        table tbody tr:hover { background-color: #f9f9f9; }
        .grade-tag { padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; display: inline-block; }
        .grade-a { background: #e8f5e9; color: #2e7d32; }
        .grade-f { background: #ffebee; color: #c62828; }
        .total-row { background-color: #e8eaf6; font-weight: bold; }
        .total-row td { color: var(--primary-blue); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">SPMS</div>
        <ul class="sidebar-menu">
            <li class="active"><i class="fas fa-chart-line"></i> <a href="user_dashboard.php">My Performance</a></li>
            <li><i class="fas fa-sign-out-alt"></i> <a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-header">
            <i class="fas fa-bars"></i>
            <div class="user-info">Teeda Natiijada: <?php echo htmlspecialchars($full_name); ?></div>
        </div>

        <div class="content-body">
            <div class="performance-card">
                <h3><i class="fas fa-graduation-cap"></i> Natiijada Imtixaanka</h3>
                
                <?php if ($marks): ?>
                    <div class="student-details-bar">
                        <div class="detail-item">Magaca Ardayga: <b><?php echo htmlspecialchars($marks['full_name']); ?></b></div>
                        <div class="detail-item">Roll Number: <b><?php echo htmlspecialchars($marks['roll_no']); ?></b></div>
                        <div class="detail-item">Class / Qaybta: <b><?php echo htmlspecialchars($marks['class']); ?></b></div>
                        <div class="detail-item">Xaaladda Natiijada: <b>Dhammaystiran</b></div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Maaddada (Subject)</th>
                                <th>Dhibcaha (Marks Obtained)</th>
                                <th>Ugu Sarreeya (Max Marks)</th>
                                <th>Darajada (Grade)</th>
                                <th>Faallo (Remark)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subjects_list as $subject => $score): 
                                $gradeData = calculateGrade($score);
                            ?>
                            <tr>
                                <td><b><?php echo ucfirst($subject); ?></b></td>
                                <td><?php echo $score; ?></td>
                                <td>100</td>
                                <td><span class="grade-tag <?php echo $gradeData['class']; ?>"><?php echo $gradeData['grade']; ?></span></td>
                                <td><span class="<?php echo $gradeData['class']; ?>"><?php echo $gradeData['remark']; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <tr class="total-row">
                                <td>Wadarta Guud (Total)</td>
                                <td><?php echo $total_obtained; ?></td>
                                <td>1000</td>
                                <td colspan="2">Perc: <?php echo ($total_obtained / 1000) * 100; ?>%</td>
                            </tr>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="padding: 20px; background: #fff3cd; color: #856404; border-radius: 5px;">
                        ⚠️ Ma jiro wax natiijo ah oo hadda laguu galiyay Roll Number-kaaga (<?php echo htmlspecialchars($roll_no); ?>). Fadlan la xiriir maamulka.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>