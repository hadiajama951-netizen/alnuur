<?php
include('conn.php');
session_start();

// Handle Form Submission
if (isset($_POST['add_marks'])) {
   // THE CORRECT LINE:
$student_email = mysqli_real_escape_string($conn, $_POST['student_email']);
    $subject_id = $_POST['subject_id'];
    $credit_hours = $_POST['credit_hours'];
    $attendance = $_POST['attendance'];
    $assignment = $_POST['assignment'];
    $mid_exam = $_POST['mid_exam'];
    $final_exam = $_POST['final_exam'];

    // Search for student ID using only the email column
    $user_query = "SELECT id FROM users WHERE email='$student_email' LIMIT 1";
    $user_check = mysqli_query($conn, $user_query);
    
    if ($user_check && mysqli_num_rows($user_check) > 0) {
        $user_row = mysqli_fetch_assoc($user_check);
        $student_id = $user_row['id'];

        // Insert into marks table with all 8 categories
        $sql = "INSERT INTO marks (student_id, subject_id, credit_hours, attendance, assignment, mid_exam, final_exam) 
                VALUES ('$student_id', '$subject_id', '$credit_hours', '$attendance', '$assignment', '$mid_exam', '$final_exam')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Results saved successfully for $student_email!'); window.location.href='marks.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Error: Email [$student_email] not found. Please check the email in your Users list.');</script>";
    }
}

// Fetch all marks for the table at the bottom
$view_query = "SELECT m.*, u.email as student_email, s.subject_name 
               FROM marks m 
               JOIN users u ON m.student_id = u.id 
               JOIN subjects s ON m.subject_id = s.id 
               ORDER BY m.id DESC";
$view_result = mysqli_query($conn, $view_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alnuur Admin | Manage Marks</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; }
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 1000px; margin: auto; }
        h2 { color: #1a237e; border-bottom: 2px solid #1a237e; padding-bottom: 10px; margin-bottom: 25px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .input-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 8px; color: #333; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        .btn-save { background: #1a237e; color: white; padding: 15px 40px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; width: 100%; margin-top: 20px; transition: 0.3s; }
        .btn-save:hover { background: #0d1440; }
        
        /* Table Styles */
        .table-container { margin-top: 50px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f1f3f4; color: #1a237e; padding: 12px; border: 1px solid #dee2e6; text-align: left; }
        td { padding: 12px; border: 1px solid #dee2e6; font-size: 14px; color: #444; }
        .total-bold { font-weight: bold; color: #1a237e; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Gali Natiijada Cusub (Add Student Marks)</h2>
    <form method="POST">
        <div class="form-grid">
            <div class="input-group">
                <label>Student Email Address:</label>
                <input type="text" name="student_email" placeholder="e.g. student@alnuur.com" required>
            </div>

            <div class="input-group">
                <label>Maaddada (Subject):</label>
                <select name="subject_id" required>
                    <option value="">-- Door Maaddada --</option>
                    <?php 
                    $subs = mysqli_query($conn, "SELECT * FROM subjects");
                    while($sb = mysqli_fetch_assoc($subs)) {
                        echo "<option value='".$sb['id']."'>".$sb['subject_name']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="input-group">
                <label>Credit Hours:</label>
                <input type="number" name="credit_hours" value="3" required>
            </div>

            <div class="input-group">
                <label>Attendance (10):</label>
                <input type="number" step="0.01" name="attendance" placeholder="Gali dhibcaha" required>
            </div>

            <div class="input-group">
                <label>Assignment (10):</label>
                <input type="number" step="0.01" name="assignment" placeholder="Gali dhibcaha" required>
            </div>

            <div class="input-group">
                <label>Mid Exam (30):</label>
                <input type="number" step="0.01" name="mid_exam" placeholder="Gali dhibcaha" required>
            </div>

            <div class="input-group">
                <label>Final Exam (50):</label>
                <input type="number" step="0.01" name="final_exam" placeholder="Gali dhibcaha" required>
            </div>
        </div>

        <button type="submit" name="add_marks" class="btn-save">Keydi Natiijada</button>
    </form>

    <div class="table-container">
        <h2>Dhibcihii Ugu Dambeeyay (Recent Marks)</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Email</th>
                    <th>Subject</th>
                    <th>Mid</th>
                    <th>Final</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($view_result)): ?>
                <tr>
                    <td><?php echo $row['student_email']; ?></td>
                    <td><?php echo $row['subject_name']; ?></td>
                    <td><?php echo $row['mid_exam']; ?></td>
                    <td><?php echo $row['final_exam']; ?></td>
                    <td class="total-bold"><?php echo $row['total_marks']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>