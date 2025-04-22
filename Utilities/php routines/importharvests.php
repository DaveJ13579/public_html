<?php 
// STEP 3
// converts downloaded current harvests table data into new harvests table
// copy good harvests table as harvests-template
// truncate and reset harvests-template
// drop harvests table
// drop harvestsold
// download and import harvests as harvests from web server. 
// Change name to 'harvestsold'. 
// Copy empty harvests-newtemplate as 'harvests'. 
// Run this script to populate the harvests table with the data from harvestsold.
// Keep harvestsold for step 4

require_once('../../Connections/piercecty.php');
require_once('../../includes/sqlcleaner.php');

$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');

$inq="select * from harvestsold";
$rsIn=mysqli_query($piercecty, $inq) or die(mysqli_error($piercecty));
while($row=mysqli_fetch_assoc($rsIn)) {
extract($row);

$ID_leader=isset($row['ID_leader']) ? $row['ID_leader'] : 0;
$ID_leader2=$row['ID_leader2']; 
$ID_site=$row['ID_crop'];
$calcwgt=$row['weight'];
$totwgt=$row['total_lbs'];
$adjwgt=$totwgt-$calcwgt;

$insertq = sprintf("INSERT INTO harvests (ID_harvest, ID_site, ID_coordinator, ID_leader, ID_leader2, h_date, h_time, calcwgt, totwgt, adjwgt, pick_num, SHT, where_to, otherinfo, longinfo, gmap, status, taxdate, summary, surveysent, KeyRec) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($ID_harvest, "int"),
                       GetSQLValueString($ID_site, "int"),
                       GetSQLValueString($ID_coordinator, "int"),
                       GetSQLValueString($ID_leader, "int"),
                       GetSQLValueString($ID_leader2, "int"),
					   GetSQLValueString($h_date, "date"),
                       GetSQLValueString($h_time, "text"),
                       GetSQLValueString($calcwgt, "int"),
                       GetSQLValueString($totwgt, "int"),
                       GetSQLValueString($adjwgt, "int"),
                       GetSQLValueString($pick_num, "int"),
                       GetSQLValueString($SHT, "text"),
                       GetSQLValueString($where_to, "text"),
                       GetSQLValueString($otherinfo, "text"),
                       GetSQLValueString($longinfo, "text"),
                       GetSQLValueString($gmap, "text"),
					   GetSQLValueString($status, "text"),
                       GetSQLValueString($taxdate, "date"),
                       GetSQLValueString($summary, "text"),
                       GetSQLValueString($surveysent, "date"),
                       GetSQLValueString($KeyRec, "int"));
// echo $insertq.'<br />';

$rsInsert=mysqli_query($piercecty,$insertq) or die(mysqli_error($piercecty));
}
echo 'done';
?>
