<?php 

include('conn.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alnuur_School - Subjects</title>
    <!-- Haddii aad leeyahay style.css dibadda ah halkan ayuu ka akhrisanayaa -->
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS-ka gudaha ah ee loogu talagalay Modal-ka iyo Table-ka */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; }
        .app { display: flex; min-height: 100vh; }
        
        /* Sidebar Styles */
        .sidebar { width: 250px; background-color: #2c3e50; color: white; padding: 20px; }
        .brand { font-size: 20px; font-weight: bold; margin-bottom: 30px; border-bottom: 1px solid #34495e; padding-bottom: 10px; }
        .side-nav a { display: block; color: #bdc3c7; padding: 12px; text-decoration: none; border-radius: 4px; margin-bottom: 5px; }
        .side-nav a.active, .side-nav a:hover { background-color: #34495e; color: white; }

        /* Main Content Area */
        .main { flex: 1; display: flex; flex-direction: column; }
        .topbar { background: white; padding: 15px 30px; display: flex; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .content { padding: 30px; }

        /* Table Styles */
        .panel { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f8f9fa; color: #333; text-align: left; padding: 12px; border-bottom: 2px solid #dee2e6; }
        td { padding: 12px; border-bottom: 1px solid #dee2e6; color: #555; }

        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fff; margin: 10% auto; padding: 25px; border-radius: 8px; width: 400px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: slideDown 0.3s ease; }
        @keyframes slideDown { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .close-btn { float: right; font-size: 24px; cursor: pointer; color: #666; }
        .form-input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn-save { background-color: #4caf50; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-save:hover { background-color: #45a049; }
        .badge { padding: 5px 10px; border-radius: 4px; border: none; color: white; cursor: pointer; font-size: 12px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="app">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">ALnour System</div>
            <nav class="side-nav">
                <a href="admin_dashboard.php">🏠 Dashboard</a>
                <a href="student.php">👤 Students</a>
                <a href="subject.php" class="active">📚 Subjects</a>
                <a href="marks.php">📝 Marks</a>
                <a href="reports.php">📊 Reports</a>
                <a href="user.php">⚙️ Users</a>
                <a href="logout.php" style="margin-top: 50px; color: #e74c3c;">🚪 Logout</a>
            </nav>
        </aside>

        <main class="main">
            <!-- Header -->
            <header class="topbar">
                <div class="burger" style="cursor: pointer;">☰</div>
                <div class="user-profile">
                    <span><strong>Admin</strong> ▼</span>
                </div>
            </header>

            <div class="content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; color: #2c3e50;">Manage Subjects</h2>
                    <button class="btn-save" id="openModal">+ Add New Subject</button>
                </div>
                
                <div class="panel">
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Subject Name</th>
                                <th>Teacher</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Kasoo qaad xogta database-ka
                            $query = mysqli_query($conn, "SELECT * FROM subjects ORDER BY id DESC");
                            
                            if(mysqli_num_rows($query) > 0) {
                                while($row = mysqli_fetch_array($query)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['subject_code']; ?></td>
                                       <td><?php echo $row['subject_name']; ?></td>
                                        <td><?php echo $row['teacher_name']; ?></td>
                                        <td>
                                            <a href="edit_subject.php?id=<?php echo $row['id']; ?>" class="badge" style="background-color: #2196f3;">Edit</a>
                                            <a href="delete_subject.php?id=<?php echo $row['id']; ?>" class="badge" style="background-color: #f44336;" onclick="return confirm('Ma hubtaa inaad tirtirto?')">Delete</a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center;'>Ma jirto xog la helay.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Form (Add Subject) -->
    <div id="subjectModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h3 style="margin-top: 0; color: #2c3e50;">Add New Subject</h3>
            <hr>
            <!-- action="save_subject.php" waa faylka xogta qabanaya -->
            <form action="save_subject.php" method="POST" style="margin-top: 15px;">
                <label style="font-size: 13px; font-weight: bold;">Subject Code</label>
                <input type="text" name="subject_code" class="form-input" placeholder="e.g. MAT101" required>
                
                <label style="font-size: 13px; font-weight: bold;">Subject Name</label>
                <input type="text" name="subject_name" class="form-input" placeholder="e.g. Mathematics" required>
                
                <label style="font-size: 13px; font-weight: bold;">Assign Teacher</label>
                <select name="teacher" class="form-input" required>
                    <option value="">Select Teacher</option>
                    <option value="Ali Hassan">Mr. Ali Hassan</option>
                    <option value="Ahmed Warsame">Mr. Ahmed Warsame</option>
                    <option value="Fatumo Abdi">Ms. Fatumo Abdi</option>
                </select>
                
                <button type="submit" name="save_btn" class="btn-save" style="width: 100%; margin-top: 10px;">Save Subject</button>
            </form>
        </div>
    </div>

    <!-- JavaScript for Modal -->
    <script>
        const modal = document.getElementById("subjectModal");
        const btn = document.getElementById("openModal");
        const span = document.getElementById("closeModal");

        // Fura Modal-ka
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Xira Modal-ka (Marka X-ta la riixo)
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Xira Modal-ka (Marka meel bannaanka ah la riixo)
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>