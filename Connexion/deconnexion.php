<?php
session_start();

setcookie('pseudo','',time()-3600);
setcookie('passwordlabo','',time()-3600);
$_SESSION = array();
session_destroy();
header("Location: ../index.php");
?>