<?php 
include('conn.php'); 

if(isset($_POST['save_attendance'])) {
    $student_id = $_POST['student_id'];
    $status = $_POST['status'];
    $date = $_POST['date'];

    $sql = "INSERT INTO attendance (student_id, status, date) VALUES ('$student_id', '$status', '$date')";
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Attendance marked successfully!');</script>";
    }
}

$students = mysqli_query($conn, "SELECT student_id, name FROM students");
?>

<div class="main-card" style="padding: 20px; background: white; border-radius: 10px;">
    <h3>Mark Attendance</h3>
    <form method="POST">
        <label>Student:</label>
        <select name="student_id" required style="width: 100%; padding: 10px; margin: 10px 0;">
            <?php while($s = mysqli_fetch_assoc($students)) echo "<option value='{$s['student_id']}'>{$s['name']}</option>"; ?>
        </select>
        
        <label>Status:</label>
        <select name="status" style="width: 100%; padding: 10px; margin: 10px 0;">
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
            <option value="Late">Late</option>
        </select>

        <label>Date:</label>
        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 10px; margin: 10px 0;">
        
        <button type="submit" name="save_attendance" style="background: #1a237e; color: white; padding: 10px 20px; border: none; cursor: pointer;">Save Attendance</button>
    </form>
</div>