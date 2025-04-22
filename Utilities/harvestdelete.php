<?php 
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');

if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

if ((isset($_POST['ID_harvest'])) && ($_POST['ID_harvest'] != "")) { 
  $deleteSQL = sprintf("DELETE FROM harvests WHERE ID_harvest=%s", GetSQLValueString($_POST['ID_harvest'], "int"));
  $Result1 = mysqli_query($piercecty, $deleteSQL) or die(mysqli_error($piercecty));
  
  $deleterosters=sprintf("delete from rosters where ID_harvest=%s", GetSQLValueString($_POST['ID_harvest'], "int"));
  $Result2 = mysqli_query($piercecty, $deleterosters) or die(mysqli_error($piercecty));
  
  $deleteGoTo = "harvestupdate.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

$colname_rsDelete = "-1";
if (isset($_GET['harvesttemp'])) {   $colname_rsDelete = $_GET['harvesttemp']; }
$query_rsDelete = sprintf("SELECT * FROM harvests WHERE ID_harvest = %s", GetSQLValueString($colname_rsDelete, "int"));
$rsDelete = mysqli_query($piercecty, $query_rsDelete) or die(mysqli_error($piercecty));
$row_rsDelete = mysqli_fetch_assoc($rsDelete);
$totalRows_rsDelete = mysqli_num_rows($rsDelete);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>harvest delete</title>
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
    <h2><strong>Harvest delete</strong></h2>
    <p>Be sure that you are deleting the right harvest. It woould be <strong><em>extremely unusual</em></strong> to delete a completed harvest with weights already entered. This will also delete all roster entries associated with this harvest.</p>
    <table width="1240" border="1" cellpadding="1" cellspacing="1" id="harvestlist">
      <tr>
        <th scope="col">Harvest ID</th>
        <th scope="col">Site ID</th>
        <th scope="col">Leader ID</th>
        <th scope="col">Date</th>
        <th scope="col">Time</th>
        <th scope="col">Calculated weight</th>
        <th scope="col">Total weight</th>
        <th scope="col">Pickers needed</th>
        <th scope="col">Where donated</th>
        <th scope="col">Status</th>
      </tr>
<form id="harvestdeleteform" name="harvestdeleteform" method="POST">
          <td><?php echo $row_rsDelete['ID_harvest']; ?></td>
          <td><?php echo $row_rsDelete['ID_site']; ?></td>
          <td><?php echo $row_rsDelete['ID_leader']; ?></td>
          <td><?php echo $row_rsDelete['h_date']; ?></td>
		  <td><?php echo $row_rsDelete['h_time']; ?></td>
          <td><?php echo $row_rsDelete['calcwgt']; ?></td>
          <td><?php echo $row_rsDelete['totwgt']; ?></td>
          <td><?php echo $row_rsDelete['pick_num']; ?></td>
          <td><?php echo $row_rsDelete['where_to']; ?></td>
          <td><?php echo $row_rsDelete['status']; ?></td>
          <td><input type="submit" name="submit" id="submit" value="delete" />
            <input type="hidden" name="ID_harvest" value="<?php echo $row_rsDelete['ID_harvest']; ?>" /></td>
      </form>
      </tr>
    </table>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</div>
</body>
</html>
<?php
((mysqli_free_result($rsDelete) || (is_object($rsDelete) && (get_class($rsDelete) == "mysqli_result"))) ? true : false);
?>
