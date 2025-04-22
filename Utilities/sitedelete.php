<?php 
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
if ((isset($_POST['ID_site'])) && ($_POST['ID_site'] != "")) {
  $deleteSQL = sprintf("DELETE FROM sites WHERE ID_site=%s", GetSQLValueString($_POST['ID_site'], "int"));
 
  $Result1 = mysqli_query($piercecty, $deleteSQL) or die(mysqli_error($piercecty));
  $deleteGoTo = "sitelist.php";
  header(sprintf("Location: %s", $deleteGoTo));
}

$colname_rsDelete = "-1";
if (isset($_GET['sitetemp'])) {
  $colname_rsDelete = $_GET['sitetemp'];
}

$query_rsDelete = sprintf("SELECT * FROM sites WHERE ID_site = %s", GetSQLValueString($colname_rsDelete, "int"));
$rsDelete = mysqli_query($piercecty, $query_rsDelete) or die(mysqli_error($piercecty));
$row_rsDelete = mysqli_fetch_assoc($rsDelete);
$totalRows_rsDelete = mysqli_num_rows($rsDelete);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>site delete</title>
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
    <h2><strong>Site delete</strong></h2>
    <p><em><strong>Are you sure that you want to delete this site completely?</strong></em> Usually a site that will no longer be used will be marked as 'Inactive' rather than deleted. Never delete sites which have previously had harvests.</p>
    <table width="1240" border="1" cellpadding="1" cellspacing="1" id="rosterlist">
      <tr>
        <th scope="col">ID_site</th>
        <th scope="col">Farm name</th>
        <th scope="col">Contact</th>
      </tr>
      <tr class="centercell">
<form id="sitedeleteform" name="sitedeleteform" method="POST">
          <td><?php echo $row_rsDelete['ID_site']; ?></td>
          <td><?php echo $row_rsDelete['farm']; ?></td>
		  <td><?php echo $row_rsDelete['contact1']; ?></td>
          <td><input type="submit" name="submit" id="submit" value="delete" />
            <input type="hidden" name="ID_site" value="<?php echo $row_rsDelete['ID_site']; ?>" /></td>
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
