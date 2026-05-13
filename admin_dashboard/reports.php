<?php 
include('conn.php'); 

// Query-gan wuxuu isku darayaa dhibcaha arday kasta isagoo xogta ka keenaya saddexda Table
$query = "SELECT s.student_id, s.name, 
          SUM(m.score) as total_score, 
          COUNT(m.subject_id) as subjects_count,
          AVG(m.score) as average_score
          FROM students s
          LEFT JOIN marks m ON s.student_id = m.student_id
          GROUP BY s.student_id, s.name";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Reports - Alnuur System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .report-container { padding: 30px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .report-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .report-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .report-table th { background: #1a237e; color: white; padding: 15px; text-align: left; }
        .report-table td { padding: 12px; border-bottom: 1px solid #eee; }
        .badge { padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 12px; }
        .pass { background: #e8f5e9; color: #2e7d32; }
        .fail { background: #ffebee; color: #c62828; }
        .no-marks { background: #f5f5f5; color: #757575; }
        .print-btn { 
            background: #4527a0; color: white; border: none; padding: 10px 25px; 
            border-radius: 5px; cursor: pointer; float: right; margin-bottom: 20px;
        }
        @media print {
            .sidebar, .print-btn { display: none; }
            .report-container { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <button class="print-btn" onclick="window.print()">🖨️ Print Report</button>
        <h2>📊 Student Academic Performance</h2>
        <p>Warbixinta Guud ee Ardayda Alnuur System</p>

        <div class="report-card">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Total Score</th>
                        <th>Subjects</th>
                        <th>Average (%)</th>
                        <th>Result Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?php echo $row['student_id']; ?></strong></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['total_score'] ?? '0'; ?></td>
                        <td><?php echo $row['subjects_count']; ?></td>
                        <td>
                            <?php 
                            $avg = $row['average_score'];
                            echo $avg ? number_format($avg, 1) . '%' : '0%'; 
                            ?>
                        </td>
                        <td>
                            <?php 
                            if ($row['subjects_count'] == 0) {
                                echo '<span class="badge no-marks">Ma fadhiisan</span>';
                            } elseif ($avg >= 50) {
                                echo '<span class="badge pass">Gudbay (Pass)</span>';
                            } else {
                                echo '<span class="badge fail">Haray (Fail)</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>