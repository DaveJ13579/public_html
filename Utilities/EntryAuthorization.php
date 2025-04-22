<?php require_once('../Connections/piercecty.php'); 
if (!isset($_SESSION)) { session_start(); }
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$site = -1;
if (isset($_GET['site'])) { $site=$_GET['site']; }
$query="select * from sites where ID_site='$site'";
$rssite = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
if(mysqli_num_rows($rssite)) {
	$row = mysqli_fetch_assoc($rssite);
	$where=$row['address'].", ".$row['city'].", ".$row['state']." ".$row['zip'];
	$contact1=$row['contact1'];
	$crops=$row['crops'];
	$ID_site=$row['ID_site'];
	}  else { 
	$where=':<br /><br />_______________________________________________________________<br /><br />'; 
	$contact1=''; $crops=''; $ID_site='';
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Entry Authorization Form</title>
<style type="text/css">

body  {
	background: #ffffff;
	margin: 0; /* it's good practice to zero the margin and padding of the body element to account for differing browser defaults */
	text-align: left; /* this centers the container in IE 5* browsers. The text is then set to the left aligned default in the #container selector */
	color: #000000;
	font-size: 100.01%;
	clear: none;
	background-color: #FFFfff;
	font-family: Arial, Helvetica, sans-serif;
	overflow: visible;
}
.SH #container {
	width: 8.5 in; 
	height: 11 in;
	font-size: 16pt;
	padding: 0.1in;
} 
-->
</style>
</head>
<body class="SH">
<div id="container">
    <div id="header">
	</div>
  <div id="mainContent">
    <p><img src="../images/logos/gleancolorhorz.png" width="340" height="75" /></p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p><strong>ENTRY AUTHORIZATION FORM</strong></p>
    <p>Site registry  number: <?php echo $ID_site; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Crop type(s): <?php echo $crops; ?></p>
    <p>&nbsp;</p>
<p>Harvest Pierce County's Gleaning Project  members and volunteers have my permission to enter onto my property located at <?php echo $where; ?> for the sole purpose of picking and/or gathering fruit/vegetables that I have  agreed to donate to Harvest Pierce County's Gleaning Project. I may revoke this authorization at any time by contacting Harvest Pierce County's Gleaning Project.
<p>&nbsp;</p>
<p>________________________________________    ______________<br />
  <em>Signature of Owner                                                     Date</em></p>
<p><?php echo $contact1; ?></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><em>Revised 2014-10-31</em></p>
  </div>
<!-- end #container --></div>
</body>
</html>
