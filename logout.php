<?PHP session_start();

unset($_SESSION["connected"]);
unset($_SESSION["username"]);
unset($_SESSION["uid"]);

header("Location: ./index.php");
exit();