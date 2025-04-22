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
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "rosterinsertform")) {
// check that ID_picker exists	
$picker=$_POST['ID_picker'];
$harvest=$_POST['ID_harvest'];
$regdate=$_POST['regdate'];
$status=$_POST['status'];

$exists="select ID_picker from pickers where ID_picker=$picker";
$rsExists=mysqli_query($piercecty,$exists);
$numrows=mysqli_num_rows($rsExists);
if(!$numrows) { $err='That picker number could not be found'; }
elseif(!is_numeric($harvest)){ $err='No harvest number entered';}
elseif($regdate==''){ $err='No sign up date entered';}
elseif($status=='') {$err='no roster status selected';}
	else {
 $insertSQL = sprintf("INSERT INTO rosters (ID_harvest, ID_picker, seats, regdate, status) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['ID_harvest'], "int"),
                       GetSQLValueString($_POST['ID_picker'], "int"),
                       GetSQLValueString($_POST['seats'], "int"),
                       GetSQLValueString($_POST['regdate'], "date"),
                       GetSQLValueString($_POST['status'], "text"));

  $Result1 = mysqli_query($piercecty, $insertSQL) or die(mysqli_error($piercecty));
  $insertGoTo = "rosterinsert.php";
  header("Location: $insertGoTo");
} // found picker
} // end of if form submit
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>roster insert</title>
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
    <h2><strong>Roster insert</strong></h2>
    <table width="170" border="2" cellpadding="1" cellspacing="1" id="sort">
      <tr align="center">
        <td width="162"><a href="rosterupdate.php">to Roster update</a></td>
      </tr>
    </table>
    <p><?php echo $err; ?></p>
    <table width="1200" border="1" cellpadding="1" cellspacing="1" id="rosterlist">
      <tr>
        <th scope="col">harvest ID number</th>
        <th scope="col">Volunteer ID number</th>
        <th scope="col">Seats (-1 or greater)</th>
        <th scope="col">Signup date yyyy-mm-dd</th>
        <th scope="col">Roster status</th>
      </tr>
        <tr class="centercell">
	    <form action="" id="rosterinsertform" name="rosterinsertform" method="POST">
          <td><input name="ID_harvest" type="number" size="5" maxlength="5" /></td>
          <td><input name="ID_picker" type="number" size="5" maxlength="5" /></td>
          <td><input name="seats" type="number" size="3" maxlength="2" value="0"/></td>
          <td><input name="regdate" type="text" size="20" maxlength="20" /></td>
          <td><select name="status"  id="status" >
		            <option value="" >[select]</option>
		            <option value="signup" >signup</option>
            		<option value="waiting" >waiting</option>
            		<option value="assisted" >assisted</option>
            		<option value="leader" >leader</option>
            		<option value="harvested">harvested</option>
            		<option value="cancel" >cancel</option>
            		<option value="added">added</option>
            		<option value="absent">absent</option>
		            </select></td>
		  <td><input type="submit" name="submit" id="submit" value="Insert" />
          <input type="hidden" name="MM_insert" value="rosterinsertform" /></td>
         </form>
        </tr>
</table>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <div><form id="lastname" name="lastname" method="post" action="">
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
