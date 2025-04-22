<?php
if (!isset($_SESSION)) {  session_start(); }
require_once('../Connections/piercecty.php');
require_once('../includes/dencode.inc.php');
require_once('../includes/sqlcleaner.php');
include_once('../includes/converter.inc.php');
require_once('../includes/smtpmailer.inc.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {   $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']); }
// get the decoded harvest ID
$ID=0;
if(isset($_GET['ID'])) { $ID=($_GET['ID']);
$ID= decode($ID);
if(!is_numeric($ID)) $ID=0;
}
$crops=cropstring($ID);

// find the ID in the harvest table if exists
$query="Select ID_harvest from harvests where ID_harvest=$ID";
$result=mysqli_query($piercecty, $query);
if(!isset($result)) $ID=0;

// if form button is presed insert into surveyowner table
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "survey")) {
	$quote= isset($_POST['quote']) ? 'Yes' : 'No';
	$surveydate=date('Y-m-d');
	$insertSQL = sprintf("INSERT INTO surveyowner (ID_harvest, surveydate, crops, most, better, other, quote) VALUES ($ID, '$surveydate', %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['crops'], "text"),
                       GetSQLValueString($_POST['most'], "text"),
                       GetSQLValueString($_POST['better'], "text"),
                       GetSQLValueString($_POST['other'], "text"),
                       GetSQLValueString($quote, "text"));

$Result1 = mysqli_query($piercecty, $insertSQL) or die(mysqli_error($piercecty));
// send a notice to info and harvest leader
$query="select harvests.ID_harvest, contact1, farm, h_date, pickers.email from sites, harvests, pickers where sites.ID_site=harvests.ID_site and  pickers.ID_picker=harvests.ID_leader and harvests.ID_harvest=$ID";
$result=mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$row=mysqli_fetch_assoc($result);
$leaderemail=$row['email'];

$ms="Harvest date: ".$row['h_date']." ID: ".$row['ID_harvest']."\n";
$ms.="Owner: ".$row['contact1']." ".$row['farm']."\n";
$ms.="Crop ".$crops."\n";
$ms.="What did you like the most about the harvest? ".stripslashes($_POST['most'])."\n\n";
$ms.="What could we do better next year? ".stripslashes($_POST['better'])."\n\n";
$ms.="Do you have any other comments or suggestions? ".stripslashes($_POST['other'])."\n\n";
if($quote=='Yes') {$ms.="Pierce County Gleaning Project may use my survey response in their written materials."."\n\n";}
$message=$ms;

$email="info@piercecountycd.org";
$subject='Crop owner survey response';
smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");
// echo "<br />".$email."<br />".$subject."<br />".$headers."<br />".$message; exit;
  $insertGoTo = "surveythankyou-owners.php";
  header(sprintf("Location: %s", $insertGoTo));
  exit;
} // end of if form isset

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update thank you</title>
<link href="../piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	margin-top: 10px;
}
-->
</style>
</head>
<body class="SH">
<div id="container">
  <div id="header">
   <img src="../images/banners/banner-home.jpg" width="876" height="180" border="2" /> 
  </div>
<?php require_once('../includes/navlinks2.inc.php'); ?>
<div id="mainContent">
<p><img src="../images/logos/logo.jpg" /></p>

    <h2>Crop owner survey</h2>
<p>Thank you for letting us harvest your crop.  By filling out this short survey you can help us make improvements to benefit the volunteers, crop owners and those who receive the food donations.</p>
<p>Please fill out the form with as much or as little information as you want and then click on 'Save Survey.' Thank you for your support.</p>
<form id="survey" name="survey" method="POST" action="<?php echo $editFormAction; ?>">
  <p>What crops of yours did we pick? 
    <input name="crops" type="text" size="65" maxlength="50" />
  </p>
  <p>&nbsp;</p>
<p>What did you like the most about the harvest?</p>
  <p>
  <textarea name="most" id="most" cols="100" rows="5"></textarea>
  </p>
  <p>&nbsp;</p>
  <p>What could we do better next year?</p>
  <p>
    <textarea name="better" id="better" cols="100" rows="5"></textarea>
  </p>
<p>&nbsp;</p>
  <p>Do you have any other comments or suggestions?</p>
  <p>
    <textarea name="other" id="other" cols="100" rows="5"></textarea>
  </p>
<input name="quote" type="checkbox" value="Yes" checked />
Harvest Pierce County's Gleaning Project may use my survey response in their written materials.
<p>
    <label>
      <input type="submit" name="submit" id="submit" value="Save Survey" />
    </label>
  </p>
  <input type="hidden" name="MM_insert" value="survey" />
</form>
<p>&nbsp;</p>
  <!-- end #mainContent -->
<!-- end #mainContent --></div>
<br class="clearfloat" />
<?php require_once('../includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>