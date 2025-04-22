<?php 
require_once('../Connections/piercecty.php'); 
if (!isset($_SESSION))  session_start(); 
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../includes/sqlcleaner.php');
$delswitch='no';
if(isset($_POST['userindex']))  $userindex=$_POST['userindex'];
if(isset($_POST['delete'])) { // delete user
$delswitch=$_POST['delswitch'];
if($delswitch=='no') {$delswitch='yes';}  
elseif($delswitch=='yes') {
	$dquery="delete from users where userindex=$userindex";
	$rsDelete=mysqli_query($piercecty, $dquery);
	$delswitch='no';
	}
} // end of if delete

if(isset($_POST['update']) and $_POST['IDuser']<>'') { // update user
$updatequery = sprintf("UPDATE users SET ID_user=%s,user_name=%s, user_password=%s, level=%s where userindex=$userindex",
                       GetSQLValueString($_POST['IDuser'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['userpassword'], "text"),
                       GetSQLValueString($_POST['level'], "text"));
$Result1 = mysqli_query($piercecty, $updatequery) or die(mysqli_error($piercecty));
} // end of update

if(isset($_POST['insert'])) {
$insertquery="INSERT INTO users (user_name, user_password, level) VALUES ('-username-','-password-','view')";
$Result1 = mysqli_query( $piercecty, $insertquery);
} // end of insert

$queryusers="select userindex, ID_user, user_name, user_password, level, lastlogin from users where user_name<>'admin' order by user_name";
$rsUsers = mysqli_query( $piercecty, $queryusers);

// picker finder search
$colname_rsName = "";
if (isset($_POST['nametemp'])) {$colname_rsName = $_POST['nametemp'];}
if (is_numeric($colname_rsName)) { 
$ID_picker = intval($colname_rsName);
$query_rsName = sprintf("SELECT ID_picker, lname, fname FROM pickers WHERE ID_picker = '$ID_picker' ORDER BY ID_picker ASC");
$rsName = mysqli_query( $piercecty, $query_rsName);
$row_rsName = mysqli_fetch_assoc($rsName);
} else {
$sfield='lname'; 
if(substr($colname_rsName,0,1)==' ') { $sfield='fname'; $colname_rsName=trim($colname_rsName); }
if(substr($colname_rsName,0,1)=='-') { $sfield='email'; $colname_rsName=substr($colname_rsName,1,strlen($colname_rsName)-1);
 }
$long=strlen(stripslashes($colname_rsName));
if($long==0) {$long=1; }
$query_rsName = "SELECT ID_picker, lname, fname FROM pickers WHERE left($sfield,'$long') = '$colname_rsName' ORDER BY ID_picker ASC";
$rsName = mysqli_query( $piercecty, $query_rsName);
$row_rsName = mysqli_fetch_assoc($rsName);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>database users manager</title>
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
    <h2><strong>Database user manager</strong></h2>
    NOTE: usernames and passwords may use only letters and numbers.<br />
    <table width="1240" border="1" cellpadding="1" cellspacing="1">
      <tr>
		<th>Name</th>
        <th>ID_user</th>
        <th>User name<br />[letters and numbers]</th>
        <th>Password<br />[letters and numbers]</th>
        <th>level</th>
        <th>lastlogin</th>
   	  <th><form action="users.php" name="inserting" method="POST"><input type="submit" name="insert" value="Add user" /></form></td>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($rsUsers)) { 
	  	if($delswitch=='no' or ($delswitch='yes' and $row['userindex']==$userindex)) {
			if($row['user_name']<>'webmaster')  {
	  ?>
        <tr class="centercell">
	    <form action="users.php" name="users" method="POST">
		  <td><?php $who=$row['ID_user']; $whoq="select fname, lname from pickers where ID_picker=$who";
		  				$rsWho=mysqli_query($piercecty, $whoq);
						if(mysqli_num_rows($rsWho)) {
						$whorow=mysqli_fetch_assoc($rsWho);
						extract($whorow); 
						echo '&nbsp;&nbsp;'.$fname.' '.$lname; }
           ?></td>         
          <td><input name="IDuser" type="int" value="<?php echo $row['ID_user']; ?>" size="5" maxlength="5" /></td>
          <td><input name="username" type="text" value="<?php echo $row['user_name']; ?>" size="15" maxlength="15" /></td>
          <td><input name="userpassword" type="text" value="<?php echo $row['user_password']; ?>" size="15" maxlength="15" /></td>
          <td><select name="level" type="text">
		            <option value="" <?php if($row['level']=='') echo 'selected="selected"';?>>[select]</option>
		            <option value="branch" <?php if( $row['level']=='branch') echo 'selected="selected"';?>>branch</option>
		            <option value="view" <?php if( $row['level']=='view') echo 'selected="selected"';?>>view</option>
		            <option value="change" <?php if( $row['level']=='change') echo 'selected="selected"';?>>change</option>
		            <option value="all" <?php if( $row['level']=='all') echo 'selected="selected"';?>>all</option>
                    </select></td>
  			<td><?php echo $row['lastlogin'];?></td>
		  <td><input type="submit" name="update" value="update" /></td>
          <?php if($delswitch=='yes')  {?>
          <td style="color:red; background-color:pink">Are you sure?<input type="submit" name="cancel" value="cancel" /></td>
          <?php } ?>
		  <td><input type="submit" name="delete" value="delete" /></td>
          <input type="hidden" name="delswitch" value="<?php echo $delswitch;?>" />
          <input type="hidden" name="userindex" value="<?php echo $row['userindex'];?>" />
          </form>
        </tr>
        <?php
		} // end of if not webmaster
		 } // end of itr delswitch
		  } // end of all users loop
		 ?>
</table>
<div><form id="lastname" name="lastname" method="post" action="">
      <label><strong>Picker finder</strong>: Type letters from the start of the last name<br />
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
</div>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container --></div>
</div>
</body>
</html>
