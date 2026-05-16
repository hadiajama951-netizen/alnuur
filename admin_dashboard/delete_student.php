<?php
session_start();
include('conn.php'); 

// Hubi in qofka soo galay uu yahay Admin ama Macallin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

// Hubi in ID-ga ardayga la soo diray
if (isset($_GET['id'])) {
    $student_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // 1. Marka hore soo qabo xogta qofka si loo hubiyo inuu arday yahay
    $check_user = mysqli_query($conn, "SELECT role FROM users WHERE id = '$student_id'");
    $user_data = mysqli_fetch_assoc($check_user);

    if ($user_data && $user_data['role'] === 'student') {
        // 2. Tirtir dhibcihii ardayga u diwaangashnaa marka hore (maadaama ay ku xiran yihiin Foreign Key)
        mysqli_query($conn, "DELETE FROM marks WHERE student_id = '$student_id'");

        // 3. Hadda tirtir ardayga laftiisa miiska users
        $delete_query = "DELETE FROM users WHERE id = '$student_id' AND role = 'student'";
        
        if (mysqli_query($conn, $delete_query)) {
            header("Location: student.php?msg=deleted");
            exit();
        } else {
            header("Location: student.php?msg=error&err=" . urlencode(mysqli_error($conn)));
            exit();
        }
    } else {
        // Haddii ardayda miis kale oo la yiraahdo 'students' lagu kaydiyo
        mysqli_query($conn, "DELETE FROM marks WHERE student_id = '$student_id'");
        $delete_query = "DELETE FROM students WHERE id = '$student_id'";
        
        if (mysqli_query($conn, $delete_query)) {
            header("Location: student.php?msg=deleted");
            exit();
        } else {
            header("Location: student.php?msg=error&err=" . urlencode(mysqli_error($conn)));
            exit();
        }
    }
} else {
    header("Location: student.php");
    exit();
}
?>