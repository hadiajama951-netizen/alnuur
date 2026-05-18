<?php 
include('conn.php'); 

// Qaybta raadinta (Search Logic)
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM students WHERE name LIKE '%$search%' OR student_id LIKE '%$search%' ORDER BY id DESC";
} else {
    $query = "SELECT * FROM students ORDER BY id DESC";
}
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SPMS - Student Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --primary-color: #1a237e;
            --secondary-color: #3f51b5;
            --success-color: #2e7d32;
            --bg-body: #f4f7f6;
            --text-dark: #333;
        }

        body { background-color: var(--bg-body); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; }

        /* MODAL CSS (Foomka qarsoon) */
        .modal-overlay {
            display: none; /* Marka hore waa qarsanyahay */
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 500px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.3);
            position: relative;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .close-modal {
            position: absolute;
            top: 15px; right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        /* Table & Layout Styles */
        .student-table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; }
        .student-table thead tr { background-color: #f8f9fa; color: var(--primary-color); border-bottom: 2px solid #eee; }
        .student-table th, .student-table td { padding: 18px; text-align: left; border-bottom: 1px solid #eee; }
        .student-table tbody tr:hover { background-color: #f1f3f9; transition: 0.3s; }
        
        .btn-add-new {
            background: var(--primary-color);
            color: white; padding: 12px 25px; border: none; border-radius: 8px;
            cursor: pointer; font-weight: 600; transition: 0.3s;
        }

        .btn-save { 
            background: var(--primary-color);
            color: white; padding: 14px; border: none; border-radius: 8px; 
            cursor: pointer; width: 100%; font-weight: 600; margin-top: 10px;
        }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-control { width: 100%; padding: 12px; border: 1.5px solid #dce1e7; border-radius: 8px; box-sizing: border-box; }

        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .search-input { padding: 12px; border: 1px solid #ddd; border-radius: 8px; width: 250px; }
        .edit-link { color: #3f51b5; text-decoration: none; font-weight: 600; padding: 5px 10px; border-radius: 4px; background: #e8eaf6; }
        .del-link { color: #d32f2f; text-decoration: none; font-weight: 600; padding: 5px 10px; border-radius: 4px; background: #ffebee; margin-left: 5px; }
        .badge-class { background: #e0e0e0; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="app">
        <!-- SIDEBAR (Sidaadii hore) -->
        <aside class="sidebar">
            <div class="brand">SPMS</div>
            <nav class="side-nav">
                <a class="nav-item" href="admin_dashboard.php">🏠 Dashboard</a>
                <a class="nav-item active" href="students.php">👤 Students</a>
                <a class="nav-item" href="subject.php">📚 Subjects</a>
                <a class="nav-item" href="marks.php">📝 Marks</a>
                <a class="nav-item" href="user.php">⚙️ Users</a>
                <a class="nav-item" href="#" style="margin-top: 50px;">🚪 Logout</a>
            </nav>
        </aside>

        <main class="main">
            <div class="content">
                <!-- Header Qaybta -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <div>
                        <h2 style="color: var(--primary-color); margin: 0;">Student Management</h2>
                        <p style="color: #666; margin: 5px 0 0 0;">Maamul xogta ardayda halkan</p>
                    </div>
                    <!-- Badhanka foomka fura -->
                    <button class="btn-add-new" onclick="openModal()">+ Add New Student</button>
                </div>

                <!-- TABLE CARD -->
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h4 style="margin: 0;">📋 Student Directory</h4>
                        <form method="GET" style="display: flex; gap: 10px;">
                            <input type="text" name="search" class="search-input" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn-add-new" style="padding: 10px 15px;">Search</button>
                        </form>
                    </div>

                    <table class="student-table">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Full Name</th>
                                <th>Class</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                            <td style='font-weight: bold; color: #1a237e;'>#{$row['student_id']}</td>
                                            <td>{$row['name']}</td>
                                            <td><span class='badge-class'>{$row['class']}</span></td>
                                            <td style='text-align: center;'>
                                                <a href='edit_student.php?id={$row['id']}' class='edit-link'>Edit</a>
                                                <a href='delete_student.php?id={$row['id']}' class='del-link' onclick='return confirm(\"Ma hubtaa?\")'>Delete</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center; padding: 40px;'>Xog lama helin.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL POPUP (Foomka dhexe) -->
    <div class="modal-overlay" id="studentModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h3 style="color: var(--primary-color); margin-top: 0;">➕ Add New Student</h3>
            <hr style="border: 0.5px solid #eee; margin-bottom: 20px;">
            
            <form action="save_student.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Ahmed Ali..." required>
                </div>
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" class="form-control" placeholder="ID123" required>
                </div>
                <div class="form-group">
                    <label>Class / Form</label>
                    <select name="class" class="form-control">
                        <option value="Form 4A">Form 4A</option>
                        <option value="Form 4B">Form 4B</option>
                        <option value="Form 4C">Form 4C</option>
                    </select>
                </div>
                <button type="submit" name="submit" class="btn-save">Save Student Record</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('studentModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('studentModal').style.display = 'none';
        }

        // Haddii uu qofku meel ka baxsan foomka gujiyo ha xidhmo
        window.onclick = function(event) {
            let modal = document.getElementById('studentModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>