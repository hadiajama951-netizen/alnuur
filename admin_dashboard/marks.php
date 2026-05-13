<?php 
include('conn.php'); 

// The SQL query is updated to use 'subject_name' instead of 'name'
$query = "SELECT m.id, m.score, s.name as student_name, s.student_id, sub.subject_name 
          FROM marks m 
          INNER JOIN students s ON m.student_id = s.student_id 
          INNER JOIN subjects sub ON m.subject_id = sub.id 
          ORDER BY m.id DESC";

$result = mysqli_query($conn, $query);

// Check for errors
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}

$students = mysqli_query($conn, "SELECT student_id, name FROM students");
$subjects = mysqli_query($conn, "SELECT id, subject_name FROM subjects");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Marks</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div style="padding: 20px;">
        <h2>Student Marks</h2>
        <table border="1" width="100%" style="border-collapse: collapse;">
            <tr style="background: #1a237e; color: white;">
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Subject</th>
                <th>Score</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['student_id']; ?></td>
                <td><?php echo $row['student_name']; ?></td>
                <td><?php echo $row['subject_name']; ?></td>
                <td><?php echo $row['score']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>