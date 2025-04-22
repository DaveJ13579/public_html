<?php 
// Go through all sites and set as Inactive any that were not harvested in last two years and were registered before two years ago.
// echo 'Comment out line 5 to run<br />';
// $exit; 
require_once('../../Connections/piercecty.php');
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');

// find active sites registered more than 2 years ago
$sitesq="select sites.ID_site as ID_site, regdate from sites where regdate<date_sub(now(), interval 2 year) and Active<>'No'";
$rsSites=mysqli_query($piercecty, $sitesq) or die(mysqli_error($piercecty));
echo mysqli_num_rows($rsSites).' active sites registered before  2 years ago<br />'; 

$ct=0;
while($row=mysqli_fetch_assoc($rsSites)) {
	extract($row);
$harvestsq="select count(ID_harvest) as harvests from harvests where ID_site=$ID_site and h_date>date_sub(now(), interval 2 year)";
$rsHarvests=mysqli_query($piercecty,$harvestsq) or die(mysqli_error($piercecty));
$hrow=mysqli_fetch_assoc($rsHarvests);
extract($hrow);
if(!$harvests) echo 'site:'.$ID_site.', regdate:'.$regdate.', had no harvests in the last two years<br />'; 
// set as Inactive those with no harvests
	$inactiveq="update sites set Active='No' where $harvests=0 and ID_site=$ID_site";
//	echo $inactiveq.'<br />';
$rsInactive=mysqli_query($piercecty, $inactiveq);
	$ct+= $harvests ? 0 : 1;
}

echo $ct.' sites set to Inactive because of no harvests in the last two years.';
?>
