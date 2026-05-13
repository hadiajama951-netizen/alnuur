<?php
// save_student.php

// LINE 6: SAX - include waa inay ku jirtaa
include('conn.php'); 

if (isset($_POST['submit'])) {
    
    // LINE 11: Hadda wuu shaqaynayaa waayo $conn waa la helay
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);

    // LINE 18: Hubi inay isku xigaan (student_id marka hore, ka dib name)
    // Waxaan u habeeyay siday u kala horreeyaan database-kaaga
    $sql = "INSERT INTO students ( name,student_id, class) VALUES ( '$name','$student_id', '$class')";

    if (mysqli_query($conn, $sql)) {
        header("Location: student.php?msg=success");
        exit(); 
    } else {
        echo "Khalad ayaa dhacay: " . mysqli_error($conn);
    }
}
?>