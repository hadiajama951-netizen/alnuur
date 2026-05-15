<?php
session_start();
session_unset();
session_destroy();

// Markuu qofku Logout dhaho, wuxuu ku noqonayaa bogga Login-ka
header("Location: ../login.php");
exit();
?>