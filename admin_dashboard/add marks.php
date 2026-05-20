<?php
include('conn.php'); 
$message = "";

// 1. QAYBTA AJAX-KA: Waxay xogta ka akhrinaysaa shaxda STUDENTS hadda!
if (isset($_GET['fetch_student'])) {
    $roll_no = mysqli_real_escape_string($conn, trim($_GET['roll_no']));
    
    // Halkan waxaa isbeddel lagu sameeyay shaxda laga akhrinayo (students)
    $query = mysqli_query($conn, "SELECT name, class FROM students WHERE student_id = '$roll_no'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        // Waxaan u bixiyey 'full_name' si aysan JavaScript-iga dambe u khalkhalin
        echo json_encode(['status' => 'success', 'full_name' => $data['name'], 'class' => $data['class']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ Ardaygan laguma dhex arag nidaamka school-ka! Fadlan marka hore ku dar Student Directory.']);
    }
    exit; 
}

// 2. QAYBTA KEYDINTA DHIBCAHA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_marks'])) {
    $roll_no = mysqli_real_escape_string($conn, trim($_POST['roll_no']));
    $math = isset($_POST['math']) ? intval($_POST['math']) : 0;
    $english = isset($_POST['english']) ? intval($_POST['english']) : 0;
    $science = isset($_POST['science']) ? intval($_POST['science']) : 0;
    $somali = isset($_POST['somali']) ? intval($_POST['somali']) : 0;
    $history = isset($_POST['history']) ? intval($_POST['history']) : 0;
    $geography = isset($_POST['geography']) ? intval($_POST['geography']) : 0;
    $arabic = isset($_POST['arabic']) ? intval($_POST['arabic']) : 0;
    $islamic = isset($_POST['islamic']) ? intval($_POST['islamic']) : 0;
    $chemistry = isset($_POST['chemistry']) ? intval($_POST['chemistry']) : 0;
    $physics = isset($_POST['physics']) ? intval($_POST['physics']) : 0;

    if (!empty($roll_no)) {
        $sql = "UPDATE student_marks SET 
                math = $math, english = $english, science = $science, somali = $somali, 
                history = $history, geography = $geography, arabic = $arabic, islamic = $islamic, 
                chemistry = $chemistry, physics = $physics 
                WHERE roll_no = '$roll_no'";
                
        if (mysqli_query($conn, $sql)) {
            $message = "
            <div style='margin: 20px 0; background: #e8f5e9; border: 1px solid #2e7d32; color: #2e7d32; padding: 15px; border-radius: 8px; font-weight: 500;'>
                ✅ Dhibcihii ardayga si guul leh ayaa loo cusboonaysiiyey (Updated)!
            </div>";
        } else {
            $message = "
            <div style='margin: 20px 0; background: #ffebee; border: 1px solid #c62828; color: #c62828; padding: 15px; border-radius: 8px; font-weight: 500;'>
                ❌ Khalad: " . mysqli_error($conn) . "
            </div>";
        }
    }
}

$students = [];
if ($conn) {
    $result = mysqli_query($conn, "SELECT * FROM student_marks ORDER BY id DESC");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMS - Maamulka Dhibcaha (Marks)</title>
     <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --primary-color: #1a237e;
            --secondary-color: #3f51b5;
            --success-color: #2e7d32;
            --bg-body: #f4f7f6;
            --text-dark: #333;
        }
        body { background-color: var(--bg-body); font-family: 'Segoe UI', sans-serif; margin: 0; }
        .app { display: flex; min-height: 100vh; width: 100%; }
        .sidebar { width: 250px; min-width: 250px; background-color: #4b53b1; color: white; min-height: 100vh; box-sizing: border-box; }
        .main { flex: 1; padding: 30px; overflow-x: hidden; box-sizing: border-box; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 30px; border-radius: 15px; width: 650px; max-height: 85vh; overflow-y: auto; box-shadow: 0 5px 30px rgba(0,0,0,0.3); position: relative; }
        .close-modal { position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; color: #666; }
        .student-table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; }
        .student-table thead tr { background-color: #f8f9fa; color: var(--primary-color); border-bottom: 2px solid #eee; }
        .student-table th, .student-table td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        .student-table tbody tr:hover { background-color: #f1f3f9; }
        .btn-add-new { background: var(--primary-color); color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .btn-save { background: var(--primary-color); color: white; padding: 14px; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-weight: 600; margin-top: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-control { width: 100%; padding: 12px; border: 1.5px solid #dce1e7; border-radius: 8px; box-sizing: border-box; }
        .grid-inputs { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 15px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .search-input { padding: 12px; border: 1px solid #ddd; border-radius: 8px; width: 250px; }
        .edit-link { color: #3f51b5; text-decoration: none; font-weight: 600; padding: 5px 10px; border-radius: 4px; background: #e8eaf6; font-size: 12px; }
        .del-link { color: #d32f2f; text-decoration: none; font-weight: 600; padding: 5px 10px; border-radius: 4px; background: #ffebee; margin-left: 5px; font-size: 12px; }
        .badge-class { background: #e0e0e0; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; color: #333; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .marks-pass { color: #2e7d32; font-weight: 600; }
        .marks-fail { color: #c62828; font-weight: 600; }
        .btn-search-trigger { background: #3f51b5; color: white; border: none; padding: 0 15px; border-radius: 8px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="brand">SPMS</div>
            <nav class="side-nav">
                <a class="nav-item" href="admin_dashboard.php">🏠 Dashboard</a>
                <a class="nav-item" href="students.php">👤 Students</a>
                <a class="nav-item" href="subject.php">📚 Subjects</a>
                <a class="nav-item active" href="add_marks.php">📝 Marks</a>
                <a class="nav-item" href="report.php">📊 Reports</a>
                <a class="nav-item" href="user.php">⚙️ Users</a>
                <a class="nav-item" href="logout.php" style="margin-top: 50px;">🚪 Logout</a>
            </nav>
        </aside>
        
        <main class="main">
            <div class="content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <div>
                        <h2 style="color: var(--primary-color); margin: 0;">Student Marks Management</h2>
                        <p style="color: #666; margin: 5px 0 0 0;">Maamul xogta dhibcaha ardayda halkan</p>
                    </div>
                    <button class="btn-add-new" onclick="openModal()">+ Add New Student Marks</button>
                </div>
                
                <?php echo $message; ?>
                
                <div class="card" style="overflow-x: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h4 style="margin: 0;">📋 Student Marks Directory (10 Subjects)</h4>
                    </div>
                    <table class="student-table">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Full Name</th>
                                <th>Class</th>
                                <th class="text-center">Math</th>
                                <th class="text-center">English</th>
                                <th class="text-center">Science</th>
                                <th class="text-center">Somali</th>
                                <th class="text-center">History</th>
                                <th class="text-center">Geo</th>
                                <th class="text-center">Arabic</th>
                                <th class="text-center">Islamic</th>
                                <th class="text-center">Chem</th>
                                <th class="text-center">Phys</th>
                                <th class="text-center" style="background: #e8eaf6; color: var(--primary-color);">Total</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)): ?>
                                <tr>
                                    <td colspan="15" style="text-align: center; color: #888; padding: 20px;">Wax xog ah kuma jiraan database-ka hadda.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($students as $row): 
                                    $total = $row['math'] + $row['english'] + $row['science'] + $row['somali'] + $row['history'] + $row['geography'] + $row['arabic'] + $row['islamic'] + $row['chemistry'] + $row['physics'];
                                ?>
                                    <tr>
                                        <td class="font-bold" style="color: var(--primary-color);">#<?php echo htmlspecialchars($row['roll_no']); ?></td>
                                        <td style="text-transform: uppercase; font-weight: 500;"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                        <td><span class="badge-class"><?php echo htmlspecialchars($row['class']); ?></span></td>
                                        
                                        <td class="text-center <?php echo $row['math'] < 50 ? 'marks-fail' : 'marks-pass'; ?>"><?php echo $row['math']; ?></td>
                                        <td class="text-center <?php echo $row['english'] < 50 ? 'marks-fail' : 'marks-pass'; ?>"><?php echo $row['english']; ?></td>
                                        <td class="text-center <?php echo $row['science'] < 50 ? 'marks-fail' : 'marks-pass'; ?>"><?php echo $row['science']; ?></td>
                                        <td class="text-center <?php echo $row['somali'] < 50 ? 'marks-fail' : 'marks-pass'; ?>"><?php echo $row['somali']; ?></td>
                                        <td class="text-center <?php echo $row['history'] < 50 ? 'marks-fail' : 'marks-pass'; ?>"><?php echo $row['history']; ?></td>
                                        <td class="text-center <?php echo $row['geography'] < 50 ? 'marks-fail' : 'marks-pass'; ?>"><?php echo $row['geography']; ?></td>
                                        <td class="text-center <?php echo $row['arabic'] < 50 ? 'marks-fail' : 'marks-pass'; ?>"><?php echo $row['arabic']; ?></td>
                                        <td class="text-center <?php echo $row['islamic'] < 50 ? 'marks-fail' : 'marks-pass'; ?>"><?php echo $row['islamic']; ?></td>
                                        <td class="text-center <?php echo $row['chemistry'] < 50 ? 'marks-fail' : 'marks-pass'; ?>"><?php echo $row['chemistry']; ?></td>
                                        <td class="text-center <?php echo $row['physics'] < 50 ? 'marks-fail' : 'marks-pass'; ?>"><?php echo $row['physics']; ?></td>
                                        <td class="text-center font-bold" style="background: #e8eaf6; color: var(--primary-color);"><?php echo $total; ?>/1000</td>
                                        <td class="text-center">
                                            <a href="#" class="edit-link" onclick="editMarks('<?php echo $row['roll_no']; ?>')">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modal-overlay" id="marksModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h3 style="color: var(--primary-color); margin-top: 0;">➕ Geli Dhibco Maadooyin Cusub</h3>
            <hr style="border: 0.5px solid #eee; margin-bottom: 20px;">
            
            <form action="" method="POST">
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 2; display: flex; flex-direction: column;">
                        <label>Roll No / Student ID *</label>
                        <div style="display: flex; gap: 5px;">
                            <input type="text" id="modal_roll_no" name="roll_no" required placeholder="Tusaale: 105" class="form-control">
                            <button type="button" class="btn-search-trigger" onclick="searchStudent()">Search</button>
                        </div>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Class / Form</label>
                        <input type="text" id="modal_class" name="class" readonly placeholder="Si otomaatig ah" class="form-control" style="background: #eee;">
                    </div>
                </div>

                <div class="form-group">
                    <label>Full Name (Magaca Ardayga)</label>
                    <input type="text" id="modal_full_name" name="full_name" readonly placeholder="Si otomaatig ah ayuu u soo baxayaa" class="form-control" style="background: #eee;">
                </div>

                <h5 style="color: var(--primary-color); margin: 20px 0 5px 0; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Dhibcaha Maadooyin (Max 100)</h5>
                <hr style="border: 0.5px solid #f1f3f9;">

                <div class="grid-inputs">
                    <div class="form-group"><label style="font-size: 13px;">Math</label><input type="number" name="math" min="0" max="100" value="0" class="form-control" style="padding: 8px;"></div>
                    <div class="form-group"><label style="font-size: 13px;">English</label><input type="number" name="english" min="0" max="100" value="0" class="form-control" style="padding: 8px;"></div>
                    <div class="form-group"><label style="font-size: 13px;">Science</label><input type="number" name="science" min="0" max="100" value="0" class="form-control" style="padding: 8px;"></div>
                    <div class="form-group"><label style="font-size: 13px;">Somali</label><input type="number" name="somali" min="0" max="100" value="0" class="form-control" style="padding: 8px;"></div>
                    <div class="form-group"><label style="font-size: 13px;">History</label><input type="number" name="history" min="0" max="100" value="0" class="form-control" style="padding: 8px;"></div>
                    <div class="form-group"><label style="font-size: 13px;">Geography</label><input type="number" name="geography" min="0" max="100" value="0" class="form-control" style="padding: 8px;"></div>
                    <div class="form-group"><label style="font-size: 13px;">Arabic</label><input type="number" name="arabic" min="0" max="100" value="0" class="form-control" style="padding: 8px;"></div>
                    <div class="form-group"><label style="font-size: 13px;">Islamic</label><input type="number" name="islamic" min="0" max="100" value="0" class="form-control" style="padding: 8px;"></div>
                    <div class="form-group"><label style="font-size: 13px;">Chemistry</label><input type="number" name="chemistry" min="0" max="100" value="0" class="form-control" style="padding: 8px;"></div>
                    <div class="form-group"><label style="font-size: 13px;">Physics</label><input type="number" name="physics" min="0" max="100" value="0" class="form-control" style="padding: 8px;"></div>
                </div>

                <button type="submit" name="save_marks" class="btn-save">Keydi Xogta Dhibcaha</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('marksModal').style.display = 'flex'; }
        function closeModal() {
            document.getElementById('marksModal').style.display = 'none';
            document.getElementById('modal_roll_no').value = '';
            document.getElementById('modal_full_name').value = '';
            document.getElementById('modal_class').value = '';
        }

        function searchStudent() {
            let rollNo = document.getElementById('modal_roll_no').value;
            if(rollNo.trim() == "") { alert("Fadlan qor Roll Number-ka marka hore!"); return; }
            
            fetch('add_marks.php?fetch_student=1&roll_no=' + rollNo)
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        document.getElementById('modal_full_name').value = data.full_name;
                        document.getElementById('modal_class').value = data.class;
                    } else {
                        alert(data.message);
                        document.getElementById('modal_full_name').value = '';
                        document.getElementById('modal_class').value = '';
                    }
                })
                .catch(err => console.log(err));
        }

        function editMarks(rollNo) {
            openModal();
            document.getElementById('modal_roll_no').value = rollNo;
            searchStudent();
        }

        window.onclick = function(event) {
            let modal = document.getElementById('marksModal');
            if (event.target == modal) { closeModal(); }
        }
    </script>
</body>  
</html>