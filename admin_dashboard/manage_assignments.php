<?php 
include('conn.php'); 

if(isset($_POST['save_assignment'])) {
    $student_id = $_POST['student_id'];
    $title = $_POST['title'];
    $score = $_POST['score'];
    $total = $_POST['total_marks'];

    $sql = "INSERT INTO assignments (student_id, title, score, total_marks) VALUES ('$student_id', '$title', '$score', '$total')";
    mysqli_query($conn, $sql);
}

$students = mysqli_query($conn, "SELECT student_id, name FROM students");
?>

<div class="main-card" style="padding: 20px; background: white; border-radius: 10px;">
    <h3>Give Assignment Score</h3>
    <form method="POST">
        <select name="student_id" required style="width: 100%; padding: 10px; margin: 10px 0;">
            <?php while($s = mysqli_fetch_assoc($students)) echo "<option value='{$s['student_id']}'>{$s['name']}</option>"; ?>
        </select>
        
        <input type="text" name="title" placeholder="Assignment Title (e.g. Homework 1)" required style="width: 100%; padding: 10px; margin: 10px 0;">
        <input type="number" name="score" placeholder="Earned Score" required style="width: 48%; padding: 10px;">
        <input type="number" name="total_marks" placeholder="Out of (Total)" required style="width: 48%; padding: 10px;">
        
        <button type="submit" name="save_assignment" style="background: #2e7d32; color: white; padding: 10px 20px; border: none; cursor: pointer; margin-top: 10px;">Save Assignment</button>
    </form>
</div>