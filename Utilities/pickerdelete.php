<?php 
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');

if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "all";


$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

if ((isset($_POST['ID_picker'])) && ($_POST['ID_picker'] != "")) {
  $deleteSQL = sprintf("DELETE FROM pickers WHERE ID_picker=%s",
                       GetSQLValueString($_POST['ID_picker'], "int"));

  
  $Result1 = mysqli_query($piercecty, $deleteSQL) or die(mysqli_error($piercecty));

  $deleteGoTo = "duplicates.php";
  header(sprintf("Location: %s", $deleteGoTo));
}

$colname_rsDelete = "-1";
if (isset($_GET['pickertemp'])) {
  $colname_rsDelete = $_GET['pickertemp'];
}

$query_rsDelete = sprintf("SELECT * FROM pickers WHERE ID_picker = %s", GetSQLValueString($colname_rsDelete, "int"));
$rsDelete = mysqli_query($piercecty, $query_rsDelete) or die(mysqli_error($piercecty));
$row_rsDelete = mysqli_fetch_assoc($rsDelete);
$totalRows_rsDelete = mysqli_num_rows($rsDelete);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>picker delete</title>
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
    <h2><strong>Picker delete</strong></h2>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <table width="1240" border="1" cellpadding="1" cellspacing="1" id="rosterlist">
      <tr>
        <th scope="col">ID_picker</th>
        <th scope="col">Last</th>
        <th scope="col">First</th>
      </tr>
      <tr class="centercell">
<form id="pickerdeleteform" name="pickerdeleteform" method="POST">
          <td><?php echo $row_rsDelete['ID_picker']; ?></td>
          <td><?php echo $row_rsDelete['lname']; ?></td>
		  <td><?php echo $row_rsDelete['fname']; ?></td>
          <td><input type="submit" name="submit" id="submit" value="delete" />
            <input type="hidden" name="ID_picker" value="<?php echo $row_rsDelete['ID_picker']; ?>" /></td>
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
