<?php 
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$query_rsName = sprintf("SELECT * FROM pickers ORDER BY lname, fname, ID_picker");
$rsName = mysqli_query($piercecty, $query_rsName) or die(mysqli_error($piercecty));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Picker duplicates finder</title>
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
    <h2><strong>Picker duplicates finder</strong>    </h2>
    <p>If the more recent registration has new information, click on the older Picker ID number to go to Picker Update to change those fields. If the picker has harvest roster entries under both registrations, then adjust the picker ID numbers in <a href="rosterupdate.php">Roster Update</a>. Recheck and adjust the duplicate entries until one can be deleted.</p>
    <p>A quicker adjustment can be made by clicking on 'Merge all...&quot; This puts all of the higher picker number information into the older picker number record, deletes the newer record, and assigns any of the newer picker number's roster entries to the older picker number. Be certain of duplicates before using this function. The page may not execute if there are single quotes in any fields. If distinct pickers with identical names are identified, change the 'dupname' fields to 'Yes' using pickerupdate.php.</p>
    <table width="400" border="1" align="center" cellpadding="2" cellspacing="2" id="Pickerlist">
      <tr>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>
            
      <?php
	  $temparr1 = mysqli_fetch_assoc($rsName);
	  $temparr2 = mysqli_fetch_assoc($rsName);
	  for ($i=1; $i < mysqli_num_rows($rsName); ++$i) { 
	  	$dupes=''; if(($temparr2['dupname'] == 'Yes') AND ($temparr1['dupname'] =='Yes')) {$dupes='skip'; } // check for duplicate names flags
		if(($dupes=='') AND ($temparr2['lname'] == $temparr1['lname']) AND ($temparr2['fname'] == $temparr1['fname'])) { 
		// two consecutive names are the same so build a table section?>
 	<tr>
    	<td>&nbsp;</td>
    	<td><a href="pickerdelete.php?pickertemp=<?php echo $temparr1['ID_picker']; ?>">Delete</a></td>
    	<td><a href="pickerdelete.php?pickertemp=<?php echo $temparr2['ID_picker']; ?>">Delete</a></td>
	</tr>
    <tr>
    	<td>Picker ID</td>
        <td><a href="pickerupdate.php?temp1=<?php echo $temparr1['ID_picker']; ?>"><?php echo $temparr1['ID_picker']; ?></a></td>
        <td><a href="pickerupdate.php?temp1=<?php echo $temparr2['ID_picker']; ?>" ><?php echo $temparr2['ID_picker']; ?></a></td>
	</tr>
    <tr>    
    <td><form action="merge.php" name="merge" method="get" target="self" >
    	<input  type="submit" value="Merge all new into old" />
    	<input name="old" type="hidden" value="<?php echo $temparr1['ID_picker']; ?>" />
    	<input name="new" type="hidden" value="<?php echo $temparr2['ID_picker']; ?>" />
		</form></td>
    <td></td>
    <td></td>
        </tr>
    <tr>
    	<td>Name</td>
        <td><?php echo $temparr1['fname']." ".$temparr2['lname'];?></td>
        <td><?php echo $temparr2['fname']." ".$temparr2['lname'];?></td>
	</tr>
	<tr>
 		<td>email</td>
        <td><?php echo $temparr1['email'];?></td>
        <td><?php echo $temparr2['email'];?></td>
	</tr>
	<tr>
 		<td>address</td>
        <td><?php echo $temparr1['address'];?></td>
        <td><?php echo $temparr2['address'];?></td>
        
    	<?php if(($temparr1['phone']) <> ($temparr2['phone'])) { ?>
	<tr>
 		<td>Phone</td>
        <td><?php echo $temparr1['phone'];?></td>
        <td><?php echo $temparr2['phone'];?></td>
    </tr> <?php } ?>      

    	<?php if(($temparr1['phone2']) <> ($temparr2['phone2'])) { ?>
	<tr>
 		<td>Phone2</td>
        <td><?php echo $temparr1['phone2'];?></td>
        <td><?php echo $temparr2['phone2'];?></td>
    </tr> <?php } ?>      
     
   	  <?php if(($temparr1['harvester']) <> ($temparr2['harvester'])) { ?>
	<tr>
 		<td>harvester</td>
        <td><?php echo $temparr1['harvester'];?></td>
        <td><?php echo $temparr2['harvester'];?></td>
    </tr> <?php } ?>      
        
    	<?php if(($temparr1['leader']) <> ($temparr2['leader'])) { ?>
	<tr>
 		<td>leader</td>
        <td><?php echo $temparr1['leader'];?></td>
        <td><?php echo $temparr2['leader'];?></td>
    </tr> <?php } ?>      
        
    	<?php if(($temparr1['scout']) <> ($temparr2['scout'])) { ?>
	<tr>
 		<td>intake</td>
        <td><?php echo $temparr1['scout'];?></td>
        <td><?php echo $temparr2['scout'];?></td>
    </tr> <?php } ?>      
        
	<?php // look up harvest histories of both 
	$tempharv1 = $temparr1['ID_picker'];
	
	$query_rsHarvests1 = sprintf("SELECT ID_picker FROM rosters WHERE ID_picker = $tempharv1");
	$rsHarvests1 = mysqli_query($piercecty, $query_rsHarvests1) or die(mysqli_error($piercecty));
	$picked1 = mysqli_num_rows($rsHarvests1);
	
	$tempharv2 = $temparr2['ID_picker'];
	
	$query_rsHarvests2 = sprintf("SELECT ID_picker FROM rosters WHERE ID_picker = $tempharv2");
	$rsHarvests2 = mysqli_query($piercecty, $query_rsHarvests2) or die(mysqli_error($piercecty));
	$picked2 = mysqli_num_rows($rsHarvests2); ?>

	<tr><td>harvests</td><td><?php echo $picked1 ?></td><td><?php echo $picked2 ?></td></tr>
             
    <tr><th colspan = 3>&nbsp;</th></tr>
        <?php } // end of one pair of pickers so shift arrays and look for the next pair
  
  		$temparr1 = $temparr2;
	    $temparr2 = mysqli_fetch_assoc($rsName);
         } // done with all pickers ?> 
     
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
((mysqli_free_result($rsName) || (is_object($rsName) && (get_class($rsName) == "mysqli_result"))) ? true : false);
?>
