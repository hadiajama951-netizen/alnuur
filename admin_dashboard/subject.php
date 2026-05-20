<?php
include('conn.php'); 
$message = "";

// 1. MEELTA LAGU REEBO AMA LAGU KEYDIYO MAADO CUSUB
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    $subject_code = mysqli_real_escape_string($conn, trim($_POST['subject_code']));
    $subject_name = mysqli_real_escape_string($conn, trim($_POST['subject_name']));
    $teacher = mysqli_real_escape_string($conn, trim($_POST['teacher']));

    if (!empty($subject_code) && !empty($subject_name)) {
        // Hubi marka hore haddii maadadu horay u jirtay (waxaan ku hubinaa magaca maadada si aan loo helin maado labo jeer dhalata)
        $check_query = mysqli_query($conn, "SELECT * FROM subjects WHERE subject_name = '$subject_name'");
        
        if (mysqli_num_rows($check_query) > 0) {
            // Haddii ay horay u jirtay, xogteeda CODE iyo TEACHER uun baa la cusboonaysiinayaa (Update)
            $sql = "UPDATE subjects SET subject_code = '$subject_code', teacher = '$teacher' WHERE subject_name = '$subject_name'";
            if (mysqli_query($conn, $sql)) {
                $message = "<div style='padding:15px; margin-bottom:20px; background:#e8f5e9; border:1px solid #2e7d32; color:#2e7d32; border-radius:5px;'>✅ Xogta maadada $subject_name si guul leh ayaa loo cusboonaysiiyey!</div>";
            }
        } else {
            // Haddii ay tahay maado ku cusub database-ka, markaas baa la dhex gelinayaa (Insert)
            $sql = "INSERT INTO subjects (subject_code, subject_name, teacher) VALUES ('$subject_code', '$subject_name', '$teacher')";
            if (mysqli_query($conn, $sql)) {
                $message = "<div style='padding:15px; margin-bottom:20px; background:#e8f5e9; border:1px solid #2e7d32; color:#2e7d32; border-radius:5px;'>✅ Maadada cusub si guul leh ayaa loo diwaangeliyey!</div>";
            } else {
                $message = "<div style='padding:15px; margin-bottom:20px; background:#ffebee; border:1px solid #c62828; color:#c62828; border-radius:5px;'>❌ Khalad: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}

// 2. MEELTA LAGU TIRTIRAYO MAADADA (DELETE FUNCTION)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (mysqli_query($conn, "DELETE FROM subjects WHERE id = $id")) {
        header("Location: subject.php");
        exit;
    }
}

// 3. SOO AKHRI LISKA MADOOMINKA EE DATABASE-KA KU JIRA
$saved_subjects = [];
if ($conn) {
    $result = mysqli_query($conn, "SELECT * FROM subjects");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Waxaan u habaynaynaa qaab maaddo walba magaceeda yar lagu garto
            $key = strtolower($row['subject_name']);
            $saved_subjects[$key] = $row;
        }
    }
}

// Liiska rasmiga ah ee 10-ka Maado ee nidaamka marks-ka ku dhex jira
$fixed_subjects = [
    'mathematics' => 'Mathematics',
    'english'     => 'English',
    'science'     => 'Science',
    'somali'      => 'Somali',
    'history'     => 'History',
    'geography'   => 'Geography',
    'arabic'      => 'Arabic',
    'islamic'     => 'Islamic',
    'chemistry'   => 'Chemistry',
    'physics'     => 'Physics'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMS - Manage Subjects</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .subject-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
            margin-top: 10px;
        }

        .subject-table thead tr {
            background-color: #1a237e; 
            color: white; 
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
            cursor: pointer;
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
        .panel {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
        }
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="brand">SPMS</div>
            <nav class="side-nav">
                <a class="nav-item" href="admin_dashboard.php">🏠 Dashboard</a>
                <a class="nav-item" href="students.php">👤 Students</a>
                <a class="nav-item active" href="subject.php">📚 Subjects</a>
                <a class="nav-item" href="add_marks.php">📝 Marks</a>
                <a class="nav-item" href="report.php">📊 Reports</a>
                <a class="nav-item" href="user.php">⚙️ Users</a>
                <a class="nav-item" href="logout.php" style="margin-top: 50px;">🚪 Logout</a>
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
                
                <?php echo $message; ?>

                <div class="grid-2">
                    <div class="panel">
                        <h4 style="border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">Add / Edit Subject</h4>
                        <form action="" method="POST" id="subjectForm">
                            
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Subject Name</label>
                                <select name="subject_name" id="subject_name" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; box-sizing: border-box;">
                                    <option value="">-- Dooro Maadada --</option>
                                    <?php foreach ($fixed_subjects as $slug => $real_name): ?>
                                        <option value="<?php echo $real_name; ?>"><?php echo $real_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Subject Code</label>
                                <input type="text" name="subject_code" id="subject_code" placeholder="e.g. MATH101" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; box-sizing: border-box;">
                            </div>

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Teacher</label>
                                <select name="teacher" id="teacher" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; box-sizing: border-box;">
                                    <option value="Axmed">Axmed</option>
                                    <option value="Ali">Ali</option>
                                    <option value="Husein">Husein</option>
                                </select>
                            </div>
                            
                            <button type="submit" name="save" class="btn-save">Register Subject</button>
                        </form>
                    </div>

                    <div class="panel">
                        <h4 style="border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">Subject List (10 active fields)</h4>
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
                                <?php foreach ($fixed_subjects as $slug => $real_name): ?>
                                    <?php 
                                        // Haddii maadadan database-ka laga helo xogteeda soo saar
                                        if (isset($saved_subjects[$slug])) {
                                            $code = htmlspecialchars($saved_subjects[$slug]['subject_code']);
                                            $teacher = htmlspecialchars($saved_subjects[$slug]['teacher']);
                                            $db_id = $saved_subjects[$slug]['id'];
                                            
                                            $action_buttons = '
                                                <a class="btn-edit" onclick="editSubject(\''.$real_name.'\', \''.$code.'\', \''.$teacher.'\')">Edit</a>
                                                <a href="subject.php?delete='.$db_id.'" class="btn-delete" onclick="return confirm(\'Ma hubaal inaad tirtirto maadadan?\')">Delete</a>';
                                        } else {
                                            // Haddii aan weydano database-ka, miiska ha madnaado laakiin magaca maadadu ha muuqdo
                                            $code = "<span style='color:#ccc; font-style:italic;'>Not Set</span>";
                                            $teacher = "<span style='color:#ccc; font-style:italic;'>No Teacher</span>";
                                            $action_buttons = "<span style='color:#aaa; font-size:12px;'>Not Configured</span>";
                                        }
                                    ?>
                                    <tr>
                                        <td style="font-weight: bold; color: #1a237e;"><?php echo $code; ?></td>
                                        <td style="font-weight: 600; text-transform: uppercase;"><?php echo $real_name; ?></td>
                                        <td><?php echo $teacher; ?></td>
                                        <td>
                                            <div class="action-btns">
                                                <?php echo $action_buttons; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Markaad "Edit" gujiso, xogta toos foomka ugu shub si loo update-gareeyo
        function editSubject(name, code, teacher) {
            document.getElementById('subject_name').value = name;
            document.getElementById('subject_code').value = code;
            document.getElementById('teacher').value = teacher;
        }
    </script>
</body>
</html>