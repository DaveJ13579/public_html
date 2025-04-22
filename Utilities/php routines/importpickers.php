<?php 
// STEP 6
// converts downloaded current pickers table data into new pickers table
// rename good pickers table as pickers-template
// truncate and reset pickers-template
// drop pickersold
// download and import pickers as pickers from web server. 
// Change name to 'pickersold'. 
// delete record 0 Unregistered
// Copy empty pickers-newtemplate as 'pickers'. 
// Run this script to populate the pickers table with the data from pickersold.
// insert 0 unregistered into pickers
// delete columns ladderdate and ladderscore
require_once('../../Connections/piercecty.php');
require_once('../../includes/sqlcleaner.php');

$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');

$inq="select * from pickersold";
$rsIn=mysqli_query($piercecty, $inq) or die(mysqli_error($piercecty));
while($row=mysqli_fetch_assoc($rsIn)) {
extract($row);
echo $ID_picker.' '.$lname.'<br />';
$insertq = sprintf("INSERT INTO pickers (ID_picker, lname, fname, phone, phone2, email, address, city, state, zip, assistance, dupname, harvester, leader, how_hear, other_info, regdate, contactdate, waive_date, latitude, longitude, IP_picker, selectteam, emerg, ephone) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($ID_picker, "int"),
                       GetSQLValueString($lname, "text"),
                       GetSQLValueString($fname, "text"),
                       GetSQLValueString($phone, "text"),
                       GetSQLValueString($phone2, "text"),
					   GetSQLValueString($email, "text"),
                       GetSQLValueString($address, "text"),
                       GetSQLValueString($city, "text"),
                       GetSQLValueString($state, "text"),
                       GetSQLValueString($zip, "text"),
                       GetSQLValueString($foodassist, "text"),
                       GetSQLValueString($dupname, "text"),
                       GetSQLValueString($harvester, "text"),
                       GetSQLValueString($leader, "text"),
                       GetSQLValueString($how_hear, "text"),
                       GetSQLValueString($otherhelp, "text"),
                       GetSQLValueString($regdate, "date"),
                       GetSQLValueString($contactdate, "date"),
                       GetSQLValueString($waive_date, "date"),
                       GetSQLValueString($latitude, "text"),
                       GetSQLValueString($longitude, "text"),
                       GetSQLValueString($IP_picker, "text"),
                       GetSQLValueString($selectteam, "text"),
                       GetSQLValueString($emerg, "text"),
                       GetSQLValueString($ephone, "text"));
// echo $insertq.'<br />';

$rsInsert=mysqli_query($piercecty,$insertq) or die(mysqli_error($piercecty));
}
echo 'done';
?>
