<?php 
// Go through all pickers and set as Inactive any that made no contact in last three years.
require_once('../../Connections/piercecty.php');
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');
// find volunteers with contactdate more than 3 years ago
$pickersq="select ID_picker, fname, lname, contactdate from pickers where  contactdate<date_sub(now(), interval 3 year)";
$rspickers=mysqli_query($piercecty,$pickersq) or die(mysqli_error($piercecty));
while($prow=mysqli_fetch_assoc($rspickers)) {
extract($prow);
if($ID_picker) echo 'picker:'.$ID_picker.' '.$fname.' '.$lname.' contactdate:'.$contactdate.', made no contact in the last three years<br />'; 
$inactiveq="update pickers set Active='No' where ID_picker=$ID_picker";
//$rsInactive=mysqli_query($piercecty, $inactiveq);
}
echo mysqli_num_rows($rspickers).' pickers set to Inactive because of no harvests in the last three years.';
?>
