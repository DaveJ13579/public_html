<?php require_once('../Connections/piercecty.php'); 
if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../includes/sqlcleaner.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {   $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']); }

// picker finder search
$colname_rsName = "";
if (isset($_POST['nametemp'])) {$colname_rsName = $_POST['nametemp'];}
if (is_numeric($colname_rsName)) { 
$ID_picker = intval($colname_rsName);
$query_rsName = sprintf("SELECT ID_picker, lname, fname FROM pickers WHERE ID_picker = '$ID_picker' ORDER BY ID_picker ASC");
} else {
$sfield='lname'; 
if(substr($colname_rsName,0,1)==' ') { $sfield='fname'; $colname_rsName=trim($colname_rsName); }
if(substr($colname_rsName,0,1)=='-') { $sfield='email'; $colname_rsName=substr($colname_rsName,1,strlen($colname_rsName)-1); }
$long=strlen(stripslashes($colname_rsName));
if($long==0) {$long=1; }
$query_rsName = "SELECT ID_picker, lname, fname FROM pickers WHERE left($sfield,'$long') = '$colname_rsName' ORDER BY ID_picker ASC";
}
$rsName = mysqli_query($piercecty, $query_rsName) or die(mysqli_error($piercecty));
$row_rsName = mysqli_fetch_assoc($rsName);
$numrows=mysqli_num_rows($rsName);
// end picker finder search
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "pickerupdateform")) {
  $updateSQL = sprintf("UPDATE pickers SET lname=%s, fname=%s, phone=%s, phone2=%s, address=%s, city=%s, state=%s, zip=%s,  email=%s, assistance=%s, emerg=%s, ephone=%s, dupname=%s, harvester=%s,  leader=%s, scout=%s, selectteam=%s, weekemail=%s,  weekphone=%s, special=%s, how_hear=%s, volpass=%s, other_info=%s, regdate=%s, contactdate=%s, waive_date=%s, IP_picker=%s WHERE ID_picker=%s",
                       GetSQLValueString(ucwords(strtolower($_POST['lname'])), "text"),
                       GetSQLValueString(ucwords(strtolower($_POST['fname'])), "text"),
                       GetSQLValueString($_POST['phone'], "text"),
                       GetSQLValueString($_POST['phone2'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['zip'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['assistance'], "text"),
                       GetSQLValueString($_POST['emerg'], "text"),
                       GetSQLValueString($_POST['ephone'], "text"),
                       GetSQLValueString(ucfirst(strtolower($_POST['dupname'])), "text"),
                       GetSQLValueString(ucfirst(strtolower($_POST['harvester'])), "text"),
                       GetSQLValueString(ucfirst(strtolower($_POST['leader'])), "text"),
					   GetSQLValueString(ucfirst(strtolower($_POST['scout'])), "text"),
                       GetSQLValueString(ucfirst(strtolower($_POST['selects'])), "text"),				   
                       GetSQLValueString($_POST['weekemail'], "text"),
                       GetSQLValueString($_POST['weekphone'], "text"),
                       GetSQLValueString($_POST['special'], "text"),
                       GetSQLValueString($_POST['how_hear'], "text"),
                       GetSQLValueString(trim($_POST['volpass']), "text"),
                       GetSQLValueString($_POST['other_info'], "text"),
                       GetSQLValueString($_POST['regdate'], "date"),
                       GetSQLValueString($_POST['contactdate'], "date"),
                       GetSQLValueString($_POST['waive_date'], "date"),
                       GetSQLValueString($_POST['IP_picker'], "text"),
                       GetSQLValueString($_POST['hiddenfield'], "int"));

  $Result1 = mysqli_query($piercecty, $updateSQL);

  $updateGoTo = "pickerupdate.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsPicker = "-1";
if (isset($_GET['temp1'])) { $colname_rsPicker = $_GET['temp1']; }

$query_rsPicker = sprintf("SELECT * FROM pickers WHERE ID_picker = %s", GetSQLValueString($colname_rsPicker, "int"));
$rsPicker = mysqli_query($piercecty, $query_rsPicker) or die(mysqli_error($piercecty));
$row_rsPicker = mysqli_fetch_assoc($rsPicker);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>picker update</title>
<link href="../database.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH" >
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2><strong>Volunteer update</strong></h2>
    <p><a href="voldetail.php?voltemp=<?php echo $colname_rsPicker; ?>">Go to all details</a></p>
<div><form id="lastname" name="lastname" method="post" action="<?php echo $editFormAction ?>">
      <label><strong>Picker finder</strong>: Type letters from the start of the last name OR the ID number OR letters from the start of the first name preceded by a space  or letters from the start of an email address preceded by a dash.<br />
<input width = "10" type="text"  name="nametemp" id="nametemp" /> and press 'Enter'</label>
      </form>
<?php if(isset($numrows) and $numrows>0) { ?>
    <table width="825" border="1" cellpadding="5" cellspacing="5" id="Pickerlist"> 
        <tr>
        <?php 
	  	$colct=1; // initialize column count
	  	do {  ?> 
          <td><a href="pickerupdate.php?temp1=<?php echo $row_rsName['ID_picker'];?>"><?php echo $row_rsName['ID_picker']." ".$row_rsName['fname']." ".$row_rsName['lname'];?></a></td>
        <?php ++$colct; if($colct==6) {$colct=1; echo "</tr><tr>"; } // if done 5 columns go to new row
         } while ($row_rsName = mysqli_fetch_assoc($rsName)); ?>     
        </tr>
    </table>
<?php } // end of if found matches ?>
</div>
      <p>&nbsp;</p>
    <form action="<?php echo $editFormAction; ?>" id="pickerupdateform" name="pickerupdateform" method="POST">
      <table border="1" cellpadding="2" cellspacing="2" id="pickerlist">
      <tr><td><label><input type="submit" name="submit" id="submit" value="Save changes" /></label>
      <input type="hidden" name="MM_update" value="pickerupdateform" />
		</td>
        <td></td><td></td><td></td><td></td><td></td><td width="46"><a href="pickerdelete.php?pickertemp=<?php echo $row_rsPicker['ID_picker']; ?>">Delete</a></td>
</tr>
        <tr>
          <th>ID_picker</th>
          <th>Last name</th>
          <th>First name</th>
          <th>address</th>
          <th>city</th>
          <th>state</th>
          <th>Zip</th>
</tr>
<tr>
          <td align="center"><strong><?php echo $row_rsPicker['ID_picker']; ?></strong>
<input name="hiddenfield" type="hidden" id="hiddenfield" value="<?php echo $row_rsPicker['ID_picker']; ?>" /></td>
          <td><input name="lname" type="text" id="lname" value="<?php echo $row_rsPicker['lname']; ?>" size="20" maxlength="20" /></td>
          <td><input name="fname" type="text" id="fname" value="<?php echo $row_rsPicker['fname']; ?>" size="15" maxlength="15" /></td>
          <td><input name="address" type="text" id="address" value="<?php echo $row_rsPicker['address']; ?>" size="30" maxlength="50" /></td>
          <td><input name="city" type="text" id="city" value="<?php echo $row_rsPicker['city']; ?>" size="20" maxlength="50" /></td>
          <td><input name="state" type="text" id="state" value="<?php echo $row_rsPicker['state']; ?>" size="4" maxlength="2" /></td>
          <td><input name="zip" type="text" id="zip" value="<?php echo $row_rsPicker['zip']; ?>" size="5" maxlength="5" /></td>
</tr>
<tr>
          <th>Email</th>
          <th>Phone</th>
          <th>Phone2</th>
          <th>On assistance</th>
          <th>Password</th>
</tr>
<tr>
          <td><input name="email" type="text" id="email" value="<?php echo $row_rsPicker['email']; ?>" size="40" maxlength="40" /></td>
          <td><input name="phone" type="text" id="phone" value="<?php echo $row_rsPicker['phone']; ?>" size="15" maxlength="20" /></td>
          <td><input name="phone2" type="text" id="phone2" value="<?php echo $row_rsPicker['phone2']; ?>" size="15" maxlength="20" /></td>
         <td><select name="assistance">
                 <option value="Yes" <?php if($row_rsPicker['assistance']=='Yes') echo 'selected="selected"';?>>Yes</option>
                <option value="No" <?php if($row_rsPicker['assistance']=='No') echo 'selected="selected"';?>>No</option>
                </select>
		  </td>
          <td><input name="volpass" type="text" id="volpass" value="<?php echo $row_rsPicker['volpass']; ?>" size="15" maxlength="15" /></td>
        </tr>
        <tr>
          <th>Duplicate name alert</th>
          <th>Emergency contact</th>
          <th>Emergency phone</th>
          <th>How heard</th>
        </tr>
        <tr>
         <td><select name="dupname">
                 <option value="Yes" <?php if($row_rsPicker['dupname']=='Yes') echo 'selected="selected"';?>>Yes</option>
                <option value="No" <?php if($row_rsPicker['dupname']=='No') echo 'selected="selected"';?>>No</option>
                </select>
		  </td>
          <td><input name="emerg" type="text" id="emerg" value="<?php echo $row_rsPicker['emerg']; ?>" size="40" maxlength="40" /></td>
          <td><input name="ephone" type="text" id="ephone" value="<?php echo $row_rsPicker['ephone']; ?>" size="15" maxlength="20" /></td>
          <td><select name="how_hear">
            <option value=" " selected="selected">[select]</option>
            <option value="Neighbor">Neighbor</option>
            <option value="Food Is Free Tacoma">Food Is Free Tacoma</option>
            <option value="Harvest Pierce County's Gleaning Project web site">Harvest Pierce County's Gleaning Project web site</option>
            <option value="Food Bank">Food Bank</option>
            <option value="Harvest Pierce County's Gleaning Project web site volunteer">Harvest Pierce County's Gleaning Project volunteer</option>
            <option value="Newspaper">Newspaper</option>
            <option value="Facebook">Facebook</option>
            <option value="Flyer">Flyer</option>
            <option value="Other harvesting group">Other harvesting group</option>
            <option value="Friend">Friend</option>
            <option value="Other">Other</option>
            <option value="Web search">Web search</option>
          </select></td>

        </tr>
      </table>
      <p>&nbsp;</p>
      <table width="1220" border="2" cellpadding="2" cellspacing="2" id="table2">
        <tr>
          <th>Harvester</th>
          <th>Harvest/Site Leader</th>
          <th>Scout</th>
          <th>Select team</th>
          <th>Email notice</th>
          <th>Phone notice</th>
        </tr>
        <tr class="centercell">
         <td><select name="harvester">
                 <option value="Yes" <?php if($row_rsPicker['harvester']=='Yes') echo 'selected="selected"';?>>Yes</option>
                <option value="No" <?php if($row_rsPicker['harvester']=='No') echo 'selected="selected"';?>>No</option>
                </select>
		  </td>
         <td><select name="leader">
                 <option value="Yes" <?php if($row_rsPicker['leader']=='Yes') echo 'selected="selected"';?>>Yes</option>
                <option value="No" <?php if($row_rsPicker['leader']=='No') echo 'selected="selected"';?>>No</option>
                </select>
		  </td>
         <td><select name="scout">
                 <option value="Yes" <?php if($row_rsPicker['scout']=='Yes') echo 'selected="selected"';?>>Yes</option>
                <option value="No" <?php if($row_rsPicker['scout']=='No') echo 'selected="selected"';?>>No</option>
                </select>
		  </td>
         <td><select name="selects">
                 <option value="Yes" <?php if($row_rsPicker['selectteam']=='Yes') echo 'selected="selected"';?>>Yes</option>
                <option value="No" <?php if($row_rsPicker['selectteam']=='No') echo 'selected="selected"';?>>No</option>
                </select>
		  </td>
         <td><select name="weekemail">
                 <option value="Yes" <?php if($row_rsPicker['weekemail']=='Yes') echo 'selected="selected"';?>>Yes</option>
                <option value="No" <?php if($row_rsPicker['weekemail']=='No') echo 'selected="selected"';?>>No</option>
                </select>
		  </td>
         <td><select name="weekphone">
                 <option value="Yes" <?php if($row_rsPicker['weekphone']=='Yes') echo 'selected="selected"';?>>Yes</option>
                <option value="No" <?php if($row_rsPicker['weekphone']=='No') echo 'selected="selected"';?>>No</option>
                </select>
		  </td>
        </tr>
        <tr>
          <th scope="col">Most recent contact</th>
          <th scope="col">Registration Date</th>
          <th scope="col">IP address</th>
          <th scope="col">Select team waiver date</th>
        </tr>
        <tr class="centercell">
          <td><input name="contactdate" type="text" value="<?php echo $row_rsPicker['contactdate']; ?>" size="20" maxlength="20" /></td>
          <td><input name="regdate" type="text" value="<?php echo $row_rsPicker['regdate']; ?>" size="20" maxlength="20" /></td>
          <td><input name="IP_picker" type="text" value="<?php echo $row_rsPicker['IP_picker']; ?>" size="20" maxlength="20" /></td>
          <td><input name="waive_date" type="text" value="<?php echo $row_rsPicker['waive_date']; ?>" size="20" maxlength="20" /></td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <table width="1220" border="2" cellpadding="2" cellspacing="10" id="table3">
        <tr><th>Physical Limitations or Special Accommodations</th></tr>
        <tr><td><textarea name="special" cols="140" rows="4" id="special"><?php echo $row_rsPicker['special']; ?></textarea></td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <table width="1220" border="2" cellpadding="2" cellspacing="10" id="table3">
        <tr>
          <th>Other info</th>
        </tr>
        <tr>
          <td><textarea name="other_info" cols="140" rows="4" id="other_info"><?php echo $row_rsPicker['other_info']; ?></textarea></td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <a href="pickerdelete.php?pickertemp=<?php echo $row_rsPicker['ID_picker']; ?>">Delete</a>
    </form>
    <p>
      <!-- end #mainContent -->
    </p>
  </div>


  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
<?php
((mysqli_free_result($rsPicker) || (is_object($rsPicker) && (get_class($rsPicker) == "mysqli_result"))) ? true : false);
?>
