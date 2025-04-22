<?php 
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
// picker finder search
$colname_rsName = "";
if (isset($_POST['nametemp'])) {$colname_rsName = $_POST['nametemp'];}
if (is_numeric($colname_rsName)) { 
$ID_picker = intval($colname_rsName);
$query_rsName = sprintf("SELECT ID_picker, lname, fname FROM pickers WHERE ID_picker = '$ID_picker' ORDER BY ID_picker ASC");
$rsName = mysqli_query($piercecty, $query_rsName) or die(mysqli_error($piercecty));
$row_rsName = mysqli_fetch_assoc($rsName);
} else {
$sfield='lname'; 
if(substr($colname_rsName,0,1)==' ') { $sfield='fname'; $colname_rsName=trim($colname_rsName); }
if(substr($colname_rsName,0,1)=='-') { $sfield='email'; $colname_rsName=substr($colname_rsName,1,strlen($colname_rsName)-1);
 }
$long=strlen(stripslashes($colname_rsName));
if($long==0) {$long=1; }
$query_rsName = "SELECT ID_picker, lname, fname FROM pickers WHERE left($sfield,'$long') = '$colname_rsName' ORDER BY ID_picker ASC";
$rsName = mysqli_query($piercecty, $query_rsName) or die(mysqli_error($piercecty));
$row_rsName = mysqli_fetch_assoc($rsName);
}
 $err='';
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
$IDharvest='';
$totalRows_rsRoster = 0;
unset($nametemp);
unset($IDpicker);
unset($IDharvest);
unset($status);

if (isset($_GET["MM_filter"]) && ($_GET["MM_filter"] == "filtersform")) {
                       if($_GET['nametemp']=='') { unset($nametemp); } else { $nametemp =  "'".$_GET['nametemp']."'"; }
                       if($_GET['pickertemp']=='') { unset($IDpicker); } else { $IDpicker =  $_GET['pickertemp']; }
                       if($_GET['harvesttemp']=='') { unset($IDharvest); } else { $IDharvest = $_GET['harvesttemp']; }
                       if($_GET['statustemp']=='') { unset($status); } else { $status = "'".$_GET['statustemp']."'"; }

$colname_rsRoster = "";

if (isset($_GET['nametemp'])) { $colname_rsRoster = $_GET['nametemp']; }

if (is_numeric($colname_rsRoster)) { 
$ID_picker = intval($colname_rsRoster);
$query_rsRoster = sprintf("SELECT ID_rosters, ID_harvest, rosters.ID_picker, pickers.lname, pickers.fname, seats, rosters.regdate, rosters.status FROM rosters, pickers WHERE rosters.ID_picker=pickers.ID_picker and rosters.ID_picker = '$ID_picker' ORDER BY ID_picker ASC");
$rsRoster = mysqli_query($piercecty, $query_rsRoster) or die(mysqli_error($piercecty));
$row_rsRoster = mysqli_fetch_assoc($rsRoster);
$totalRows_rsRoster = mysqli_num_rows($rsRoster);

} else {

$query="select ID_rosters, ID_harvest, rosters.ID_picker, pickers.lname, pickers.fname, seats, rosters.regdate, rosters.status from rosters,pickers where rosters.ID_picker=pickers.ID_picker "; // 'where 1=1' is so that other conditions can be appended next
if(isset($nametemp)) $query.= " and left(pickers.lname, 3) = left($nametemp,3)";
if(isset($IDpicker)) $query.= " and rosters.ID_picker=$IDpicker";
if(isset($IDharvest)) $query.= " and ID_harvest=$IDharvest";
if(isset($status)) $query.= " and rosters.status=$status";
$query_rsRoster=$query." ORDER BY ID_harvest, pickers.lname, fname ASC";
//echo $query_rsRoster; 
$rsRoster = mysqli_query($piercecty, $query_rsRoster) or die(mysqli_error($piercecty));
$row_rsRoster = mysqli_fetch_assoc($rsRoster);
$totalRows_rsRoster = mysqli_num_rows($rsRoster);
}
} // end of if filters isset

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "rosterupdateform")) {
	
// check if picker is registered	
$picker=$_POST['ID_picker'];
$existsq="select ID_picker from pickers where ID_picker=$picker";
$rsExists=mysqli_query($piercecty,$existsq);
if(mysqli_num_rows($rsExists)<>1) {$err="No volunteer with number $picker"; } 
else {
  $updateSQL = sprintf("UPDATE rosters SET ID_harvest=%s, ID_picker=%s, seats=%s, regdate=%s, status=%s WHERE ID_rosters=%s",
                       GetSQLValueString($_POST['ID_harvest'], "int"),
                       GetSQLValueString($_POST['ID_picker'], "int"),
                       GetSQLValueString($_POST['seats'], "int"),
                       GetSQLValueString($_POST['regdate'], "date"),
                       GetSQLValueString($_POST['status'], "text"),
					   GetSQLValueString($_POST['hiddenfield'], "int"));

  $Result1 = mysqli_query($piercecty, $updateSQL) or die(mysqli_error($piercecty));

  $updateGoTo = "rosterupdate.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
} // end of if exists
} // end of if post

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
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
  <div id="mainContent">
    <h2><strong>Roster update</strong></h2>
    <table width="170" border="2" cellpadding="1" cellspacing="1" id="sort">
      <tr align="center">
        <td width="162"><a href="rosterinsert.php">to Roster insert</a></td>
      </tr>
    </table>
    <p><strong>Filters:</strong></p>
    <form action="rosterupdate.php" method="get" name="filtersform">
    <p>Type three letters of last name 
      <input width = "100" type="text" name="nametemp" id="nametemp" value="<?php if(isset($_GET['nametemp'])) echo $_GET['nametemp']; ?>"/> and press 'Enter'</p>
    <p>Picker number      <input width = "50" type="int" name="pickertemp" id="pickertemp" value="<?php if(isset($IDpicker)) echo $IDpicker; ?>"/>
       Harvest number     
         <input width = "50" type="int" name="harvesttemp" id="harvesttemp" value="<?php if(isset($IDharvest)) echo $IDharvest; ?>"/>
       Roster status    <select name="statustemp" id="statustemp">
		 				<option selected=" "> </option>
			            <option value="signup">signup</option>
						<option value="leader">leader</option>
				  		<option value="cancel">cancel</option>
				        <option value="absent">absent</option>
          				<option value="harvested">harvested</option>
          				<option value="waiting">waiting</option>
          				<option value="added">added</option>
	  </select>    
    <p>
      <input type="submit" name="submit" id="submit" value="Show records" />
      <input type="hidden" name="MM_filter" value="filtersform" />
      </form>
      </p>
   <?php if($err) echo $err; ?>
    <table width="1220" border="1" cellpadding="1" cellspacing="1" id="rosterlist">
      <tr>
        <th scope="col">Rosters ID</th>
        <th scope="col">harvest ID</th>
        <th scope="col">Picker ID</th>
        <th scope="col">Name</th>
        <th scope="col">Seats</th>
        <th scope="col">Regdate</th>
        <th scope="col">status</th>
      </tr>
      <?php 
	  if(isset($rsRoster)) {

	  do { ?>
        <tr class="centercell">
	    <form action="<?php echo $editFormAction; ?>" id="rosterupdateform" name="rosterupdateform" method="POST">
          <td><?php echo $row_rsRoster['ID_rosters']; ?>
              <input name="hiddenfield" type="hidden" id="hiddenfield" value="<?php echo $row_rsRoster['ID_rosters']; ?>" /></td>
          <td><?php echo $row_rsRoster['ID_harvest']; ?>
	          <input name="ID_harvest" type="hidden" value="<?php echo $row_rsRoster['ID_harvest']; ?>" /></td>
          <td><input name="ID_picker" type="text" id="ID_picker" value="<?php echo $row_rsRoster['ID_picker']; ?>" size="5" maxlength="5" /></td>
          <td><?php echo $row_rsRoster['lname'].', '.$row_rsRoster['fname']; ?></td>
          <td><input name="seats" type="text" value="<?php echo $row_rsRoster['seats']; ?>" size="3" maxlength="2" /></td>
          <td><input name="regdate" type="text" id="regdate" value="<?php echo $row_rsRoster['regdate']; ?>" size="20" maxlength="20" /></td>
          <td><select name="status"  id="status" >
		            <option value="" <?php if( $row_rsRoster['status']=='') echo 'selected="selected"';?>>[select]</option>
		            <option value="signup" <?php if( $row_rsRoster['status']=='signup') echo 'selected="selected"';?>>signup</option>
            		<option value="waiting" <?php if( $row_rsRoster['status']=='waiting') echo 'selected="selected"';?>>waiting</option>
            		<option value="leader" <?php if( $row_rsRoster['status']=='leader') echo 'selected="selected"';?>>leader</option>
            		<option value="harvested" <?php if( $row_rsRoster['status']=='harvested') echo 'selected="selected"';?>>harvested</option>
            		<option value="cancel" <?php if( $row_rsRoster['status']=='cancel') echo 'selected="selected"';?>>cancel</option>
            		<option value="added" <?php if( $row_rsRoster['status']=='added') echo 'selected="selected"';?>>added</option>
            		<option value="absent" <?php if( $row_rsRoster['status']=='absent') echo 'selected="selected"';?>>absent</option>
		            </select></td>
		  <td><input type="submit" name="submit" id="submit" value="Update" /><input type="hidden" name="MM_update" value="rosterupdateform" /></td>
          <td><a href="rosterdelete.php?rosterstemp=<?php echo $row_rsRoster['ID_rosters']; ?>">Delete</a></td>
         </form>
        </tr>
        <?php } while ($row_rsRoster = mysqli_fetch_assoc($rsRoster));
	  } // end of if totalrows > 0?>
</table>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <div><form id="lastname" name="lastname" method="post" action="<?php echo $editFormAction ?>">
      <label><strong>Picker finder</strong>: Type letters from the start of the last name OR the ID number OR letters from the start of the first name preceded by a space <br />
<input width = "10" type="text"  style="background-color:#aaf969" name="nametemp" id="nametemp" /> and press 'Enter'</label>
      </form>
    <table width="825" border="1" cellpadding="5" cellspacing="5" id="Pickerlist"> 
        <tr>
        <?php 
	  	$colct=1; // initialize column count
	  	do {  ?> 
          <td><a href="voldetail.php?voltemp=<?php echo $row_rsName['ID_picker'];?>"><?php echo $row_rsName['ID_picker']." ".$row_rsName['fname']." ".$row_rsName['lname'];?></a></td>
        <?php ++$colct; if($colct==6) {$colct=1; echo "</tr><tr>"; } // if done 5 columns go to new row
         } while ($row_rsName = mysqli_fetch_assoc($rsName)); ?>     
        </tr>
    </table>
    <p>&nbsp;</p>
</div>
  <!-- end #container -->
</div>
</div>
</body>
</html>
