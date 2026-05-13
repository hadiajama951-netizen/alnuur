<?php
include('conn.php');

if (isset($_POST['save_btn'])) {
    // Ka qaado xogta form-ka
    $code = mysqli_real_escape_string($conn, $_POST['subject_code']);
    $name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $teacher = mysqli_real_escape_string($conn, $_POST['teacher']);

    // SQL Query
    $sql = "INSERT INTO subjects (subject_code, subject_name, teacher_name) 
            VALUES ('$code', '$name', '$teacher')";

    if (mysqli_query($conn, $sql)) {
        // Markay guulaysato, wuxuu kugu soo celinayaa bogga subject.php
        header("Location: subject.php?msg=success");
        exit();
    } else {
        echo "Cillad: " . mysqli_error($conn);
    }
}
?>