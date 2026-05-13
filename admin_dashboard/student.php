<?php 
include('conn.php'); 

// Search Logic
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMS - Student Management</title>
    <!-- Waxaan isticmaalaynaa style.css-kaaga guud -->
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS dheeraad ah oo loogu talagalay boga students-ka */
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .btn-add { background: #2e7d32; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .btn-save { background: #1a237e; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; }
        .btn-search { background: #1a237e; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; }
        
        .student-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .student-table thead { background: #f8f9fa; text-align: left; }
        .student-table th, .student-table td { padding: 15px; border-bottom: 1px solid #eee; }
        
        .form-control { border: 1.5px solid #ddd; border-radius: 8px; padding: 12px; width: 100%; box-sizing: border-box; margin-top: 5px; }
        .badge-class { background: #e8eaf6; padding: 5px 12px; border-radius: 15px; font-size: 12px; color: #1a237e; font-weight: bold; }
        #addStudentForm { display: none; animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="app">
        <!-- Sidebar: Midka Dashboard-ka laga soo minguubiyay -->
        <aside class="sidebar">
            <div class="brand">SPMS</div>
            <nav class="side-nav">
                <a class="nav-item" href="admin_dashboard.php">🏠 Dashboard</a>
                <a class="nav-item active" href="student.php">👤 Students</a>
                <a class="nav-item" href="subject.php">📚 Subjects</a>
                <a class="nav-item" href="marks.php">📝 Marks</a>
                <a class="nav-item" href="#">📊 Reports</a>
                <a class="nav-item" href="user.php">⚙️ Users</a>
                <a class="nav-item" href="#" style="margin-top: 50px;">🚪 Logout</a>
            </nav>
        </aside>

        <main class="main">
            <!-- Header -->
            <header class="topbar">
                <div class="burger">☰</div>
                <div class="user-profile" style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://via.placeholder.com/35" style="border-radius: 50%;" alt="Admin">
                    <span>Admin ▼</span>
                </div>
            </header>

            <div class="content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h2 style="margin:0;">Student Management</h2>
                    <button onclick="toggleForm()" class="btn-add" id="toggleBtn">+ Add New Student</button>
                </div>

                <!-- 1. Form-ka (Qarsan) -->
                <div id="addStudentForm" class="card">
                    <h4 style="margin-top:0;">➕ Register New Student</h4>
                    <form action="save_student.php" method="POST">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                            <div>
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Ahmed Ali" required>
                            </div>
                            <div>
                                <label>Student ID</label>
                                <input type="text" name="student_id" class="form-control" placeholder="ID123" required>
                            </div>
                            <div>
                                <label>Class</label>
                                <select name="class" class="form-control">
                                    <option value="Form 4A">Form 4A</option>
                                    <option value="Form 4B">Form 4B</option>
                                </select>
                            </div>
                        </div>
                        <div style="margin-top: 20px; text-align: right;">
                            <button type="button" onclick="toggleForm()" style="background:#ccc; border:none; padding:10px 20px; border-radius:8px; margin-right:10px; cursor:pointer;">Cancel</button>
                            <button type="submit" name="submit" class="btn-save">Save Student</button>
                        </div>
                    </form>
                </div>

                <!-- 2. Table-ka -->
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h4 style="margin:0;">📋 Student Directory</h4>
                        <form method="GET" style="display: flex; gap: 10px;">
                            <input type="text" name="search" style="padding:10px; border-radius:8px; border:1px solid #ddd;" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn-search">Search</button>
                        </form>
                    </div>

                    <table class="student-table">
                        <thead>
                            <tr>
                                <th>ID No</th>
                                <th>Full Name</th>
                                <th>Class</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                            <td style='font-weight:bold;'>#{$row['student_id']}</td>
                                            <td>{$row['name']}</td>
                                            <td><span class='badge-class'>{$row['class']}</span></td>
                                            <td>
                                                <a href='edit.php?id={$row['id']}' style='color:blue; text-decoration:none;'>Edit</a> | 
                                                <a href='delete.php?id={$row['id']}' style='color:red; text-decoration:none;'>Delete</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center;'>No records found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleForm() {
            var form = document.getElementById("addStudentForm");
            var btn = document.getElementById("toggleBtn");
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block";
                btn.innerHTML = "✖ Close Form";
                btn.style.background = "#d32f2f";
            } else {
                form.style.display = "none";
                btn.innerHTML = "+ Add New Student";
                btn.style.background = "#2e7d32";
            }
        }
    </script>
</body>
</html>