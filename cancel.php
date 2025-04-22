<?php 
require_once('Connections/piercecty.php');
require_once('includes/sqlcleaner.php'); 
require_once('includes/dencode.inc.php'); 
require_once('includes/fixroster.inc.php'); 
require_once('includes/emailer.inc.php'); 
require_once('includes/smtpmailer.inc.php');

$ID='';$harvpost=''; $confirmed='no';
if(isset($_GET['ID'])) $ID=decode($_GET['ID']); 
if(isset($_GET['h'])) $harvpost=decode($_GET['h']); 

if(isset($_POST['close'])) { $home='http://www.piercecountygleaningproject.org'; header("Location: $home"); exit(); }

if(isset($_POST['confirm'])) { // confirm button is clicked 

$confirmed='yes';
$err=""; $numrows1=0; $numrows2=0; $status='';

// who is it?
$IP=$_SERVER['REMOTE_ADDR'];
$ID=0;
if(isset($_POST['ID'])) $ID=($_POST['ID']); 
	
if($ID<1 || $ID>99999) $err="Decoded ID not found"; 

if($err=="") {
	$whoquery="Select fname, lname, ID_picker, email from pickers where ID_picker=$ID";
	$whoresult=mysqli_query($piercecty, $whoquery);
	$numrows1=mysqli_num_rows($whoresult);
	if($numrows1==0) { $err="picker not found"; } 
	else { $whorow=mysqli_fetch_assoc($whoresult) or die(mysqli_error($piercecty)); }
} // end of if $err==""
 
// which harvest?
$harvest=0;
if($err=="") {
if(isset($_POST['harvpost'])) { $harvest=($_POST['harvpost']); }
if($harvest==0) {$err="Harvest ID not found";}
} // end of err==""

if($err=="") {
	$harvquery="Select h_time, h_date from harvests where ID_harvest=$harvest";
	$harvresult=mysqli_query($piercecty, $harvquery) or die(mysqli_error($piercecty));
	$numrows2=mysqli_num_rows($harvresult);
	if($numrows2==0) { $err="harvest not found";}
	else { 	$harvrow=mysqli_fetch_assoc($harvresult) or die(mysqli_error($piercecty)); }
} // end of err=="" 

// is he or she on the roster?
if($err=="") {
$query = "SELECT status FROM rosters WHERE ID_picker=$ID and ID_harvest=$harvest";
$rsCancel=mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$cancelrow = mysqli_fetch_assoc($rsCancel);
$status=$cancelrow['status'];
$numrows3=mysqli_num_rows($rsCancel);
if($numrows3==0) { $err= $whorow['fname']." ".$whorow['lname']." is not on the roster for harvest ".$harvest; }
} // end of if err==""

// is the harvest already over?
if($err=="") {
if($harvrow['h_date']<date("Y-m-d") || ($harvrow['h_date']==date("Y-m-d") && substr($harvrow['h_time'],0,5)<date("H:i"))) {$err=$whorow['fname']." ".$whorow['lname']." is canceling for a past harvest ".substr($harvrow['h_time'],0,5)." ".date("H:i"); }
} // end of if err==""

// cancel the roster status if no errors
if($err=="")  { 

if($status<>'waiting' and $status<>'cancel') { // change status to cancel 
$regdate=date("Y-m-d H:i:s");
$updateSQL="UPDATE rosters set status='cancel', regdate='$regdate' where ID_harvest=$harvest and ID_picker=$ID";
$Updated=mysqli_query($piercecty, $updateSQL) or die(mysqli_error($piercecty)); 

} // end of change status to cancel

if($status=='waiting') { // status is waiting
$deleteSQL="delete from rosters where ID_harvest=$harvest and ID_picker=$ID and status='waiting'";
$Deleted=mysqli_query($piercecty, $deleteSQL) or die(mysqli_error($piercecty));
} // end of delete waiting status

fixroster($harvest, $piercecty);

} // end of if err=="" so update
// send an email with the error details

$picker = $numrows1==0 ?  "picker not found" : $whorow['fname']." ".$whorow['lname'].", ID=".$whorow['ID_picker'].", ".$whorow['email'];
$which = $numrows2==0 ? "harvest not found" : $harvest." ".$harvrow['h_date'];
if($err<>"") 	{ // send webmaster an email only if not normal cancel
$email = 'piercecty@gleanweb.org';
	$subject = "Roster cancellation: ".$picker.", ".$which;
	$message = "REQUESTED: ".$picker.", harvest=".$harvest.", status=".$status;
	$message.="\n"."ERROR: ".$err;
		if(isset($_GET['ID'])) {$getid=$_GET['ID']; } else {$getid='no GET ID';}
		if(isset($_GET['h'])) {$geth=$_GET['h']; } else {$geth='no GET h';}
	$message.= "\n"."GET[ID]: ".$getid." GET[h] = ".$geth; 
	$message.="\n"."IP ADDRESS: ".$IP; 
	$rosterurl="http://www.piercecountygleaningproject.org/Utilities/rosterupdate.php?nametemp=&pickertemp=&harvesttemp=".$harvest."&statustemp=&submit=Show+records&MM_filter=filtersform";
	$message.="\n\n"."View the roster for this harvest:"."\n".$rosterurl;
	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");
} // end of $err<>'';
} // end of if confirm button is clicked
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cancel signup</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
tr { 	background-color: #d2e2f7; }
th { 	background-color: #b2c2d7; } 
-->
</style>
</head>
<body class="SH">
<div id="container">
  <div id="header">
   <img src="images/banners/banner-home.jpg" width="876" height="180" border="0" /> 
  </div>
<?php require_once('includes/navlinks.inc.php'); ?>
<div id="mainContent">

<h3 class="SH"><strong>Harvest signup cancellation</strong></h3>
<?php if($confirmed=='no') { ?>
<div id="confirm">    
<p>Click the 'Cancel signup' button to confirm that you want to cancel your roster spot for the harvest. Click the 'Do not cancel' button or close the browser window if you do not want to cancel your spot. </p>
<form action="" method="post" name="confirmform">
<input name="confirm" type="submit" value="Cancel signup" />
<input name="close" type="submit" value="Do not cancel" />
<input name="harvpost" type="hidden" value="<?php echo $harvpost;?>" />
<input name="ID" type="hidden" value="<?php echo $ID;?>" />
</form>
<p></p>
</div> <!-- end of confirm div -->
<?php } 

else { ?>
<div id="cancelthanks">
    <p><?php if($err=="") { echo $whorow['fname'].", ";}?>Thank you for letting us know to cancel your sign up for the harvest  
<?php if($err=="") {echo " on ".date('F j',strtotime($harvrow['h_date']));} ?>. This may allow another person to attend. If you have any other questions about your registration or harvest sign up, please send an email to <a href="mailto:MasonD@piercecountycd.org">harvestpiercecounty@gmail.com</a>.</p>
</div> <!-- end of cancelthanks div -->
<?php } ?>
<!-- end #mainContent --></div>
	<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
<?php require_once('includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>
