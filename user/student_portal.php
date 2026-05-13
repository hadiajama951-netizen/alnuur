<?php 
session_start();
include('../conn.php'); 

if(!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$id = $_SESSION['student_id'];
$res = mysqli_query($conn, "SELECT name FROM students WHERE student_id = '$id'");
$student = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Portal</title>
    <style>
        body { font-family: sans-serif; margin:0; background:#f4f4f4; }
        .header { background:#1a237e; color:white; padding:20px; display:flex; justify-content:space-between; }
        .container { padding:20px; display:flex; gap:20px; }
        .card { background:white; padding:20px; border-radius:10px; flex:1; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:10px; border-bottom:1px solid #eee; }
    </style>
</head>
<body>
    <div class="header">
        <span>Welcome, <b><?php echo $student['name']; ?></b></span>
        <a href="logout.php" style="color:white;">Logout</a>
    </div>

    <div class="container">
        <div class="card">
            <h3>Exam Results</h3>
            <table>
                <tr><th>Subject</th><th>Score</th></tr>
                <?php 
                $m_query = mysqli_query($conn, "SELECT sub.subject_name, m.score FROM marks m 
                                                JOIN subjects sub ON m.subject_id = sub.id 
                                                WHERE m.student_id = '$id'");
                while($m = mysqli_fetch_assoc($m_query)) {
                    echo "<tr><td>{$m['subject_name']}</td><td><b>{$m['score']}</b></td></tr>";
                }
                ?>
            </table>
        </div>

        <div class="card">
            <h3>Attendance</h3>
            <?php 
            $att = mysqli_query($conn, "SELECT COUNT(*) as total, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present 
                                        FROM attendance WHERE student_id = '$id'");
            $a = mysqli_fetch_assoc($att);
            echo "<p>Days Present: " . ($a['present'] ?? 0) . " / " . ($a['total'] ?? 0) . "</p>";
            ?>
        </div>
    </div>
</body>
</html>