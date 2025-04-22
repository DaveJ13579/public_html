<?php require_once('../Connections/piercecty.php'); 
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../includes/sqlcleaner.php');

$distsite = "-1";
if (isset($_GET['distsitetemp']) and $_GET['distsitetemp']<>'') { $distsite = $_GET['distsitetemp']; }
if(isset($_GET['distsitedrop']) and $_GET['distsitedrop']<>'') { $distsite = $_GET['distsitedrop']; }

if(isset($_POST['delete'])) { 
   $distsite=$_POST['distsite']; 
	$deleteq="delete from distsites where distsite=$distsite";
	$Result0 = mysqli_query($piercecty, $deleteq) or die(mysqli_error($piercecty));
	}

if(isset($_POST['new'])) {
$insertSQL = "INSERT INTO distsites (name) VALUES ('--NEW DISTRIBUTION SITE--')";
$Result1 = mysqli_query($piercecty, $insertSQL) or die(mysqli_error($piercecty));
$newdistsiteq="select distsite from distsites where name='--NEW DISTRIBUTION SITE--'";
$Result2 = mysqli_query($piercecty, $newdistsiteq) or die(mysqli_error($piercecty));
$siterow=mysqli_fetch_assoc($Result2);

$newdistsite=$siterow['distsite'];
$details="distributionsitesmanager.php?distsitetemp=".$newdistsite;
header("Location: $details"); exit(); }

$distsiteq = "SELECT * FROM distsites WHERE distsite = $distsite";
$rsDistsite = mysqli_query($piercecty, $distsiteq)  or die(mysqli_error($piercecty));
$distsiterow = mysqli_fetch_assoc($rsDistsite);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "distsiteupdateform") &&  $_POST['distsite']<>'') {
$distsite=$_POST['distsite']; 
	
$updateSQL = sprintf("UPDATE distsites SET name=%s, distsitetype=%s, active=%s, address=%s, city=%s, zip=%s, contact=%s, phone=%s, email=%s, hours=%s, notes=%s, EIN=%s where distsite=$distsite" ,
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['distsitetype'], "text"),
                       GetSQLValueString($_POST['active'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['zip'], "text"),
                       GetSQLValueString($_POST['contact'], "text"),
                       GetSQLValueString($_POST['phone'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['hours'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['EIN'], "text"));
                       
// echo $updateSQL;  
  $Result1 = mysqli_query($piercecty, $updateSQL) or die(mysqli_error($piercecty));

  $updateGoTo = "distributionsitesmanager.php?distsitetemp=$distsite";
  header(sprintf("Location: %s", $updateGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Distribution sites manager</title>
<link href="../database.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH">
<div id="container">
<?php require_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
  <h2>Distribution sites manager</h2>
      <form action="distributionsitesmanager.php" method="get" name="filtersform">
         Select site:
		<select name="distsitedrop">
             <option value="" selected="selected"></option>
              <?php $distsitesq="select distsite, name from distsites order by name";
                    $rsDistsites=mysqli_query($piercecty, $distsitesq) or die(mysqli_error($piercecty));
                    while ($droprow=mysqli_fetch_assoc($rsDistsites)) { ?>
              <option value="<?php echo $droprow['distsite']; ?>" <?php if($droprow['distsite']==$distsite) echo 'selected="selected" '; ?>><?php echo $droprow['name'];?></option>
	 		  <?php } ?>
        </select>
      <input type="submit" name="submit" id="submit" value="Show details" />
      </p>
      </form>   
<form action="distributionsitesmanager.php" name="distsiteupdateform" method="POST">

<?php if($distsite>0) {  ?>
 <table border="2" cellpadding="15px" cellspacing="5">
 <tr>
<th><?php echo $distsiterow['distsite']; ?></th><th><input name="distsite" type="hidden" value="<?php echo $distsiterow['distsite']; ?>" />
<input name="name" type="text" value="<?php echo $distsiterow['name']; ?>" size="50" maxlength="70" style="font-size:large;text-align:center;font-weight:bold;"/></th>
<td><label><input type="submit" name="submit" id="submit" value="Save changes" /></label>
<input type="hidden" name="MM_update" value="distsiteupdateform" />
</td>
</tr>
</table>
<br />
<table border="2" cellpadding="5" cellspacing="2">
<tr>
    <th>Address</th>
    <th>City</th>
    <th>Zip</th>
	<th>Type</th>
	<th>EIN</th>
	<th>Active?</th>
</tr> 
<tr>
<td><input name="address" type="text" value="<?php echo $distsiterow['address']; ?>" size="40"maxlength="50" /></td>
<td><input name="city" type="text" value="<?php echo $distsiterow['city']; ?>" size="40" maxlength="40" /></td>
<td><input name="zip" type="text" value="<?php echo $distsiterow['zip']; ?>" size="5" maxlength="5" /></td>
<td> <select name="distsitetype" id="distsitetype" onfocus="hints(this)">
     		<option value="<?php echo $distsiterow['distsitetype'] ? $distsiterow['distsitetype'] : 'No info'; ?>" selected="selected "><?php echo $distsiterow['distsitetype'] ? $distsiterow['distsitetype'] : 'No info';?></option>
					<?php 
					$htypeq="select distsitetype from distsitetypes order by distsitetype";
					$rsHtypes=mysqli_query($piercecty,$htypeq);
					while($htypesdrop=mysqli_fetch_assoc($rsHtypes)) { ?>          			
            		<option value="<?php echo $htypesdrop['distsitetype']; ?>" <?php if($distsiterow['distsitetype']==$htypesdrop['distsitetype']) echo 'selected="selected"'; ?>><?php echo $htypesdrop['distsitetype'];?></option>
            		<?php } ?>
 </select>
</td>
<td><input name="EIN" type="text" value="<?php echo $distsiterow['EIN']; ?>" size="12" maxlength="12" /></td>
<td><select name="active">
  <option value="" <?php echo 'selected="selected"';?>></option>
  <option value="Yes" <?php if($distsiterow['active']=='Yes') echo 'selected="selected"';?>>Yes</option>
  <option value="No" <?php if($distsiterow['active']=='No') echo 'selected="selected"';?>>No</option>
  </select></td>
</tr>
</table>
<br />
 <table border="2" cellpadding="5" cellspacing="5" width="1100">
<tr><th>Contact</th>
<td><textarea name="contact" cols="130" rows="3"><?php echo $distsiterow['contact']; ?></textarea></td>
</tr>
<tr><th>Phone</th>
<td><textarea name="phone" cols="130" rows="3"><?php echo $distsiterow['phone']; ?></textarea></td>
</tr>
<tr><th>Hours</th>
<td><textarea name="hours" cols="130" rows="3"><?php echo $distsiterow['hours']; ?></textarea></td>
</tr>
<tr><th>Notes</th>
<td><textarea name="notes" cols="130" rows="3"><?php echo $distsiterow['notes']; ?></textarea></td>
</tr>
</table>
<br />
  <p><input name="delete" type="submit" value="Delete this distribution site" id="delete" onclick="if (! confirm('Be very sure that you want to delete this distribution site. If there has ever been a harvest using this site, it should not be deleted. This is not reversible.')) return false;" />&nbsp;&nbsp;&nbsp;
  <?php } ?>
  <input name="new" type="submit" value="New distribution site" id="new" onclick="if (! confirm('Continuing will add a new site and immediately go to the Distribution Site Manager page for the new site where you can change the site information.')) return false;" /></p>
  </form>
	
<?php 
if(isset($_GET['sort'])) 
{ 
	$sort=$_GET['sort'];
	$direct=$_GET['direct'];
	if($direct=='ASC') { $direct='DESC'; } else { $direct='ASC'; }
}
 else { 
	 $sort='date';
 	$direct='DESC'; 
}
switch($sort) {
	case 'date': 		$orderby="d_date $direct, name $direct"; 		break;
	case 'name': 		$orderby="name $direct, d_date $direct"; 		break;
	default: 		$orderby="d_date $direct, name $direct"; 		break;
}	
	
$q="select ID_harvest, d_date, name, pounds from crops, distributions where crops.ID_crop=distributions.ID_crop and distsite=$distsite order by $orderby";
// echo $q.'<br />';
$rsDists=mysqli_query($piercecty, $q) or die(mysqli_error($piercecty));
?>
<div style="width:450px;float:left;">
<table width="400px" border="1">
<tr class="center_cell">
	<th>Harvest ID</th>
	<th><a href="distributionsitesmanager.php?distsitetemp=<?php echo $distsite;?>&sort=date&amp;direct=<?php echo $direct; ?>">Distribution date</th>
   <th><a href="distributionsitesmanager.php?distsitetemp=<?php echo $distsite;?>&sort=crop&amp;direct=<?php echo $direct; ?>">Crop</th>
	<th>Pounds</th>
</tr>
<?php while($row=mysqli_fetch_assoc($rsDists)) { ?>
<tr>
<td><a href="distributions.php?harvesttemp=<?php echo $row['ID_harvest'];?>" target="_blank"><?php echo $row['ID_harvest'];?></a></td>
<td><?php echo $row['d_date']; ?></td>
<td><?php echo $row['name']; ?></td>
<td  class="centercell"><?php echo $row['pounds']; ?></td></tr>
<?php } ?>
</table>	
</div> <!-- end of left div -->
<?php 
$q2="select name, sum(pounds) as pounds from distributions, crops  where distributions.ID_crop=crops.ID_crop and distsite=$distsite group by crops.ID_crop order by name";
$rsCrops=mysqli_query($piercecty, $q2) or die(mysqli_error($piercecty)); 
?>
<div style="width:450px;float:left;">
<table width="400px" border="1">
<tr><th>Crop</th><th>Total pounds</th></tr>
<?php while($row=mysqli_fetch_assoc($rsCrops)) {
?> <tr><td><?php echo $row['name'];?></td><td class="centercell"><?php echo $row['pounds'];?></td></tr>
<?php	} ?>
<tr><th>ALL CROPS</th><th>
	<?php $q3="select sum(pounds) as pounds from distributions where distsite=$distsite";
					$rsTotal=mysqli_query($piercecty,$q3);
					$totalrow=mysqli_fetch_assoc($rsTotal);
					echo $totalrow['pounds'];
	?>
</th></tr>

</table>
</div>
</div>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
