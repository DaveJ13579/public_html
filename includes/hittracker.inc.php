<?php
// hittracker.inc.php
// 'included' into a php page, it inserts a record into database table 'hits'
// be sure that the Connection to database is made in the script before this includes is called
if (!isset($_SESSION)) {   session_start(); }
// date_default_timezone_set('America/Los_Angeles');
$page=$_SERVER['PHP_SELF'];
$file=strrchr($page,"/");
$extlength=strlen(strrchr($file,"."));
$page=substr($file,1,strlen($file)-$extlength-1);
$IP = $_SERVER['REMOTE_ADDR'];
$when= date('Y-m-d H:i:s');


$insertsql = "INSERT INTO hits (whenhit,page,IPaddress) VALUES ('$when','$page','$IP')";
$result = mysqli_query($piercecty, $insertsql) or die (mysqli_error($piercecty));
?>