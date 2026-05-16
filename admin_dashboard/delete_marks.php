<?php
session_start();
include('conn.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $delete_sql = "DELETE FROM marks WHERE id = '$id'";
    mysqli_query($conn, $delete_sql);
}

header("Location: marks.php");
exit();
?>