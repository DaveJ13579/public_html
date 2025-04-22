<?php 
// Go through all harvests with leaders, delete roster entries with the leader and add the leader to the roster as 'leader'
echo 'Comment line 4 to run';
// $exit; 
require_once('../../Connections/piercecty.php');
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');

$ldrq="select ID_harvest, h_date, ID_leader from harvests where ID_leader<>0";
$rsLdr=mysqli_query($piercecty, $ldrq) or die(mysqli_error($piercecty));
while($row=mysqli_fetch_assoc($rsLdr)) {
 extract($row);
 echo $ID_harvest.' '.$ID_leader.'<br />';
 $deleteq="delete from rosters where rosters.ID_picker=$ID_leader and ID_harvest=$ID_harvest";
 $RsDelete=mysqli_query($piercecty, $deleteq);
 $insertq="insert into rosters (ID_picker, ID_harvest, regdate, status) values ($ID_leader, $ID_harvest, '$h_date', 'leader')";
echo $insertq.'<br />';
$rsInsert=mysqli_query($piercecty, $insertq) or die(mysqli_error($piercecty));
}
echo 'done';
?>
