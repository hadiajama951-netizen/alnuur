<?php
session_start();
include('conn.php'); 

// Hubi in qofka soo galay uu yahay Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Marka la gujiyo Save Subject
if (isset($_POST['add_subject'])) {
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $subject_code = mysqli_real_escape_string($conn, $_POST['subject_code']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);

    // Hubi haddii ay maaddadan ama koodhkan horay u jireen
    $check_subject = mysqli_query($conn, "SELECT * FROM subjects WHERE subject_code='$subject_code'");
    if (mysqli_num_rows($check_subject) > 0) {
        $error = "Subject Code-kan horay ayaa loo diwaangeliyey sxb!";
    } else {
        // Ku shub database-ka
        $insert_query = "INSERT INTO subjects (subject_name, subject_code, class) VALUES ('$subject_name', '$subject_code', '$class')";
        if (mysqli_query($conn, $insert_query)) {
            // Markay si guul leh u kaydsanto, u weeci bogga weyn ee maaddooyinka
            header("Location: subject.php");
            exit();
        } else {
            $error = "Cilad ayaa dhacday: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Add New Subject | Alnuur School</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 40px 10px; display: flex; justify-content: center; align-items: center; min-height: 100vh; box-sizing: border-box;">

<div style="background: #ffffff; padding: 35px; border-radius: 10px; box-shadow: 0 4px 25px rgba(0,0,0,0.15); width: 100%; max-width: 480px; box-sizing: border-box;">
    
    <h2 style="text-align: center; color: #1a237e; margin-top: 0; margin-bottom: 25px; font-size: 24px;">Register New Subject</h2>
    
    <?php if(isset($error)) { echo '<p style="color:red; text-align:center; font-weight:bold; margin-bottom:20px;">'.$error.'</p>'; } ?>
    
    <form action="add_subject.php" method="POST">
        
        <div style="margin-bottom: 18px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333; font-size: 14px;">Subject Name:</label>
            <input type="text" name="subject_name" placeholder="E.g., Java Programming" required style="width: 100%; padding: 12px; border: 1px solid #bbb; border-radius: 6px; box-sizing: border-box; font-size: 14px;">
        </div>
        
        <div style="margin-bottom: 18px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333; font-size: 14px;">Subject Code:</label>
            <input type="text" name="subject_code" placeholder="E.g., JAVA-301" required style="width: 100%; padding: 12px; border: 1px solid #bbb; border-radius: 6px; box-sizing: border-box; font-size: 14px;">
        </div>
        
        <div style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333; font-size: 14px;">Assigned Class / Semester:</label>
            <select name="class" required style="width: 100%; padding: 12px; border: 1px solid #bbb; border-radius: 6px; box-sizing: border-box; font-size: 14px; background-color: #fff;">
                <option value="">-- Select Class --</option>
                <option value="Semester 1">Semester 1</option>
                <option value="Semester 2">Semester 2</option>
                <option value="Semester 3">Semester 3</option>
                <option value="Semester 4">Semester 4</option>
            </select>
        </div>
        
        <button type="submit" name="add_subject" style="background: #2196f3; color: #ffffff; padding: 14px; border: none; border-radius: 6px; width: 100%; font-weight: bold; cursor: pointer; font-size: 16px; display: block; text-align: center; box-shadow: 0 3px 6px rgba(0,0,0,0.1);">Save Subject</button>
        
        <a href="subject.php" style="display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #1a237e; font-size: 14px; font-weight: bold;">← Back to Subjects Directory</a>
    </form>
</div>

</body>
</html>