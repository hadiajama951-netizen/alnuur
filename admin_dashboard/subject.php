<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMS - Manage Subjects</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Qurxinta gaarka ah ee Table-ka Madooyinka */
        .subject-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
            margin-top: 10px;
        }

        .subject-table thead tr {
            background-color: #1a237e; /* Dark Blue */
            color: rgb(0, 0, 0);
        }

        .subject-table th {
            padding: 15px;
            text-align: left;
            text-transform: uppercase;
            font-size: 13px;
        }

        .subject-table th:first-child { border-radius: 8px 0 0 8px; }
        .subject-table th:last-child { border-radius: 0 8px 8px 0; }

        .subject-table tbody tr {
            background-color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        .subject-table tbody tr:hover {
            transform: translateY(-2px);
            background-color: #f0f3ff;
        }

        .subject-table td {
            padding: 15px;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .subject-table td:first-child { border-left: 1px solid #eee; border-radius: 8px 0 0 8px; }
        .subject-table td:last-child { border-right: 1px solid #eee; border-radius: 0 8px 8px 0; }

        /* Badhamada Edit & Delete */
        .action-btns {
            display: flex;
            gap: 10px;
        }

        .btn-edit {
            background-color: #2196f3;
            color: white;
            padding: 7px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
            padding: 7px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-save {
            background-color: #1a237e;
            color: white;
            padding: 10px 25px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="app">
        <!-- Sidebar: Wuxuu u taagan yahay sidii aad u haysatay -->
        <aside class="sidebar">
            <div class="brand">SPMS</div>
            <nav class="side-nav">
                <a class="nav-item" href="admin_dashboard.php">🏠 Dashboard</a>
                <a class="nav-item" href="student.php">👤 Students</a>
                <a class="nav-item active" href="subjects.php">📚 Subjects</a>
                <a class="nav-item" href="marks.php">📝 Marks</a>
                <a class="nav-item" href="report.php">📊 Reports</a>
                <a class="nav-item" href="user.php">⚙️ Users</a>
                <a class="nav-item" href="#" style="margin-top: 50px;">🚪 Logout</a>
            </nav>
        </aside>

        <main class="main">
            <header class="topbar">
                <div class="burger">☰</div>
                <div class="user-profile" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/35" style="border-radius: 50%;" alt="Admin">
                    <span>Admin ▼</span>
                </div>
            </header>

            <div class="content">
                <h2 style="margin-top: 0; color: #1a237e;">Subject Management</h2>
                
                <div class="grid-2">
                    <!-- Bidix: Foomka lagu daro Maadada -->
                    <div class="panel">
                        <h4 style="border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">Add New Subject</h4>
                        <form action="save_subject.php" method="POST">
                           
                            
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Subject Code</label>
                                <input type="text" name="subject_code" placeholder="e.g. MATH101" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                            </div>
                             <div class="form-group" style="margin-bottom: 15px;">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Subject Name</label>
                                <input type="text" name="subject_name" placeholder="e.g. Mathematics" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                            </div>

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Teacher</label>
                                <select name="dept" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                                    <option value="Science">axmed</option>
                                    <option value="Arts">Ali</option>
                                    <option value="Languages">husein</option>
                                </select>
                            </div>
                            
                            <button type="submit" name="save" class="btn-save">Register Subject</button>
                        </form>
                    </div>

                    <!-- Midig: Liiska Madooyinka (Subject List) -->
                    <div class="panel">
                        <h4 style="border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">Subject List</h4>
                        <table class="subject-table">
                            <thead>
                                <tr>
                                    <th>S_Code</th>
                                    <th>Subject</th>
                                    <th>Teacher</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ENG101</td>
                                    <td>English</td>
                                    <td>Languages</td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="#" class="btn-edit">Edit</a>
                                            <a href="#" class="btn-delete">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>MATH202</td>
                                    <td>Mathematics</td>
                                    <td>Science</td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="#" class="btn-edit">Edit</a>
                                            <a href="#" class="btn-delete">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>