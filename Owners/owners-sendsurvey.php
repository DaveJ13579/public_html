<?php 
require_once('../Connections/piercecty.php');
require_once('../includes/dencode.inc.php');
require_once('../includes/sqlcleaner.php');
include_once('../includes/converter.inc.php');
require_once('../includes/smtpmailer.inc.php');

if (!isset($_SESSION)) {  session_start();}
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

if(!isset($_GET['harvesttemp'])) { echo "No harvest number in link query string"; }
else { // do the page

$infostr='';
$harvest=$_GET['harvesttemp'];
$crops=cropstring($harvest);
$query = "SELECT calcwgt, totwgt, h_date, pick_num, farm, contact1, email1, surveysent FROM harvests, sites WHERE harvests.ID_site = sites.ID_site AND harvests.ID_harvest=$harvest";
$rsHarvests = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$row = mysqli_fetch_assoc($rsHarvests);
$eID=encode($harvest);
// send if button pressed
$message='';
if(isset($_POST['send'])) {
$message=$_POST['message'];
$email=$row['email1'];
$subject='Salem Harvest - crop owner survey request';
smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");
$query="update harvests set surveysent='Yes' where ID_harvest=$harvest";
$rsSent = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$infostr='Message sent';
} // end of send

$ms="Hello ".$row['contact1'].",\n\n";
$ms.="Thank you for donating your crop of ".strtolower($crops)." through Pierce County Gleaning Project. ";
$ms.="A total of ".$row['totwgt']." pounds were donated. It all helps us toward our goals of reducing hunger, ";
$ms.="building community and living sustainably. You will be receiving a tax donation receipt in the mail.\n\n";
$ms.="We are always interested in finding ways to do harvests better. ";
$ms.="By filling out a short survey you can help us make improvements to benefit the volunteers, crop owners and those who receive the food donations. ";
$ms.="Please take a minute to go to the survey page at:\n\n";
$ms.="http://www.piercecountygleaningproject.org/Owners/Survey-owners.php?ID=$eID \n\n";
$ms.="Thank you for your support.\n\n";
$ms.="Pierce County Gleaning Project";
if($message<>'') $ms=$message;
if($infostr=="Message sent") $ms='';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Owner survey request</title>
<link href="../database.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
  <h2 class="SH"><strong>Send owner survey request</strong></h2>

<p>Crop owner: <?php echo $row['contact1']." ".$row['farm']."<br />";?>
Crop: <?php echo $crops."<br />";?>
Harvest date: <?php echo $row['h_date']."<br />";?>
</p>
<form action="owners-sendsurvey.php?harvesttemp=<?php echo $harvest; ?>" method="post" target="_self">
  <textarea name="message" cols="100" rows="20" >
<?php echo $ms;?>
</textarea>
<input name="send" type="submit" value="Send survey request" />
</form>
<?php echo $infostr; ?> 
</div>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
<br class="clearfloat" />
  <div id="footer">
    <!-- end #footer -->
  </div>
<!-- end #container --></div>
</body>
</html>
<?php
((mysqli_free_result($rsHarvests) || (is_object($rsHarvests) && (get_class($rsHarvests) == "mysqli_result"))) ? true : false);
} // end of else do the page
?>
