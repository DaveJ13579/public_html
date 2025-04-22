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
<title>PCD Gleaning Crops</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="../datepick/jsDatePick_ltr.min.css" />
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
<h1>PCD Gleaning Crops</h1>
<?php $q="select name from crops order by name";
$rsQ=mysqli_query($piercecty,$q);
?>
<table border="1" cellspacing="1" cellpadding="2">
<tr><th>Crop Name</th></tr>
<?php 
while($r=mysqli_fetch_assoc($rsQ)) {
extract($r);
echo "<tr><td>$name</td></tr>";
} ?>
</table> 
</div> <!-- end main content div -->
  <br class="clearfloat" />
</div>
<?php include_once('../includes/AdminNav2.inc.php');?>  
<!-- end #container -->
</body>
</html>
