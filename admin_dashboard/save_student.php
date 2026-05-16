<?php
// save_student.php
session_start();
include('conn.php');

if (isset($_POST['submit'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);

    // Check if Student ID already exists
    $check_id = mysqli_query($conn, "SELECT student_id FROM students WHERE student_id = '$student_id'");
    
    if (mysqli_num_rows($check_id) > 0) {
        header("Location: student.php?msg=exists");
        exit();
    } else {
        $sql = "INSERT INTO students (student_id, name, class) VALUES ('$student_id', '$name', '$class')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: student.php?msg=success");
            exit();
        } else {
            header("Location: student.php?msg=error");
            exit();
        }
    }
}
?>