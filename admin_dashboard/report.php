<?php
// 1. HALKAN KU QOR JIDKA UU YAAL COONNECTION-KAAGA (MySQLi)
include('conn.php'); 

// 2. SOO JIIDASHADA XOGTA GUUD (Wixii ku jiray Dashboard-ka)
$total_students = 25; // Haddii aad database ka keenayso: mysqli_num_rows(mysqli_query($conn, "SELECT * FROM students"));
$total_subjects = 6;
$total_marks = 150;
$pass_rate = "75%";

// 3. SOO AQRAVTA XOGTA WARBIXINTA EE MIISKA (TABLE)
$report_data = [];
if ($conn) {
    // Waxaad halkan ka beddeli kartaa SQL Query-ga iyadoo loo eegayo sida aad u rabto warbixinta
    $query = "SELECT roll_no, full_name, class, 
              (math + english + science + somali + history + geography + arabic + islamic + chemistry + physics) as total_marks,
              ((math + english + science + somali + history + geography + arabic + islamic + chemistry + physics) / 10) as average
              FROM student_marks ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $report_data[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMS - Reports Dashboard</title>
    <style>
        :root {
            --primary-bg: #3b50b2; /* Midabka sare ee buluugga ah */
            --sidebar-bg: #2d3d5a; /* Midabka sidebar-ka madowga xiga */
            --body-bg: #f4f6f9;
            --text-dark: #333;
        }

        body { 
            background-color: var(--body-bg); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 0;
        }

        /* LAYOUT STYLES */
        .app {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR (U ekaanshiyaha sawirkaaga) */
        .sidebar {
            width: 260px;
            min-width: 260px;
            background-color: var(--sidebar-bg);
            color: #b0bec5;
            display: flex;
            flex-direction: column;
        }

        .brand-section {
            background-color: var(--primary-bg);
            color: white;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
        }

        .side-nav {
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .nav-item {
            padding: 15px 25px;
            color: #cfd8dc;
            text-decoration: none;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: 0.3s;
        }

        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: white;
        }

        .nav-item.active {
            background-color: rgba(0, 0, 0, 0.15);
            color: white;
            border-left: 4px solid #fff;
        }

        /* TOP HEADER */
        .top-header {
            background-color: var(--primary-bg);
            height: 68px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            color: white;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .container {
            padding: 30px;
        }

        /* CARDS STATS (Sidii Dashboard-kaaga) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            position: relative;
        }

        .card-blue { background-color: #2196f3; }
        .card-green { background-color: #4caf50; }
        .card-orange { background-color: #ff9800; }
        .card-red { background-color: #f44336; }

        .stat-card h3 { margin: 0 0 10px 0; font-size: 16px; font-weight: 400; opacity: 0.9; }
        .stat-card .number { font-size: 32px; font-weight: bold; margin-bottom: 5px; }
        .stat-card .link { font-size: 12px; opacity: 0.8; text-decoration: none; color: white; }

        /* REPORT BUTTONS */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print { background-color: var(--primary-bg); color: white; }
        .btn-excel { background-color: #2e7d32; color: white; margin-left: 10px; }

        /* TABLE STYLES */
        .report-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .report-table th {
            background-color: #f8f9fa;
            color: #555;
            text-align: left;
            padding: 14px;
            border-bottom: 2px solid #eceff1;
            font-weight: 600;
        }

        .report-table td {
            padding: 14px;
            border-bottom: 1px solid #eceff1;
            color: #333;
        }

        .report-table tr:hover {
            background-color: #f5f7fa;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pass { background-color: #e8f5e9; color: #2e7d32; }
        .status-fail { background-color: #ffebee; color: #c62828; }

        /* PRINT STYLES (Marka la daabacayo si uu u qurux baxo) */
        @media print {
            .sidebar, .top-header, .action-bar, .link { display: none !important; }
            .main-content { width: 100% !important; }
            .container { padding: 0 !important; }
            .report-card { box-shadow: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body>

    <div class="app">
        <aside class="sidebar">
            <div class="brand-section">SPMS</div>
            <nav class="side-nav">
                <a class="nav-item" href="admin_dashboard.php">🏠 Dashboard</a>
                <a class="nav-item" href="students.php">👤 Students</a>
                <a class="nav-item" href="subject.php">📚 Subjects</a>
                <a class="nav-item" href="marks.php">📝 Marks</a>
                <a class="nav-item active" href="reports.php">📊 Reports</a>
                <a class="nav-item" href="user.php">⚙️ Users</a>
                <a class="nav-item" href="#" style="margin-top: auto; padding-bottom: 30px;">🚪 Logout</a>
            </nav>
        </aside>

        <div class="main-content">
            <header class="top-header">
                <div style="font-size: 20px; cursor: pointer;">☰</div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-weight: 500;">👤 Admin</span>
                    <span style="font-size: 12px;">▼</span>
                </div>
            </header>

            <main class="container">
                
                <div class="action-bar">
                    <div>
                        <h2 style="margin: 0; color: #2c3e50;">System Performance & Academic Reports</h2>
                        <p style="margin: 5px 0 0 0; color: #7f8c8d; font-size: 14px;">Xogta guud iyo warbixinta dhibcaha ardayda</p>
                    </div>
                    <div>
                        <button class="btn btn-print" onclick="window.print()">🖨️ Print Report</button>
                        <button class="btn btn-excel" onclick="exportTableToExcel('reportTable', 'Student-Report')">📄 Excel</button>
                    </div>
                </div>

                
                <div class="report-card">
                    <h3 style="margin-top: 0; color: var(--sidebar-bg); border-bottom: 2px solid #f4f6f9; padding-bottom: 15px;">
                        📋 Ardayda iyo Natiijada Guud ee Imtixaanka
                    </h3>
                    
                    <table class="report-table" id="reportTable">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Full Name</th>
                                <th>Class</th>
                                <th>Total Marks</th>
                                <th>Average</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($report_data)): ?>
                                <tr>
                                    <td><strong>#88</strong></td>
                                    <td>CABDIFATAAX NUUR XUSEEN</td>
                                    <td>Form 4A</td>
                                    <td>871 / 1000</td>
                                    <td>87.1%</td>
                                    <td><span class="status-badge status-pass">Pass</span></td>
                                </tr>
                                <tr>
                                    <td><strong>#92</strong></td>
                                    <td>AMINA AHMED CALI</td>
                                    <td>Form 4B</td>
                                    <td>420 / 1000</td>
                                    <td>42.0%</td>
                                    <td><span class="status-badge status-fail">Fail</span></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($report_data as $row): ?>
                                    <tr>
                                        <td><strong>#<?php echo htmlspecialchars($row['roll_no']); ?></strong></td>
                                        <td style="text-transform: uppercase;"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['class']); ?></td>
                                        <td><?php echo $row['total_marks']; ?> / 1000</td>
                                        <td><?php echo number_format($row['average'], 1); ?>%</td>
                                        <td>
                                            <?php if ($row['average'] >= 50): ?>
                                                <span class="status-badge status-pass">Pass</span>
                                            <?php else: ?>
                                                <span class="status-badge status-fail">Fail</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </main>
        </div>
    </div>

    <script>
        function exportTableToExcel(tableID, filename = ''){
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById(tableID);
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
            
            filename = filename?filename+'.xls':'excel_data.xls';
            downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);
            
            if(navigator.msSaveOrOpenBlob){
                var blob = new Blob(['\ufeff', tableHTML], { type: dataType });
                navigator.msSaveOrOpenBlob( blob, filename);
            } else {
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
                downloadLink.download = filename;
                downloadLink.click();
            }
        }
    </script>
</body>
</html>