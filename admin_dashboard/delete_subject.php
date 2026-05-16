<?php
session_start();
include('conn.php'); 

// Hubi in qofka soo galay uu yahay Admin ama Macallin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../login.php");
    exit();
}

// Hubi in ID-ga maaddada la soo diray
if (isset($_GET['id'])) {
    $subject_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Query-ga tirtiraya maaddada
    $delete_query = "DELETE FROM subjects WHERE id = '$subject_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        // Markay si guul leh u tirtiranto, dib ugu celi bogga maaddooyinka iyadoo farxad leh
        header("Location: subject.php?msg=success");
        exit();
    } else {
        // Haddii ay cilad dhacdo (tusaale ahaan haddii maaddada ay ku xiran tahay dhibco)
        header("Location: subject.php?msg=error&err=" . urlencode(mysqli_error($conn)));
        exit();
    }
} else {
    // Haddii si khaldan loo soo galay faylkan, u celi bogga hore
    header("Location: subject.php");
    exit();
}
?>