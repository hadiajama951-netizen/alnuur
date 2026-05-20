


<?php
// 1. HALKAN KU QOR JIDKA UU YAAL COONNECTION-KAAGA (MySQLi)
include('conn.php'); 

// 2. SOO JIIDASHADA XOGTA GUUD (Database ka keen)
$total_students = 0;
$total_subjects = 10; // Maadaama maadooyinka nidaamka ku jira ay yihiin 10 maado
if ($conn) {
    $student_count_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM student_marks");
    $total_students = ($student_count_res) ? mysqli_fetch_assoc($student_count_res)['total'] : 0;
}

// 3. SOO AQRAVTA XOGTA WARBIXINTA EE MIISKA (TABLE) - OO AY KU JIRTO SEARCH-KA
$report_data = [];
$search_query = "";

if ($conn) {
    $sql_condition = "";
    
    // Haddii uu jiro qof wax raadinaya
    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search_query = mysqli_real_escape_string($conn, trim($_GET['search']));
        // Wuxuu ka raadinayaa Roll No ama Magaca ardayga
        $sql_condition = " WHERE roll_no LIKE '%$search_query%' OR full_name LIKE '%$search_query%' ";
    }

    $query = "SELECT roll_no, full_name, class, 
              (math + english + science + somali + history + geography + arabic + islamic + chemistry + physics) as total_marks,
              ((math + english + science + somali + history + geography + arabic + islamic + chemistry + physics) / 10) as average
              FROM student_marks $sql_condition ORDER BY id DESC";
              
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
            --primary-bg: #3b50b2; 
            --sidebar-bg: #2d3d5a; 
            --body-bg: #f4f6f9;
            --text-dark: #333;
        }

        body { 
            background-color: var(--body-bg); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 0;
        }

        .app { display: flex; min-height: 100vh; }

        /* SIDEBAR STYLES */
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

        .nav-item:hover { background-color: rgba(255, 255, 255, 0.05); color: white; }
        .nav-item.active { background-color: rgba(0, 0, 0, 0.15); color: white; border-left: 4px solid #fff; }

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

        .main-content { flex: 1; display: flex; flex-direction: column; }
        .container { padding: 30px; }

        /* ACTION BAR & SEARCH */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 20px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            background: white;
            padding: 6px 12px;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            align-items: center;
        }

        .search-form input {
            border: none;
            padding: 8px;
            font-size: 14px;
            outline: none;
            width: 250px;
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
        .btn-excel { background-color: #2e7d32; color: white; }
        .btn-search { background-color: #2d3d5a; color: white; border-radius: 4px; padding: 8px 15px; text-decoration: none; font-size: 14px; }
        .btn-clear { background-color: #e0e0e0; color: #333; border-radius: 4px; padding: 8px 15px; text-decoration: none; font-size: 14px; }

        /* TABLE STYLES */
        .report-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .report-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .report-table th {
            background-color: #f8f9fa;
            color: #555;
            text-align: left;
            padding: 14px;
            border-bottom: 2px solid #eceff1;
            font-weight: 600;
        }

        .report-table td { padding: 14px; border-bottom: 1px solid #eceff1; color: #333; }
        .report-table tr:hover { background-color: #f5f7fa; }

        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-pass { background-color: #e8f5e9; color: #2e7d32; }
        .status-fail { background-color: #ffebee; color: #c62828; }
        .no-records { text-align: center; color: #888; padding: 30px; font-style: italic; }

        /* PRINT STYLES */
        @media print {
            .sidebar, .top-header, .action-bar, .search-form, .brand-section { display: none !important; }
            body, .app, .main-content, .container { background: white !important; padding: 0 !important; margin: 0 !important; width: 100% !important; }
            .report-card { box-shadow: none !important; padding: 10px !important; }
            .report-table th { background-color: #1a237e !important; color: white !important; -webkit-print-color-adjust: exact; }
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
                <a class="nav-item" href="logout.php" style="margin-top: auto; padding-bottom: 30px;">🚪 Logout</a>
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
                    
                    <form method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Ku raadi Roll No ama Magaca..." value="<?php echo htmlspecialchars($search_query); ?>">
                        <button type="submit" class="btn-search">🔍 Raadi</button>
                        <?php if(!empty($search_query)): ?>
                            <a href="reports.php" class="btn-clear">Xiri</a>
                        <?php endif; ?>
                    </form>

                    <div>
                        <button class="btn btn-print" onclick="window.print()">🖨️ Print Report</button>
                        <button class="btn btn-excel" onclick="exportTableToExcel('reportTable', 'Student-Report')">📄 Excel</button>
                    </div>
                </div>

                <div class="report-card">
                    <h3 style="margin-top: 0; color: var(--sidebar-bg); border-bottom: 2px solid #f4f6f9; padding-bottom: 15px;">
                        📋 Ardayda iyo Natiijada Guud ee Imtixaanka 
                        <?php if(!empty($search_query)): ?> 
                            <span style="font-size: 14px; color:#2196f3; font-weight:normal;"> (Natiijada Raadinta: "<?php echo htmlspecialchars($search_query); ?>")</span> 
                        <?php endif; ?>
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
                                    <td colspan="6" class="no-records">Wax xog ah oo la helay ma jiraan. Hubi Roll No ama Magaca aad qortay.</td>
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