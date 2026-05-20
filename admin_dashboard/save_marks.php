<?php
include('conn.php');

if(isset($_POST['save_marks'])) {
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $full_marks = $_POST['full_marks'];
    $marks = $_POST['marks_obtained'];
    $term = $_POST['term'];
    $remark = $_POST['remark'];

    // Logic yar oo Grade-ka lagu xisaabiyo
    if ($marks >= 90) $grade = "A+";
    elseif ($marks >= 80) $grade = "A";
    elseif ($marks >= 70) $grade = "B";
    elseif ($marks >= 60) $grade = "C";
    elseif ($marks >= 50) $grade = "D";
   
    $query = "INSERT INTO marks (student_id, subject_id, full_marks, marks_obtained, grade, term, remark) 
              VALUES ('$student_id', '$subject_id', '$full_marks', '$marks', '$grade', '$term', '$remark')";

    if(mysqli_query($conn, $query)) {
        echo "<script>alert('Dhibcaha waa la kaydiyey!'); window.location='add_marks.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>