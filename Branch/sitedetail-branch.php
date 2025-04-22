<?php require_once('../Connections/piercecty.php'); 
$MM_authorizedUsers = "all,change,view,branch";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

$ldrbranch=isset($_SESSION['ldrbranch']) ? $_SESSION['ldrbranch'] : 'not assigned';

//echo '<br />'.$ldrbranch;
//echo '<br />'.$_POST['branch'];

$branch='All'; // default values
$fbranch='';
if($ldrbranch<>'not assigned' && !isset($_POST['branch'])) { // if user is a branch leader  and no POSTED branch
	$branch=$ldrbranch;
	$fbranch=" and branch='$branch' ";
} 
if(isset($_POST['branch']) && $_POST['branch']<>'All') { // POST from branch selector overrides $ldrbranch
	$branch=$_POST['branch'];
	$fbranch=" and branch='$branch' ";
} 

$site = "-1";
if (isset($_GET['sitetemp']) and $_GET['sitetemp']<>'') { $site = $_GET['sitetemp']; }
if(isset($_GET['sitedrop']) and $_GET['sitedrop']<>'') { $site = $_GET['sitedrop']; }

$siteq = "SELECT * FROM sites WHERE ID_site = $site";
$rsSite = mysqli_query($piercecty, $siteq)  or die(mysqli_error($piercecty));
$siterow = mysqli_fetch_assoc($rsSite);
// site harvest history
$harvestsq = "SELECT ID_leader, harvests.ID_harvest, h_date, totwgt, pick_num, harvests.otherinfo, lname, fname FROM harvests, pickers WHERE ID_site=$site and pickers.ID_picker=harvests.ID_leader order by h_date desc";
$rsharvests = mysqli_query($piercecty, $harvestsq)  or die(mysqli_error($piercecty));
$harvestsrow = mysqli_fetch_assoc($rsharvests);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>site detail</title>
<link href="../database.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav-branch.inc.php');?>
<div id="mainContent">
<p><span class="pagehead">Branch Site details</span> <a href="siteupdate-branch.php?sitetemp=<?php echo $site; ?>">Go to site update</a></p>
<form action="" method="post" name="branchform">
Branch <select name="branch">
	<option value="All" <?php if($branch=='All') echo 'selected="selected"';?>>All</option>
	<?php 
	$branchq="select branch as branchitem from branches order by branch";
	$rsBranch=mysqli_query($piercecty, $branchq);
	while($branchrow=mysqli_fetch_assoc($rsBranch)) {
	extract($branchrow); ?>
	<option value="<?php echo $branchitem;?>" <?php if($branchitem==$branch) echo 'selected="selected"';?>><?php echo $branchitem;?></option>";
<?php	} ?>
</select>
<input name="submit" type="submit" value="Select branch" />
 </form>

<form action="sitedetail-branch.php" method="get" name="filtersform">
      <p>Enter site ID:
        <input name="sitetemp" type="text" size="4" id="temp1" value="<?php if(isset($site) and $site>0) echo $site;?>" />
         or select from list:
		<select name="sitedrop">
             <option value="" selected="selected"></option>
              <?php $sitesq="select ID_site, farm, address, city, crops from sites where Active='Yes' $fbranch order by farm";
                    $rsSites=mysqli_query($piercecty, $sitesq) or die(mysqli_error($piercecty));
                    while ($droprow=mysqli_fetch_assoc($rsSites)) { ?>
              <option value="<?php echo $droprow['ID_site']; ?>" <?php if($droprow['ID_site']==$site) echo 'selected="selected" '; ?>><?php echo $droprow['farm'].", ".$droprow['address'].", ".$droprow['city'].", ".substr($droprow['crops'],0,30);?></option>
	 		  <?php } ?>
        </select>
      <input type="submit" name="submit" id="submit" value="Show details" />
      </p>
      </form>  
<br />
  <table border="2" cellpadding="5" cellspacing="3" width="900px" align="center">
    <tr>
      <th>Farm name</th>
      <th>Address</th>
      <th>City</th>
      <th>State</th>
      <th>Zip</th>
      <th>Branch</th>
    </tr>
    <tr>
      <td  style="font-size:large;text-align:center;font-weight:bold;"><?php echo $siterow['farm']; ?></td>
      <td><?php echo $siterow['address']; ?></td>
      <td><?php echo $siterow['city']; ?></td>
      <td><?php echo $siterow['state']; ?></td>
      <td><?php echo $siterow['zip']; ?></td>
       <td><?php echo $siterow['branch']; ?></td>
   </tr>
   <tr>
	 <td class="blankcell"> </td>
    <th>General area</th>
    <th>Venue</th>
    <th>Active?</th>
    <th>Authorization<br />Date</th>
   </tr>
   <tr>
  <td class="blankcell"> </td>
     <td><?php echo $siterow['region']; ?></td>
     <td><?php echo $siterow['venue']; ?></td>
       <td><?php echo $siterow['Active']; ?></td>
      <td><?php echo $siterow['authdate']; ?></td>
  </tr>
   </table>
   <br />
       <table border="2" cellpadding="5" cellspacing="3"  width="900px" align="center">
   <tr><th style="width:20%">Crops</th><td><?php echo $siterow['crops']; ?></td></tr>
	<tr><th style="width:20%">Other information</th><td><?php echo $siterow['otherinfo']; ?></td></tr>
  </table>
  <br />
   <table border="2" cellpadding="5" cellspacing="3"  width="900px" align="center">
    <tr>
      <th>Height</th>
      <th>Size</th>
      <th>Location</th>
    </tr>
    <tr>
      <td><?php echo $siterow['height']; ?></td>
     <td><?php echo $siterow['size']; ?></td>
     <td><?php echo $siterow['location']; ?></td>
    </tr>
    <tr>
      <th>Disease</th>
      <th>Spray</th>
      <th>Ripe</th>
    </tr>
    <tr>
      <td><?php echo $siterow['disease'].' - '.$siterow['disease_text']; ?> </td>
      <td><?php echo $siterow['spray'].' - '.$siterow['spray_text']; ?></td>
       <td><?php echo $siterow['when_ripe']; ?></td>
    </tr>
 </table>
 <br />
   <table border="2" cellpadding="5" cellspacing="3"  width="900px" align="center">
    <tr>
      <th>Contact</th>
      <th>Phone</th>
      <th>Email</th>
    </tr>
    <tr>
      <td><?php echo $siterow['contact1']; ?> </td>
      <td><?php echo $siterow['phone1']; ?></td>
      <td><?php echo $siterow['email1']; ?></td>
    </tr>
    <tr>
      <td><?php echo $siterow['contact2']; ?> </td>
      <td><?php echo $siterow['phone2']; ?></td>
      <td><?php echo $siterow['email2']; ?></td>
    </tr>
    <tr>
      <td><?php echo $siterow['contact3']; ?> </td>
      <td><?php echo $siterow['phone3']; ?></td>
      <td><?php echo $siterow['email3']; ?></td>
	</tr>
 </table>
 <br />
   <table border="2" cellpadding="5" cellspacing="3" width="900px" align="center">
    <tr>
      <th>Mailing address</th>
      <th>Mailing city</th>
      <th>Mailing state</th>
      <th>Mailing zip</th>
      </tr>
    <tr>
      <td><?php echo $siterow['maddress']; ?> </td>
      <td><?php echo $siterow['mcity']; ?></td>
      <td><?php echo $siterow['mstate']; ?></td>
      <td><?php echo $siterow['mzip']; ?></td>
	</tr>
  <tr>
      <tr>
    <th>Owner?</th>
    <th>Present for harvests</th>
    <th>Registration date</th>
  	  </tr>
    <tr>
      <td><?php echo $siterow['property_rel'].' - '.$siterow['landlord']; ?></td>
      <td><?php echo $siterow['present'];  ?></td>
      <td><?php echo $siterow['regdate']; ?></td>
</tr>
      <tr>
    <th>How did you hear?</th>
    <th>Website</th>
  	  </tr>
    <tr>
      <td><?php echo $siterow['howhear']; ?></td>
      <td><?php echo $siterow['website']; ?></td>
</tr>
    </table>
    <br />
  <?php
$addressstring=urlencode($siterow['address'].', '.$siterow['city'].', '.$siterow['state']);
$src="https://maps.google.com/maps?q=$addressstring&output=embed&z=17&t=1";
?>
  <table width="900" border="4" align="center">
    <tr>
      <td align="center"><iframe width="800" height="400" scrolling="no" frameborder="no" src="<?php echo $src; ?>"></iframe></td>
    </tr>
  </table>
<br />
<table width="900" border="2" cellspacing="10" cellpadding="2" align="center">
<tr align="center"><td colspan="5" ><strong>Harvest history</strong></td></tr>
    <tr>
      <th>Date</th>
      <th>Leader</th>
      <th>Pickers</th>
      <th>Total weight</th>
      <th>Other</th>
    </tr>
    <?php do { ?>
    <tr>
      <td align="center"><a href="harvestroster-branch.php?harvesttemp=<?php echo $harvestsrow['ID_harvest']; ?>"><?php echo $harvestsrow['h_date']; ?></a></td>
      <td><?php echo $harvestsrow['fname']; ?> <?php echo $harvestsrow['lname']; ?></td>
      <td align="center"><?php echo $harvestsrow['pick_num']; ?></td>
      <td align="center"><?php echo $harvestsrow['totwgt']; ?></td>
      <td><?php echo $harvestsrow['otherinfo']; ?></td>
    </tr>
    <?php } while ($harvestsrow = mysqli_fetch_assoc($rsharvests));?>
  </table>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
</div>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
<?php
((mysqli_free_result($rsSite) || (is_object($rsSite) && (get_class($rsSite) == "mysqli_result"))) ? true : false);
((mysqli_free_result($rsharvests) || (is_object($rsharvests) && (get_class($rsharvests) == "mysqli_result"))) ? true : false);
?>
