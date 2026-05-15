<?php
// 1. Bilow session-ka si PHP u garto qofka raba inuu baxo
session_start();

// 2. Tirtir dhamaan xogta gudaha Session-ka ku jirtay (sida user_id iyo role)
$_SESSION = array();

// 3. Haddii nidaamku isticmaalayay Cookies, halkan ayaa lagu tirtiraa
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Gebi ahaanba burburi Session-ka
session_destroy();

// 5. Ku celi qofka bogga Login-ka ee ALNUUR
header("Location: login.php");
exit();
?>