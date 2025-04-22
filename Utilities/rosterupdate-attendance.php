<?php
if (!isset($_SESSION)) {  session_start();}
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) { $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']); }

if (isset($_GET['harvesttemp'])) {$IDharvest = $_GET['harvesttemp']; 
} else {  $IDharvest = "";	}

$query_rsRoster = sprintf("SELECT ID_rosters, ID_harvest, rosters.ID_picker, pickers.lname, pickers.fname, rosters.regdate, rosters.status from rosters, pickers
						  where pickers.ID_picker=rosters.ID_picker and ID_harvest = %s and status<>'waiting' and status<>'cancel' ORDER BY pickers.lname, pickers.fname",  
						  GetSQLValueString($IDharvest, "int"));
$rsRoster = mysqli_query( $piercecty, $query_rsRoster);
$totalRows_rsRoster = mysqli_num_rows($rsRoster);

if(isset($_POST["Submit"])) {
	
for($i=0;$i<$totalRows_rsRoster;$i++) {
	$status=$_POST['status'];	
	$ID_rosters=$_POST['ID_rosters'];
	
	$rost = $ID_rosters[$i];
	$stat = $status[$i];
	$stat = strtolower($stat);
	if($stat == 'a') { $stat = 'absent'; }
	if($stat == 'h') { $stat = 'harvested'; }	
	if($stat == 'c') { $stat = 'cancel'; }
	if($stat == 'l') { $stat = 'leader'; }
	if($stat == 'w') { $stat = 'waiting'; }
	if($stat == 'd') { $stat = 'added'; }
	if($stat == 's') { $stat = 'signup'; }
	
	$sql1="UPDATE rosters SET status = '$stat' WHERE ID_rosters = $rost";
	$result1=mysqli_query($piercecty, $sql1);
	}

$updateGoTo = "rosterupdate-attendance.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
  header(sprintf("Location: %s", $updateGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>roster update</title>
    <style type="text/css">
<!--
-->
    </style>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<script>
function moveOnMax(field,nextFieldID){
  if(field.value.length >= 1){
    document.getElementById(nextFieldID).focus();
    document.getElementById(nextFieldID).select();
  }
}
function uparrow(e, attinput,prevFieldID){
var code = (e.keyCode ? e.keyCode : e.which);
if(code == 38) { //Enter keycode
    document.getElementById(prevFieldID).focus();
    document.getElementById(prevFieldID).select();
}
}
</script>
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2><strong>Roster attendance update</strong></h2>

    <form action="rosterupdate-attendance.php" method="get" name="filtersform">
       Harvest number     
         <input width = "50" type="number" name="harvesttemp" id="harvesttemp" value="<?php echo $IDharvest ?>"/>
      <input type="submit" name="submit" id="submit" value="Show records" />
      <input type="hidden" name="MM_filter" value="filtersform" />
  </form> 

     <p>Total rows = <?php echo $totalRows_rsRoster; ?></p>
     <p>Abbreviations: 'a' = 'absent'; 'h' = 'harvested'; 'c' = 'cancel'; 'l' = 'leader'; 'd'='added'; 's'=signup; up arrow=previous line  </p>
     <table width="800" border="1" cellpadding="1" cellspacing="1" id="rosterlist">
      <tr>
        <th>ID_picker</th>
        <th>Last</th>
        <th>First</th>
        <th>status</th>
      </tr>
<form action="<?php echo $editFormAction; ?>" name="attendanceform" method="POST">
      
	  <?php  
	  $ct=0;
	  while ($rows_rsRoster = mysqli_fetch_assoc($rsRoster)) { 
	  ++$ct;
	  ?>
        <tr>
          <input name="ID_rosters[]" type="hidden" id="ID_rosters" value="<?php echo $rows_rsRoster['ID_rosters'];?>" /></td>
          <td><?php echo $rows_rsRoster['ID_picker'];?></td>
          <td><?php echo $rows_rsRoster['lname'];?></td>
          <td><?php echo $rows_rsRoster['fname'];?></td>
          <td><input name="status[]" type="text" id="id<?php echo $ct;?>" value="<?php echo $rows_rsRoster['status'];?>" size="15" maxlength="15" onkeydown="uparrow(event, this,'id<?php echo $ct>0 ? $ct-2 : -1;?>')" onkeyup="moveOnMax(this,'id<?php echo $ct<$totalRows_rsRoster ? $ct+1 : 1;?>')"/></td>
        </tr>
        <?php }   ?>
		  <tr><td><input type="submit" name="Submit" value="Submit" /></td></tr>
</form>

    </table>
<p>&nbsp;</p>

<p><!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
   <br class="clearfloat" /></p>
  <!-- end #container -->
</div>
</div>
</body>
</html>
<?php
((mysqli_free_result($rsRoster) || (is_object($rsRoster) && (get_class($rsRoster) == "mysqli_result"))) ? true : false);
?>
