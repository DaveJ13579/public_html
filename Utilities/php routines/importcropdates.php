<?php 
require_once('../../Connections/piercecty.php');
require_once('../../includes/sqlcleaner.php');

$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');

$inq="select ID_crop, regdate from trees";
$rsIn=mysqli_query($piercecty, $inq) or die(mysqli_error($piercecty));
while($row=mysqli_fetch_assoc($rsIn)) {

$ID_crop=$row['ID_crop'];
$regdate=$row['regdate'];
$update="update sites set regdate='$regdate' where ID_site=$ID_crop";
echo '<br />'.$ID_crop.' '.$regdate.' '.$update;

$rsUpdate=mysqli_query($piercecty,$update) or die(mysqli_error($piercecty));
}
echo 'done';
?>
