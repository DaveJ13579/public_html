<?php 
// given $zip as a string in single quotes, searches branches' lists of zips and returns matching branch, or 'unknown' if not found
function zipbranch($zip) {
$branch='Unknown';
global $piercecty;
$query="select branch, zips from branches"; 
$rsQuery=mysqli_query($piercecty,$query) or die(mysqli_error($piercecty));
while($row=mysqli_fetch_assoc($rsQuery)) {
	$zips=explode(',',$row['zips']);
//	print_r($zips).'<br />';
//	echo $zip.'<br />';
	if(in_array($zip,$zips)) { 
		$branch=$row['branch'];
		return $branch; }
		}
return $branch;
}
// look up branch name from ID_picker
function volbranch($ID_picker){
global $piercecty;
$zip='00000';
$volq="select zip from pickers where ID_picker=$ID_picker";
$rsVol=mysqli_query($piercecty,$volq);
if(mysqli_num_rows($rsVol)>0) {
	$row=mysqli_fetch_assoc($rsVol);
	$zip=$row['zip'];
	$zip="'".$zip."'";}
$branch = zipbranch($zip);
return $branch;
}

// look up branch name from ID_site
function sitebranch($ID_site){
global $piercecty;
$zip='00000';
$siteq="select zip from sites where ID_site=$ID_site";
$rsSite=mysqli_query($piercecty,$siteq);
if(mysqli_num_rows($rsSite)>0) {
	$row=mysqli_fetch_assoc($rsSite);
	$zip=$row['zip'];
	$zip="'".$zip."'";}
$branch = zipbranch($zip);
return $branch;
}
?>