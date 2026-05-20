<?php
include('conn.php');

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $student_id = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    
    // Password-ka koowaad ee ardayga wuxuu noqonayaa ID-giisa
    $default_password = password_hash($student_id, PASSWORD_BCRYPT);
    $email = strtolower(str_replace(' ', '', $name)) . "@school.com"; // Email male-awaal ah

    // Hubi in ninkani hore u diiwaangashnaa
    $check = mysqli_query($conn, "SELECT id FROM students WHERE student_id = '$student_id'");
    
    if (mysqli_num_rows($check) > 0) {
        header("Location: students.php?error=Ardaygan hore ayaa loo diiwaangeliyey!");
        exit();
    } else {
        // Ka billow gudaha Database-ka dhowr isbeddel oo isku xidhan
        mysqli_begin_transaction($conn);

        try {
            // 1. Ku dhar shaxda Students
            mysqli_query($conn, "INSERT INTO students (student_id, name, class) VALUES ('$student_id', '$name', '$class')");

            // 2. Ku dar shaxda Users (Si uu nidaamka u galo)
            mysqli_query($conn, "INSERT INTO users (full_name, username, email, password, role, status) 
                                 VALUES ('$name', '$student_id', '$email', '$default_password', 'User', 'Active')");

            // 3. U diyaari safka dhibcaha (student_marks)
            mysqli_query($conn, "INSERT INTO student_marks (roll_no, full_name, class, math, english, science, somali, history, geography, arabic, islamic, chemistry, physics) 
                                 VALUES ('$student_id', '$name', '$class', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)");

            mysqli_commit($conn);
            header("Location: students.php?success=Ardayga si guul leh ayaa loo kaydiyey 3da dhinacba!");
            exit();

        } catch (Exception $e) {
            mysqli_rollback($conn);
            header("Location: students.php?error=Khalad ayaa dhacay: " . mysqli_error($conn));
            exit();
        }
    }
}
?>