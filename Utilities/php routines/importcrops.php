<?php 
// STEP 2 (may not be needed if no new crops in trees table)
// cleans and inserts all crop_types from newly downloaded trees table into crops table
// Run Step 1 to have the latest trees table
// truncate and reset the crops table.
require_once('../../Connections/piercecty.php');
require_once('../../includes/sqlcleaner.php');
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');

$inq="select distinct(crop_type) from trees where crop_type is not NULL order by lower(crop_type) ";
echo $inq.'<br />';
$rsIn=mysqli_query($piercecty, $inq) or die(mysqli_error($piercecty));
while($row=mysqli_fetch_assoc($rsIn)) {
$name=strtolower(trim($row['crop_type']));
echo "name:".$name.'---<br />';
$insertq=sprintf("insert into crops (name) values (%s)", GetSQLValueString($name,"text"));
$rsInsert=mysqli_query($piercecty,$insertq) or die(mysqli_error($piercecty));
}
echo 'done';
?>
