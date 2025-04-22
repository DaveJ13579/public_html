<?php
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
$MM_authorizedUsers = "all,change,view";

$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$query_rsRoster = sprintf("SELECT * FROM rosters ORDER BY ID_harvest, ID_picker");
$rsRoster = mysqli_query($piercecty, $query_rsRoster) or die(mysqli_error($piercecty));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Roster duplicates finder</title>
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
    <h2><strong>Roster duplicates finder</strong>    </h2>
    <p>CAUTION: 'Delete' is immediate and permanent</p>
    <p>&nbsp;</p>
    <table width="1000" border="1" align="center" cellpadding="2" cellspacing="2" id="Pickerlist">
      <tr>
        <th>harvest</th>
        <th>Roster ID</th>
        <th>Picker ID</th>
        <th>Name</th>
        <th>Status</th>
        <th> </th>
      </tr>
            
      <?php
	  $temparr1 = mysqli_fetch_assoc($rsRoster);
	  $temparr2 = mysqli_fetch_assoc($rsRoster);
	  for ($i=1; $i < mysqli_num_rows($rsRoster); ++$i) { 
		if(($temparr2['ID_picker']> 0) AND ($temparr2['ID_harvest'] == $temparr1['ID_harvest']) AND ($temparr2['ID_picker'] == $temparr1['ID_picker'])) {
			$picker=$temparr1['ID_picker'];
			$getname="select fname, lname from pickers where ID_picker=$picker";
			$rsName=mysqli_query($piercecty,$getname) or die(mysqli_error($piercecty));
			$namerow=mysqli_fetch_assoc($rsName);
		?>
	<tr>
        <td><?php echo $temparr1['ID_harvest'];?></td>
        <td><?php echo $temparr1['ID_rosters'];?></td>
        <td><?php echo $temparr1['ID_picker'];?></td>
        <td><?php echo $namerow['fname']." ".$namerow['lname'];?></td>
        <td><?php echo $temparr1['status'];?></td>
    	<td><a href="rosterdupdelete.php?rosterstemp=<?php echo $temparr1['ID_rosters']; ?>">Delete</a></td>
	</tr>
    <tr>
        <td><?php echo $temparr2['ID_harvest'];?></td>
        <td><?php echo $temparr2['ID_rosters'];?></td>
        <td><?php echo $temparr2['ID_picker'];?></td>
        <td><?php echo $namerow['fname']." ".$namerow['lname'];?></td>
        <td><?php echo $temparr2['status'];?></td>
    	<td><a href="rosterdupdelete.php?rosterstemp=<?php echo $temparr2['ID_rosters']; ?>">Delete</a></td>
	</tr>
        <?php }
  
  		$temparr1 = $temparr2;
	    $temparr2 = mysqli_fetch_assoc($rsRoster);
         } ?>    
     
    </table>
    <p>&nbsp;</p>
    </div>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
<?php
((mysqli_free_result($rsRoster) || (is_object($rsRoster) && (get_class($rsRoster) == "mysqli_result"))) ? true : false);
?>
