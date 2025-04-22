<?php require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
$MM_authorizedUsers = "all";

$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
if ((isset($_POST['ID_rosters'])) && ($_POST['ID_rosters'] != "")) {
  $deleteSQL = sprintf("DELETE FROM rosters WHERE ID_rosters=%s",
                       GetSQLValueString($_POST['ID_rosters'], "int"));
  
  $Result1 = mysqli_query($piercecty, $deleteSQL) or die(mysqli_error($piercecty));
  if ((isset($_POST['ID_harvest'])) && ($_POST['ID_harvest'] != "")) { $ID_harvest=$_POST['ID_harvest']; }
  $deleteGoTo = "rosterupdate.php?nametemp=&pickertemp=&harvesttemp=$ID_harvest&statustemp=&submit=Show+records&MM_filter=filtersform";
  header(sprintf("Location: %s", $deleteGoTo));
}

$colname_rsDelete = "-1";
if (isset($_GET['rosterstemp'])) {   $colname_rsDelete = $_GET['rosterstemp']; }

//$query_rsDelete = sprintf("SELECT * FROM rosters WHERE ID_rosters = %s", GetSQLValueString($colname_rsDelete, "int"));
$query_rsDelete="select ID_rosters, ID_harvest, rosters.ID_picker, rosters.regdate, rosters.status from rosters, pickers where ID_rosters=$colname_rsDelete";
$rsDelete = mysqli_query($piercecty, $query_rsDelete) or die(mysqli_error($piercecty));
$row_rsDelete = mysqli_fetch_assoc($rsDelete);
$totalRows_rsDelete = mysqli_num_rows($rsDelete);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>roster delete</title>
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
    <h2><strong>Roster delete</strong></h2>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <table width="1240" border="1" cellpadding="1" cellspacing="1" id="rosterlist">
      <tr>
        <th scope="col">ID_rosters</th>
        <th scope="col">ID_harvest</th>
        <th scope="col">ID_picker</th>
        <th scope="col">Name</th>
        <th scope="col">Regdate</th>
        <th scope="col">status</th>
      </tr>
      <tr class="centercell">
<form id="rosterdeleteform" name="rosterdeleteform" method="POST">
          <td><?php echo $row_rsDelete['ID_rosters']; ?></td>
          <td><?php echo $row_rsDelete['ID_harvest']; ?></td>
          <td><?php echo $row_rsDelete['ID_picker']; ?></td>
          <td><?php 
		  		$ID_picker=$row_rsDelete['ID_picker'];
		  		$name="select fname, lname from pickers where ID_picker=$ID_picker";
				$rsName=mysqli_query($piercecty,$name);
				$namerow=mysqli_fetch_assoc($rsName);
				if($namerow) echo $namerow['fname'].' '.$namerow['lname']; ?></td>
          <td><?php echo $row_rsDelete['regdate']; ?></td>
          <td><?php echo $row_rsDelete['status']; ?></td>
          <td><input type="submit" name="submit" id="submit" value="delete" />
        	  <input type="hidden" name="ID_harvest" value="<?php echo $row_rsDelete['ID_harvest']; ?>" />
              <input type="hidden" name="ID_rosters" value="<?php echo $row_rsDelete['ID_rosters']; ?>" /></td>
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
