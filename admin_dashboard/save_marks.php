<?php
include('conn.php');

if (isset($_POST['save_marks_btn'])) {
    // 1. Soo qaado xogta laga soo buuxiyey Form-ka
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $subject_id = mysqli_real_escape_string($conn, $_POST['subject_id']);
    $score = mysqli_real_escape_string($conn, $_POST['score']);

    // 2. Hubi haddii ardayga maaddadan dhibco hore looga diwaangeliyey (Optional)
    // Haddii aad rabto in hal arday hal mar uun maaddada dhibco looga qoro
    
    // 3. Insert query - Ku dar xogta database-ka
    $query = "INSERT INTO marks (student_id, subject_id, score) VALUES ('$student_id', '$subject_id', '$score')";
    
    if (mysqli_query($conn, $query)) {
        // Haddii ay si guul leh u badbaaddo, dib ugu celi marks.php
        header("Location: marks.php?status=success");
        exit();
    } else {
        // Haddii uu qalad dhaco
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // Haddii si toos ah loo soo galo faylkan iyadoo aan Form la soo marin
    header("Location: marks.php");
    exit();
}
?>