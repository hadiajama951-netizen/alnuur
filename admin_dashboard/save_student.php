<?php
// save_student.php
session_start();

// LINE 6: Hubi in faylka xiriirka database-ka uu sax yahay
include('conn.php');

// Hubi in qofka soo galaya uu yahay Admin ama Teacher oo kaliya
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['submit'])) {

    // LINE 11: Soo qabo xogta laga soo diray foomka (Form-ka)
    $name       = mysqli_real_escape_string($conn, $_POST['name']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']); // Aqoonsiga ardayga (e.g. ID ama Email)
    $class      = mysqli_real_escape_string($conn, $_POST['class']);

    // LINE 18: Isku diyaari gelinta xogta miiska students
    // Waxaan u habeyney siday u kala horreeyaan khaanadaha database-kaaga sxb
    $sql = "INSERT INTO students (name, student_id, class) VALUES ('$name', '$student_id', '$class')";

    if (mysqli_query($conn, $sql)) {
        // Markay si guul leh u kaydsanto, u wareeji bogga student.php isagoo wata fariin guul ah
        header("Location: student.php?msg=success");
        exit();
    } else {
        // Haddii ay ciladi dhacdo, muuji fariinta rasmiga ah ee MySQL
        echo "Khalad ayaa dhacay sxb: " . mysqli_error($conn);
    }
} else {
    // Haddii qofku si toos ah u soo booqdo faylkan isagoo foomka soo riixin, dib ugu celi bogga hore
    header("Location: student.php");
    exit();
}
?>