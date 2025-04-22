<?php
require_once('../Connections/piercecty.php');
if (!isset($_SESSION)) {  session_start(); }
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../includes/sqlcleaner.php');

if(isset($_GET['old']) && isset($_GET['new'])) { 
$old = $_GET['old'];
$new = $_GET['new'];}
else { die("no new and old inputs"); }

// get array of new data

$querynew="SELECT * from pickers where ID_picker='$new'";
$rsNew = mysqli_query($piercecty, $querynew) or die(mysqli_error($piercecty));
$rownew = mysqli_fetch_assoc($rsNew);

// get array of field names
$queryfields = "Describe pickers";
$rsFields = mysqli_query($piercecty, $queryfields) or die(mysqli_error($piercecty));
$numfields = mysqli_num_rows($rsFields);
$rowfields = mysqli_fetch_assoc($rsFields); // pull a row to skip over Picker number field

// build update query string from new data and fields names
$update="UPDATE pickers set ";
for($i=2 ; $i<=$numfields ; $i++) { 
	$rowfields = mysqli_fetch_assoc($rsFields);
	$field=$rowfields['Field'];
	if($rownew["$field"]<>'') { $update.=$field."=".GetSQLValueString($rownew["$field"],"text").", "; }
	}
$update=substr($update,0,-2); // chop off extra space and comma
$update.=" WHERE ID_picker=".$old;

// execute update
$rsUpdate=mysqli_query($piercecty, $update) or die(mysqli_error($piercecty));
// delete discarded picker
$querydelete="DELETE from pickers WHERE ID_picker=$new";
$rsDelete=mysqli_query($piercecty, $querydelete) or die(mysqli_error($piercecty));
// change roster entries
$queryroster="UPDATE rosters set ID_picker=$old WHERE ID_picker=$new";
$rsRoster=mysqli_query($piercecty, $queryroster) or die(mysqli_error($piercecty));
// return to duplicates
header('Location: duplicates.php');
exit;
?>

