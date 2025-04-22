<?php 
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
include_once('../includes/converter.inc.php');
$MM_authorizedUsers = "all,change,branch";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

// get passed in harvest number
$ID_harvest=0; $harvestq="SELECT * FROM harvests WHERE ID_harvest = 0"; 

if(isset($_GET['harvesttemp']) and $_GET['harvesttemp']<>'') { // passed in harvest number from another page
	$ID_harvest =  $_GET['harvesttemp'];
	$harvestq= "SELECT * FROM harvests WHERE ID_harvest = $ID_harvest";
	}

if(isset($_POST['ID_harvest']) and $_POST['ID_harvest']<>'') { // posted from the  harvest distribution form on this page
	$ID_harvest =  $_POST['ID_harvest'];
	}

// NOW HAVE THE harvest NUMBER (or 0) REGARDLESS OF NEW OR PREVIOUS OR RECENT OR UPDATE

// links to other pages
if(isset($_POST['harvestupdate'])) {
	if($_SESSION['MM_UserGroup']=='branch') {
		$updatepage="../Branch/harvestupdate-branch.php?harvesttemp=".$ID_harvest; }
		else { $updatepage="harvestupdate.php?harvesttemp=".$ID_harvest; }
	header("Location: $updatepage"); exit(); }
// Update the distributions
//print_r($_POST); 

if(isset($_POST["save"])) { // distribution update form submitted
if(isset($_POST['distributions'])) $distributions=$_POST['distributions'];

// process the donations     $distributions[][ID_dist], $distributions[][d_date], $distributions[][ID_crop], $distributions[][pounds], $distributions[][distsite]
//echo '<pre>';print_r($distributions);

foreach($distributions as $distribution) {
	if(isset($distribution['ID_dist']) and $distribution['ID_dist']<>'' and $distribution['pounds']<1) { //delete distribution
		$deleteq="delete from distributions where ID_dist=".$distribution['ID_dist'];
		$rsDeleteq=mysqli_query($piercecty, $deleteq);
	} elseif(isset($distribution['ID_dist']) and $distribution['ID_dist']>0 and strlen($distribution['d_date'])==10) { // update distribution
		$updateq="update distributions set d_date='".$distribution['d_date']."', ID_crop=".$distribution['ID_crop'].", pounds=".$distribution['pounds'].", distsite=".$distribution['distsite']." where ID_dist=".$distribution['ID_dist'];
		$rsUpdateq=mysqli_query($piercecty, $updateq);
	} elseif($distribution['ID_dist']=='' and $distribution['ID_crop']<>'' and $distribution['pounds']<>'' and $distribution['distsite']<>'') { // insert distribution
		$insertq="insert into distributions (d_date, ID_harvest, ID_crop, pounds, distsite) values ('".$distribution['d_date']."', ".$ID_harvest.", ".$distribution['ID_crop'].", ".$distribution['pounds'].", ".$distribution['distsite'].")";
      //echo '<br />'.$insertq;
      
		$rsInsertq=mysqli_query($piercecty, $insertq) or die(mysqli_error($piercecty).'line 46');
	}
} // end of all distributions posted
} // end of process form

//PREPARE ALL FOR DISPLAY

// get all harvest info 
$harvestq= "SELECT * FROM harvests WHERE ID_harvest = $ID_harvest";
$rsharvest = mysqli_query($piercecty, $harvestq) or die(mysqli_error($piercecty));
$harvestrow = mysqli_fetch_assoc($rsharvest);
if($harvestrow) extract($harvestrow);

// get all associated distributions
$distributionsq="select * from distributions where ID_harvest=$ID_harvest";
$rsDonations = mysqli_query($piercecty, $distributionsq) or die(mysqli_error($piercecty));

// set up distribution sites dropdown
$distsitesq="select distsite, name from distsites order by name";
$rsDistsites = mysqli_query($piercecty, $distsitesq) or die(mysqli_error($piercecty));
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>harvest distributions</title>
    <style type="text/css">
<!--
#hints {
	width:700px;
	height:70px;
	float:right;
	margin-right:300px;
	border:1px solid #000;
	padding:3px;
}
#add {
width:200px;
float:left;
height:70px;}
.cropdrop {width:200px;}
-->
</style>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="../datepick/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="../datepick/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"d_date",
			dateFormat:"%Y-%m-%d",
			yearsRange:[2020,2030],
			cellColorScheme:"armygreen",
			imgPath:"img/",
			weekStartDay:0
		});
		}
</script>
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php'); ?>
<div id="mainContent">
    <h2><strong>Harvest distributions</strong></h2>
<form action="" method="get" name="filtersform">
	Harvest number 
		<input size="7" maxlength="5" type="text" name="harvesttemp" id="harvesttemp" value="<?php echo $ID_harvest ?>"/>
<input type="submit" name="showharvest" value="Display this harvest's distributions" />
</form>
<?php // get some harvest info for heading
$hq="select farm, h_date, totwgt from harvests, sites, pickers where sites.ID_site=harvests.ID_site and harvests.ID_harvest=$ID_harvest";
$rsH=mysqli_query($piercecty, $hq);
$hrow=mysqli_fetch_assoc($rsH);
?>
<br /><span style="font-size: 1.5em"><?php echo $hrow['farm'].', '.$hrow['h_date'].', total weight: '.$hrow['totwgt'].' pounds';?></span>

<form action="distributions.php?harvesttemp=<?php echo $ID_harvest;?>" id="updateform" name="updateform" method="POST" onsubmit="return validateForm()">
 <br />
 <table border="1" cellpadding="1" cellspacing="1">
  <?php

// get all donations from this harvest
$donationsq="select * from donations where ID_harvest=$ID_harvest";
$rsDonationsq=mysqli_query($piercecty, $donationsq) or die(mysqli_error($piercecty));
$x=0;
// Outside loop for the crop types - for each donation (crop), show the crop name and total weight and pounds remaining
while($donrow=mysqli_fetch_assoc($rsDonationsq)) { 
   extract($donrow); 
   // calculate pounds already distributed of this crop donation
   $poundsdistq="select sum(pounds) as poundsdist from distributions where  ID_harvest=$ID_harvest and ID_crop=".$ID_crop;
   $rsPoundsdist=mysqli_query($piercecty, $poundsdistq);
   $poundsdistrow=mysqli_fetch_assoc($rsPoundsdist); 
   ?>
   <tr>
<th style="font-size: 1.3em;"><?php echo cropname($ID_crop).': '.$pounds.' pounds'; ?></th>
 <th style="font-size: 1.3em;" colspan="3"><?php echo $pounds-$poundsdistrow['poundsdist']; ?> pounds remaining to distribute</th>
   </tr>
<?php 

// Then list all distributions from that harvest of that crop (date, pounds, distsite)
$cropdistsq="select * from distributions where ID_harvest=$ID_harvest and ID_crop=$ID_crop";
$rsCropsdist=mysqli_query($piercecty, $cropdistsq);
while($cropdistrow=mysqli_fetch_assoc($rsCropsdist)) { ?>
<tr>
       <td>Date (yyyy-mm-dd): 
         <input type="text" name="distributions[<?php echo $x;?>][d_date]" value="<?php echo $cropdistrow['d_date'];?>"></td>
       <td>Pounds: <input type="text" name="distributions[<?php echo $x;?>][pounds]" value="<?php echo $cropdistrow['pounds'];?>"></td>
       <td>Distribution site: 
             <select name="distributions[<?php echo $x;?>][distsite]" class="cropdrop">
              <option value=""  <?php if($cropdistrow['distsite']=='') echo 'selected="selected"';?>> - </option>
              <?php mysqli_data_seek($rsDistsites,0); 
              while($distsitesrow = mysqli_fetch_assoc($rsDistsites)) { ?>
             <option value="<?php echo $distsitesrow['distsite'];?>" 
             <?php if($distsitesrow['distsite']==$cropdistrow['distsite']) echo 'selected="selected"';?>><?php echo $distsitesrow['name'];?></option>
             <?php } ?>
              </select>
         <input type="hidden" name="distributions[<?php echo $x;?>][ID_dist]"  value="<?php echo $cropdistrow['ID_dist'];?>">
         <input type="hidden" name="distributions[<?php echo $x;?>][ID_crop]"  value="<?php echo $ID_crop;?>">
         </td>
</tr>
<?php 
++$x; } 
// Then three blank rows of distributions of that crop to fill if needed for other distsites

if($pounds-$poundsdistrow['poundsdist']>0) { // if there are remaining pounds to account for
  for($y=1;$y<=3;++$y) { // do 3 blank slots to enter distributions
  ?>
<tr>
       <td>Date (yyyy-mm-dd): 
         <input type="text" name="distributions[<?php echo $x;?>][d_date]" value="<?php echo date('Y-m-d');?>"></td>
       <td>Pounds: <input type="text" name="distributions[<?php echo $x;?>][pounds]" value="0"></td>
       <td>Distribution site: <select name="distributions[<?php echo $x;?>][distsite]" class="cropdrop">
        <option value=""  selected="selected"> - </option>
	     <?php mysqli_data_seek($rsDistsites,0); 
        while($distsitesrow = mysqli_fetch_assoc($rsDistsites)) { ?>
        	<option value="<?php echo $distsitesrow['distsite'];?>"><?php echo $distsitesrow['name'];?></option>
        <?php } ?>
        </select>       
        <input type="hidden" name="distributions[<?php echo $x;?>][ID_dist]"  value="">
        <input type="hidden" name="distributions[<?php echo $x;?>][ID_crop]"  value="<?php echo $ID_crop;?>">
        </td>
</tr>
<?php 
++$x; }
} // end of if there are remaining pounds

} // end of all stored donations

 ?>
<tr><td><input name="save" type="submit" value="Save distributions"></td><td><input type="submit" name="harvestupdate" value="Back to Harvest Update"></td></tr>
 </table>
</form>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</div>
</body>
</html>
