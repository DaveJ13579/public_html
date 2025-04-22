<?php
// get custom text to insert into page
$cusquery="select pagetext from custom2 where pagename='$customfield'";
// echo $cusquery;
$rsCustom=mysqli_query($piercecty,$cusquery) or die(mysqli_error($piercecty));
$textrow=mysqli_fetch_assoc($rsCustom);
$pagetext=$textrow['pagetext'];

// set edit/show switch
if(	isset($_GET['ed']) && $_GET['ed']=='edit' 
	&&  isset($_SESSION['MM_UserGroup']) && $_SESSION['MM_UserGroup'] == 'all') 
		{$swt='edit'; $butswitch='show'; } 
		else {$swt='show'; $butswitch='edit';}
$here= $_SERVER['PHP_SELF'];

// if edited, update text in database 
if((isset($_POST["submit"])) && ($_POST["submit"] == "Save Update")) {
$updateSQL = sprintf("UPDATE custom2 set pagetext=%s where pagename='$customfield'", GetSQLValueString($_POST['pagetxt'], "text"));
$Result1 = mysqli_query( $piercecty, $updateSQL) or die(mysqli_error($piercecty));
$updateGoTo =$here.'?ed=show';
header(sprintf("Location: %s", $updateGoTo));
}
?>