<?php
if (!isset($_SESSION)) {   session_start(); }

$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');

$IDharvest="";
if(isset($_GET['harvesttemp'])) $IDharvest=$_GET['harvesttemp'];
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
			           $nametemp =  "";
                       $IDpicker =  "";
                       $status =    "";

if (isset($_POST["MM_update"]) && ($_POST["MM_update"] == "filtersform")) {
                       $nametemp =  $_POST['nametemp'];
                       $IDpicker =  $_POST['pickertemp'];
                       $IDharvest = $_POST['harvesttemp'];
                       $status =    $_POST['statustemp'];

}
else { $nametemp = ""; }

$query_rsRoster = sprintf("SELECT ID_rosters, ID_harvest, rosters.ID_picker, rosters.regdate, rosters.status, seats FROM rosters, pickers 
						  WHERE rosters.ID_picker=pickers.ID_picker and (left(lname, 3)=left(%s,3) OR rosters.ID_picker=%s OR ID_harvest=%s OR rosters.status=%s)",  
						  GetSQLValueString($nametemp, "text"),
						  GetSQLValueString($IDpicker, "int"),
						  GetSQLValueString($IDharvest, "int"),
						  GetSQLValueString($status, "text"));
$rsRoster = mysqli_query($piercecty, $query_rsRoster) or die(mysqli_error($piercecty));
$row_rsRoster = mysqli_fetch_assoc($rsRoster);
$totalRows_rsRoster = mysqli_num_rows($rsRoster);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>roster viewer</title>
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
    <h2><strong>Roster viewer</strong></h2>
    <p>Filters:</p>
    <form action="rosterviewer.php?harvesttemp=<?php echo $IDharvest; ?>" method="post" name="filtersform">
    <p>Picker's last name <input width = "100" type="text" name="nametemp" id="nametemp" /> (Matches are found based on the first three letters only.)</p>
    <p>Picker number      <input width = "50" type="text" name="pickertemp" id="pickertemp" />
       Harvest number     <input width = "50" type="text" name="harvesttemp"  value="<?php echo $IDharvest; ?>" />
       Roster status    <select name="statustemp" id="statustemp">
       					<option selected="selected"></option>
						<option value="signup">signup</option>
						<option value="leader">leader</option>
						<option value="harvested">harvested</option>
				  		<option value="cancel">cancel</option>
				        <option value="absent">absent</option>
          				<option value="assisted">assisted</option>
	  </select>    
    <p>
      <input type="submit" name="submit" id="submit" value="Show records" />
      <input type="hidden" name="MM_update" value="filtersform" />
      
    </form>
   
    <p>&nbsp;</p>
<table width="1240" border="1" cellpadding="1" cellspacing="1" id="rosterlist">
      <tr>
        <th scope="col">ID_harvest</th>
        <th scope="col">ID_picker</th>
        <th scope="col">Last</th>
        <th scope="col">First</th>
        <th scope="col">Regdate</th>
        <th scope="col">status</th>
        <th scope="col">seats</th>
      </tr>
      <?php do { ?>
        <tr class="centercell">
          <td><?php echo $row_rsRoster['ID_harvest']; ?></td>
          <td><?php echo $row_rsRoster['ID_picker']; ?></td>
          <td><?php 
		  				$picker=$row_rsRoster['ID_picker'];
		  				$pickerq="select fname, lname from pickers where ID_picker=$picker";
						$rsPicker=mysqli_query($piercecty,$pickerq);
						$pickerrow=mysqli_fetch_assoc($rsPicker);
				 	  echo $pickerrow['lname']; ?></td>
          <td><?php echo $pickerrow['fname']; ?></td>
          <td><?php echo $row_rsRoster['regdate']; ?></td>
          <td><?php echo $row_rsRoster['status']; ?></td>
          <td><?php echo $row_rsRoster['seats']; ?></td>
        </tr>
        <?php } while ($row_rsRoster = mysqli_fetch_assoc($rsRoster)); ?>
    </table>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</div>
</body>
</html>
<?php
((mysqli_free_result($rsRoster) || (is_object($rsRoster) && (get_class($rsRoster) == "mysqli_result"))) ? true : false);
?>
