<?php 
if (!isset($_SESSION)) { session_start(); }
require_once('../Connections/piercecty.php'); 
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$filedate=date('l');
$filename='../database-backups/database-'.$filedate.'.sql';
$command="/usr/bin/mysqldump  --host=$hostname_piercecty --user=$username_piercecty --password=$password_piercecty $database_piercecty > $filename --no-tablespaces";
system($command);

if (file_exists($filename)) {
header('Content-Description: File Transfer');
header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename='.basename($filename));
readfile($filename);
exit;
}
echo 'file not found';exit;
?> 
