<?php require_once('../Connections/piercecty.php'); 
$MM_authorizedUsers = "all,change,branch";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../includes/sqlcleaner.php');
require_once('../includes/branch.inc.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) { $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']); }

$site = "-1";
if (isset($_GET['sitetemp']) and $_GET['sitetemp']<>'') { $site = $_GET['sitetemp']; }
if(isset($_GET['sitedrop']) and $_GET['sitedrop']<>'') { $site = $_GET['sitedrop']; }

if(isset($_POST['delete'])) {
	$details="sitedelete.php?sitetemp=".$site;
	header("Location: $details"); exit(); }

if(isset($_POST['copy'])) {
	$query="drop temporary table if exists tmp";
	$result = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
	$query="create temporary table tmp select * from sites where ID_site=$site";
	$result = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
	$query="update tmp set ID_site=NULL";
	$result = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
	$query="insert into sites select * from tmp";
	$result = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
	$query="select max(ID_site) as ID_site from sites";
	$result = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
	$maxrow=mysqli_fetch_assoc($result);
	$newsite=$maxrow['ID_site'];
	$dateq="update sites set regdate=current_date() where ID_site=$newsite";
	$rsDate=mysqli_query($piercecty,$dateq);
	$details="siteupdate-branch.php?sitetemp=".$newsite;
	header("Location: $details"); exit(); }

$siteq = "SELECT * FROM sites WHERE ID_site = $site";
$rsSite = mysqli_query($piercecty, $siteq)  or die(mysqli_error($piercecty));
$siterow = mysqli_fetch_assoc($rsSite);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "siteupdateform") &&  $_POST['ID_site']<>'') {

$branch=zipbranch(GetSQLValueString($_POST['zip'], "text"));
	
$updateSQL = sprintf("UPDATE sites SET farm=%s, location=%s, branch='$branch', crops=%s, size=%s, region=%s, property_rel=%s, landlord=%s, present=%s, height=%s, when_ripe=%s, disease=%s, disease_text=%s, spray=%s, spray_text=%s, contact1=%s, phone1=%s, email1=%s, contact2=%s, phone2=%s, email2=%s, contact3=%s, phone3=%s, email3=%s, maddress=%s, mcity=%s, mstate=%s, mzip=%s, address=%s, city=%s, state=%s, zip=%s, website=%s, howhear=%s, otherinfo=%s, venue=%s, authdate=%s, Active=%s, regdate=%s  WHERE ID_site=$site",
                       GetSQLValueString($_POST['farm'], "text"),
                       GetSQLValueString($_POST['location'], "text"),
                       GetSQLValueString($_POST['crops'], "text"),
                       GetSQLValueString($_POST['size'], "text"),
                       GetSQLValueString($_POST['region'], "text"),
                       GetSQLValueString($_POST['property_rel'], "text"),
                       GetSQLValueString($_POST['landlord'], "text"),
                       GetSQLValueString($_POST['present'], "text"),
                       GetSQLValueString($_POST['height'], "text"),
                       GetSQLValueString($_POST['when_ripe'], "text"),
                       GetSQLValueString($_POST['disease'], "text"),
                       GetSQLValueString($_POST['disease_text'], "text"),
                       GetSQLValueString($_POST['spray'], "text"),
                       GetSQLValueString($_POST['spray_text'], "text"),
                       GetSQLValueString($_POST['contact1'], "text"),
                       GetSQLValueString($_POST['phone1'], "text"),
                       GetSQLValueString($_POST['email1'], "text"),
                       GetSQLValueString($_POST['contact2'], "text"),
                       GetSQLValueString($_POST['phone2'], "text"),
                       GetSQLValueString($_POST['email2'], "text"),
                       GetSQLValueString($_POST['contact3'], "text"),
                       GetSQLValueString($_POST['phone3'], "text"),
                       GetSQLValueString($_POST['email3'], "text"),
                       GetSQLValueString($_POST['maddress'], "text"),
                       GetSQLValueString($_POST['mcity'], "text"),
                       GetSQLValueString($_POST['mstate'], "text"),
                       GetSQLValueString($_POST['mzip'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['zip'], "text"),
                       GetSQLValueString($_POST['website'], "text"),
                       GetSQLValueString($_POST['howhear'], "text"),
                       GetSQLValueString($_POST['otherinfo'], "text"),
                       GetSQLValueString($_POST['venue'], "text"),
                       GetSQLValueString($_POST['authdate'], "date"),
                       GetSQLValueString($_POST['Active'], "text"),
					   GetSQLValueString($_POST['regdate'], "text")	);
  $Result1 = mysqli_query($piercecty, $updateSQL) or die(mysqli_error($piercecty));

  $updateGoTo = "siteupdate-branch.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$ldrbranch=isset($_SESSION['ldrbranch']) ? $_SESSION['ldrbranch'] : 'not assigned';

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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>site update</title>
<link href="../database.css" rel="stylesheet" type="text/css" />
<?php include('../includes/branchhelp.inc.php'); ?>
</head>

<body class="SH">
<div id="container">
<?php require_once('../includes/AdminNav-branch.inc.php');?>
<div id="mainContent">
<a href="../help/help.php"  onClick="wopen('../help/help.php', 'popup', 640, 480); return false;"><div class="branchhelp">Page help</div></a>
<p><span class="pagehead">Branch Site update</span> <a href="sitedetail-branch.php?sitetemp=<?php echo $site; ?>">Go to site details</a></p>
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

<form action="siteupdate-branch.php" method="get" name="filtersform">
      <p>Enter site ID:
        <input name="sitetemp" type="text" size="4" id="temp1" value="<?php if(isset($site) and $site>0) echo $site;?>" />
         or select from list:
		<select name="sitedrop">
             <option value="" selected="selected"></option>
              <?php $sitesq="select ID_site, farm, address, city, crops from sites where Active='Yes' $fbranch order by farm";
                    $rsSites=mysqli_query($piercecty, $sitesq) or die(mysqli_error($piercecty));
                    while ($droprow=mysqli_fetch_assoc($rsSites)) { ?>
              <option value="<?php echo $droprow['ID_site']; ?>" <?php if($droprow['ID_site']==$site) echo 'selected="selected" '; ?>><?php echo $droprow['farm'].", ".$droprow['address'].", ".$droprow['city'].", ".substr($droprow['crops'],0,20);?></option>
	 		  <?php } ?>
        </select>
      <input type="submit" name="submit" id="submit" value="Show details" />
      </p>
      </form>    
<form action="<?php echo $editFormAction; ?>" name="siteupdateform" method="POST">
<br />
 <table border="2" cellpadding="5" cellspacing="5" width="1100" align="center">
  <tr><td><label><input type="submit" name="submit" id="submit" value="Save changes" /></label>
 <input type="hidden" name="MM_update" value="siteupdateform" />
  </td>
      <th>Address</th>
      <th>City</th>
      <th>State</th>
      <th>Zip</th>
     <th>Branch</th>
    </tr>
    <tr>
      <th ><input name="ID_site" type="hidden" value="<?php echo $siterow['ID_site']; ?>" />
      <input name="farm" type="text" value="<?php echo $siterow['farm']; ?>" maxlength="60" style="font-size:large;text-align:center;font-weight:bold;"/></th>
      <td><input name="address" type="text" value="<?php echo $siterow['address']; ?>" size="30" maxlength="80" /></td>
      <td><input name="city" type="text" value="<?php echo $siterow['city']; ?>" maxlength="80" /></td>
      <td><input name="state" type="text" value="<?php echo $siterow['state']; ?>" maxlength="2" /></td>
      <td><input name="zip" type="text" value="<?php echo $siterow['zip']; ?>" maxlength="5" /></td>
 <td><?php echo $siterow['branch'];?></td>
</tr>
<tr>
    <td  class="blankcell">&nbsp;</td>
    <th>General area</th>
    <th>Venue</th>
   	<th style="width:100px;">Active?</th>
    <th>Entry Authorization<br />Date</th>
</tr> 
<tr>
      <td  class="blankcell">&nbsp;</td>
      <td><input name="region" type="text" value="<?php echo $siterow['region']; ?>" maxlength="25" /></td>      
      <td><select name="venue">
                <option value="noinfo" <?php if($siterow['venue']=='noinfo') echo 'selected="selected"';?>>No info</option>
                <option value="Backyard" <?php if($siterow['venue']=='Backyard') echo 'selected="selected"';?>>Backyard</option>
                <option value="Farm" <?php if($siterow['venue']=='Farm') echo 'selected="selected"';?>>Commercial Farm</option>
                <option value="Pickup" <?php if($siterow['venue']=='Pickup') echo 'selected="selected"';?>>Pickup</option>
                <option value="Market" <?php if($siterow['venue']=='Market') echo 'selected="selected"';?>>Market</option>
              </select>
</td>
       <td><select name="Active">
                <option value="Yes" <?php if($siterow['Active']=='Yes') echo 'selected="selected"';?>>Yes</option>
                <option value="No" <?php if($siterow['Active']=='No') echo 'selected="selected"';?>>No</option>
              </select>
	</td>
     <td><input name="authdate" type="text" value="<?php echo $siterow['authdate']; ?>" maxlength="10" /></td>
</tr>
<tr>
</table>
<br />
 <table border="2" cellpadding="5" cellspacing="5" width="1100" align="center">
<th>Crops:</th>
    <td><input name="crops" type="text" size="150" colspan="4" value="<?php echo $siterow['crops']; ?>" maxlength="150"/></td>
	<tr><th>Other information</th>
    <td><textarea name="otherinfo" cols="110" rows="3"><?php echo $siterow['otherinfo']; ?></textarea></td>
    </tr>
  </table>
  <br />
 <table border="2" cellpadding="5" cellspacing="5" width="1100" align="center">
    <tr>
      <th>Height / Lowest</th>
      <th>Size</th>
      <th>Location</th>
    </tr>
    <tr>
      <td><input name="height" type="text" value="<?php echo $siterow['height']; ?>" size="15" maxlength="15" /></td>
      <td><input name="size" type="text" value="<?php echo $siterow['size']; ?>" size="20" maxlength="30" /></td>
      <td><input name="location" type="text" value="<?php echo $siterow['location']; ?>" size="20" maxlength="20" /></td>
    </tr>
    <tr>
      <th>Disease</th>
      <th>Spray</th>
     <th>Ripe (mm-dd)</th>
    </tr>
    <tr>
      <td><select name="disease">
                <option value="" <?php echo 'selected="selected"';?>></option>
                <option value="Yes" <?php if($siterow['disease']=='Yes') echo 'selected="selected"';?>>Yes</option>
                <option value="No" <?php if($siterow['disease']=='No') echo 'selected="selected"';?>>No</option>
              </select>&nbsp;
              <input name="disease_text" type="text" value="<?php echo $siterow['disease_text']; ?>" size="25"  maxlength="25" /></td>
      <td><select name="spray">
                <option value="" <?php echo 'selected="selected"';?>></option>
                <option value="Yes" <?php if($siterow['spray']=='Yes') echo 'selected="selected"';?>>Yes</option>
                <option value="No" <?php if($siterow['spray']=='No') echo 'selected="selected"';?>>No</option>
              </select>&nbsp;<input name="spray_text" type="text" value="<?php echo $siterow['spray_text']; ?>" size="30"  maxlength="30" /></td>
      <td><input name="when_ripe" type="text" value="<?php echo $siterow['when_ripe']; ?>" size="5" maxlength="5" /></td>
    </tr>
 </table>
 <br />
 <table border="2" cellpadding="5" cellspacing="5" width="1100" align="center">
    <tr>
      <th>Contact 1</th>
      <th>Phone 1</th>
      <th>Email 1</th>
    </tr>
    <tr>
      <td><input name="contact1" type="text" value="<?php echo $siterow['contact1']; ?>" size="50" maxlength="80" /></td>
      <td><input name="phone1" type="text" value="<?php echo $siterow['phone1']; ?>" size="30" maxlength="50" /></td>
      <td><input name="email1" type="text" value="<?php echo $siterow['email1']; ?>" size="30" maxlength="60" /></td>
    </tr>
    <tr>
      <th>Contact 2</th>
      <th>Phone 2</th>
     <th>Email 2</th>
    </tr>
    <tr>
      <td><input name="contact2" type="text" value="<?php echo $siterow['contact2']; ?>" size="50"  maxlength="80" /></td>
      <td><input name="phone2" type="text" value="<?php echo $siterow['phone2']; ?>" size="30" maxlength="50" /></td>
      <td><input name="email2" type="text" value="<?php echo $siterow['email2']; ?>" size="30" maxlength="60" /></td>
    </tr>
    <tr>
      <th>Contact 3</th>
      <th>Phone 3</th>
      <th>Email 3</th>
    </tr>
    <tr>
      <td><input name="contact3" type="text" value="<?php echo $siterow['contact3']; ?>" size="50"  maxlength="80" /></td>
      <td><input name="phone3" type="text" value="<?php echo $siterow['phone3']; ?>" size="30" maxlength="50" /></td>
      <td><input name="email3" type="text" value="<?php echo $siterow['email3']; ?>" size="30" maxlength="60" /></td>
	</tr>
</table>
<br />
 <table border="2" cellpadding="5" cellspacing="5" width="1100" align="center">
    <tr>
      <th>Mailing address</th>
      <th>Mailing city</th>
      <th>Mailing state</th>
      <th>Mailing zip</th>
   </tr>
    <tr>
      <td><input name="maddress" type="text" value="<?php echo $siterow['maddress']; ?>" size="50" maxlength="80" /></td>
      <td><input name="mcity" type="text" value="<?php echo $siterow['mcity']; ?>" maxlength="80" /></td>
      <td><input name="mstate" type="text" value="<?php echo $siterow['mstate']; ?>" maxlength="2" /></td>
      <td><input name="mzip" type="text" value="<?php echo $siterow['mzip']; ?>" maxlength="5" /></td>
	</tr>
 </table>
 <br />
 <table border="2" cellpadding="5" cellspacing="5" width="1100" align="center">
  <tr>
  	<th>Owner or?</th>
  	<th>Present for harvests</th>
  	<th>Registration date (yyyy-mm-dd)</th>
     <tr>
      <td><select name="property_rel">
                <option value="owner" <?php if($siterow['property_rel']=='owner') echo 'selected="selected"';?>>owner</option>
                <option value="landlord" <?php if($siterow['property_rel']=='landlord') echo 'selected="selected"';?>>landlord</option>
                <option value="renter" <?php if($siterow['property_rel']=='renter') echo 'selected="selected"';?>>renter</option>
                <option value="other" <?php if($siterow['property_rel']=='other') echo 'selected="selected"';?>>other</option>
              </select>&nbsp;<input name="landlord" type="text" value="<?php echo $siterow['landlord']; ?>" size="20" maxlength="40" /></td>
      <td><select name="present">
                <option value="Yes" <?php if($siterow['present']=='Yes') echo 'selected="selected"';?>>Yes</option>
                <option value="No" <?php if($siterow['present']=='No') echo 'selected="selected"';?>>No</option>
              </select></td>
      <td><input name="regdate" type="text" value="<?php echo $siterow['regdate']; ?>" maxlength="10" /></td>
</tr>
    </tr>
    <tr>
  	<th>How did you hear?</th>
    <th>Website</th>
  </tr>
<tr>
      <td>
          <select name="howhear" id="howhear">
            <option value="" selected="selected"></option>
            <option value="Neighbor" <?php if($siterow['howhear']=="Neighbor") echo 'selected="selected" '; ?>> Neighbor</option>
            <option value="Pierce County Gleaning Project web site" <?php if($siterow['howhear']=="Pierce County Gleaning Project web site") echo 'selected="selected" '; ?>>Harvest Pierce County's Gleaning Project Web Site</option>
            <option value="Food Bank" <?php if($siterow['howhear']=="Food Bank") echo 'selected="selected" '; ?>>Food Bank</option>
            <option value="Pierce County Gleaning Project volunteer" <?php if($siterow['howhear']=="Pierce County Gleaning Project volunteer") echo 'selected="selected" '; ?>>Harvest Pierce County's Gleaning Project volunteer</option>
            <option value="Newspaper" <?php if($siterow['howhear']=="Newspaper") echo 'selected="selected" '; ?>>Newspaper</option>
            <option value="Facebook" <?php if($siterow['howhear']=="Facebook") echo 'selected="selected" '; ?>>Facebook</option>
            <option value="Flyer" <?php if($siterow['howhear']=="Flyer") echo 'selected="selected" '; ?>>Flyer</option>
            <option value="Craigslist" <?php if($siterow['howhear']=="Craigslist") echo 'selected="selected" '; ?>>Craigslist</option>
            <option value="Other urban harvesting group" <?php if($siterow['howhear']=="Other urban harvesting group") echo 'selected="selected" '; ?>>Other urban harvesting group</option>
            <option value="Friend" <?php if($siterow['howhear']=="Friend") echo 'selected="selected" '; ?>>Friend</option>
            <option value="Other" <?php if($siterow['howhear']=="Other") echo 'selected="selected" '; ?>>Other</option>
            <option value="Web search" <?php if($siterow['howhear']=="Web search") echo 'selected="selected" '; ?>>Web search</option>
          </select>
	  </td>
      <td><input name="website" type="text" value="<?php echo $siterow['website']; ?>" size="40" maxlength="80" /></td>
	</tr>
    </table>
 <p>&nbsp;</p>
  </form>
</div>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
<?php
((mysqli_free_result($rsSite) || (is_object($rsSite) && (get_class($rsSite) == "mysqli_result"))) ? true : false);
?>
