<?php
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
require_once('../includes/converter.inc.php');
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>PCD Gleaning Project Sites</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="../datepick/jsDatePick_ltr.min.css" />
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent"  style="width:98%;font-size:.7em;">
<h1>PCD Gleaning Project Sites</h1>
<?php $q="select farm, venue, address, city, state, zip, branch, contact1, phone1, email1,regdate from sites order by farm";
$rsQ=mysqli_query($piercecty,$q);
?>
<table border="1" cellspacing="1" cellpadding="2" style="width:100%;">
<tr><th>Site Name</th><th>Venue</th><th>Street Address</th><th>City</th><th>State</th><th>Zip Code</th><th>Branch</th><th>Property Owner</th><th>Phone</th><th>Email</th><th>Registration Date</th></tr>
<?php 
while($r=mysqli_fetch_assoc($rsQ)) {
extract($r);
echo "<tr><td>$farm</td><td>$venue</td><td>$address</td><td>$city</td><td>$state</td><td>$zip</td><td>$branch</td><td>$contact1</td><td>$phone1</td><td>$email1</td><td>$regdate</td></tr>";
} ?>
</table> 
</div> <!-- end main content div -->
  <br class="clearfloat" />
</div>  <!-- end #container -->
</body>
</html>
