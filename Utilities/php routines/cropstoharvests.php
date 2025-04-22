<?php 
// STEP 4
// gets crops from previous harvestsold and trees and puts them in crop slot 1 on new harvest table
// crops table should be newly compiled from trees table (Step 1)
// 'trees' table should be newly compiled using importcrops.php just before this to ensure that crops are found and inserted correctly
// harvests table should be newly compiled with Step 3 importharvests.php before running this script
require_once('../../Connections/piercecty.php');
require_once('../../includes/sqlcleaner.php');
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');

$inq="select ID_harvest, crop_type, weight from harvestsold, trees where harvestsold.ID_crop=trees.ID_crop";
$rsIn=mysqli_query($piercecty, $inq) or die(mysqli_error($piercecty));
while($row=mysqli_fetch_assoc($rsIn)) {
$name=strtolower(trim($row['crop_type']));
$harvest=$row['ID_harvest'];
$weight= $row['weight']>0 ? $row['weight'] : 0;

//echo $harvest.' '.$name.' '.$weight.'<br />';
$name=mysqli_real_escape_string($piercecty, $name);
$cropq="select ID_crop from crops where name='$name'";
echo $cropq."<br />";
$rsCrop=mysqli_query($piercecty,$cropq) or die(mysqli_error($piercecty));
$croprow=mysqli_fetch_assoc($rsCrop);
if($croprow) {
	$ID_crop=$croprow['ID_crop'];
	//echo $ID_crop.'<br />';
	$updateq="update harvests set crop1=$ID_crop, wgt1=$weight where ID_harvest=$harvest";
//echo $updateq.'<br />';
$rsUpdate=mysqli_query($piercecty,$updateq) or die(mysqli_error($piercecty));
} // end of if crop name was found in crops table
}
echo 'done';
?>
