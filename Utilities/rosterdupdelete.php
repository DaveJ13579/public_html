<?php 
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

$colname_rsDelete = "-1";
if (isset($_GET['rosterstemp'])) {
  $colname_rsDelete = $_GET['rosterstemp'];

$deleteSQL = sprintf("DELETE FROM rosters WHERE ID_rosters=%s",
                       GetSQLValueString($colname_rsDelete, "int"));
  
  $Result1 = mysqli_query($piercecty, $deleteSQL) or die(mysqli_error($piercecty));
  $deleteGoTo = "roster-duplicates.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>roster duplicates delete</title>
    <style type="text/css">
<!--
-->
    </style>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2><strong>Roster duplicate delete</strong></h2>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</div>
</body>
</html>
