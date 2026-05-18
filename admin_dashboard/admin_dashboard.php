<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMS - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app">
        <!-- Sidebar: Waxay isku xidhaysaa boggaga nidaamka -->
        <aside class="sidebar">
            <div class="brand">SPMS</div>
            <nav class="side-nav">
                <a class="nav-item active" href="admin_dashboard.php">🏠 Dashboard</a>
                <a class="nav-item" href="student.php">👤 Students</a>
                <a class="nav-item" href="subject.php">📚 Subjects</a>
                <a class="nav-item" href="marks.php">📝 Marks</a>
                <a class="nav-item" href="report.php">📊 Reports</a>
                <a class="nav-item" href="user.php">⚙️ Users</a>
                <a class="nav-item" href="#" style="margin-top: 50px;">🚪 Logout</a>
            </nav>
        </aside>

        <main class="main">
            <!-- Header: Meesha laga arko qofka soo galay -->
            <header class="topbar">
                <div class="burger">☰</div>
                <div class="user-profile" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/35" style="border-radius: 50%;" alt="Admin">
                    <span>Admin ▼</span>
                </div>
            </header>

            <div class="content">
                <h2 style="margin-top: 0;">Dashboard Overview</h2>
                
                <!-- KPI Cards: Xogta guud ee nidaamka -->
                <div class="kpis">
                    <div class="kpi blue">
                        <div class="label">Students</div>
                        <div class="value">25</div>
                        <div style="font-size: 11px;">View all</div>
                    </div>
                    <div class="kpi green">
                        <div class="label">Subjects</div>
                        <div class="value">6</div>
                        <div style="font-size: 11px;">View all</div>
                    </div>
                    <div class="kpi orange">
                        <div class="label">Marks Entered</div>
                        <div class="value">150</div>
                        <div style="font-size: 11px;">View all</div>
                    </div>
                    <div class="kpi red">
                        <div class="label">Users</div>
                        <div class="value">3</div>
                        <div style="font-size: 11px;">View all</div>
                    </div>
                </div>

                <!-- Charts & Graphs: Sida ku cad sawirka copy.jpeg -->
                <div class="grid-2">
                    <div class="panel">
                        <h4>Performance Overview</h4>
                        <div class="chart-container">
                            <div class="bar" style="height: 80%; background-color: #4caf50;"><span class="bar-label">A</span></div>
                            <div class="bar" style="height: 60%; background-color: #2196f3;"><span class="bar-label">B</span></div>
                            <div class="bar" style="height: 30%; background-color: #ff9800;"><span class="bar-label">C</span></div>
                            <div class="bar" style="height: 20%; background-color: #ff5722;"><span class="bar-label">D</span></div>
                            <div class="bar" style="height: 10%; background-color: #f44336;"><span class="bar-label">F</span></div>
                        </div>
                    </div>

                    <div class="panel">
                        <h4>Overall Pass Rate</h4>
                        <div class="donut-chart">
                            <div class="donut-inner">
                                <b style="font-size: 22px;">75%</b>
                                <span style="font-size: 11px;">Pass Rate</span>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: center; gap: 15px; margin-top: 15px;">
                            <span style="font-size: 12px;">🟢 Pass</span>
                            <span style="font-size: 12px;">🔴 Fail</span>
                        </div>
                    </div>
                </div>

                <!-- Add Student Quick View: Sida ku cad sawirka copy2.jpeg -->
                <div class="grid-2" style="margin-top: 25px;">
                    <div class="panel">
                        <h4>Quick Add Student</h4>
                        <form action="save_student.php" method="POST">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="full_name" placeholder="Enter full name" required>
                            </div>
                            <div class="form-group">
                                <label>Student ID</label>
                                <input type="text" name="student_id" placeholder="Enter student ID" required>
                            </div>
                            <div style="margin-top: 15px;">
                                <button type="submit" name="save" class="btn-save">Quick Save</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="panel">
                        <h4>Recent Activities</h4>
                        <table style="font-size: 12px;">
                            <thead>
                                <tr><th>ID</th><th>Name</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>001</td><td>Ali Hassan</td><td><span class="badge success">Active</span></td></tr>
                                <tr><td>002</td><td>Ahmed Warsame</td><td><span class="badge success">Active</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>