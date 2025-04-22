<?php
require_once('../Connections/piercecty.php'); 
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../includes/sqlcleaner.php');
require_once('../includes/branch.inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>PCD Gleaning Contacts</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="../datepick/jsDatePick_ltr.min.css" />
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
<h1>PCD Gleaning Contacts</h1>
<?php 
$q="select ID_picker, fname, lname, email, phone, address, city, state, zip from pickers where ID_picker>0 order by lname, fname";
$rsQ=mysqli_query($piercecty,$q);
?>
<table border="1" cellspacing="1" cellpadding="2">
<tr><th>First Name</th><th>Last name</th><th>Email</th><th>Phone Number</th><th>Address</th><th>City</th><th>State</th><th>Zip Code</th><th>Branch</th><th>Branch Leader (Y/N)</th></tr>
<?php 
while($r=mysqli_fetch_assoc($rsQ)) {
extract($r);
$branch=zipbranch(GetSQLValueString($zip, "text"));
echo "<tr><td>$fname</td><td>$lname</td><td>$email</td><td>$phone</td><td>$address</td><td>$city</td><td>$state</td><td>$zip</td><td>$branch</td>";

$ldrq="select ID_leader from branches where ID_leader=$ID_picker";
$rsLdr=mysqli_query($piercecty, $ldrq);
$numrows=mysqli_num_rows($rsLdr); 
$IsLdr= $numrows ?  'Y' : 'N'; 
echo "<td>$IsLdr</td></tr>";
}
?>
</table> 
</div> <!-- end main content div -->
  <br class="clearfloat" />
</div>
<?php include_once('../includes/AdminNav2.inc.php');?>  
<!-- end #container -->
</body>
</html>
