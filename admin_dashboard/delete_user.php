<?php
session_start();
include('conn.php'); 

// Hubi in qofka soo galay uu yahay Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // 1. Tirtir dhibcihii ardayga u diwaangashnaa haddii ay jiraan
    mysqli_query($conn, "DELETE FROM marks WHERE student_id = '$user_id'");

    // 2. Tirtir akoonka qofka asaga ah
    $delete_query = "DELETE FROM users WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        header("Location: add_user.php?status=deleted");
        exit();
    } else {
        header("Location: add_user.php?status=error&msg=" . urlencode(mysqli_error($conn)));
        exit();
    }
} else {
    header("Location: add_user.php");
    exit();
}
?>