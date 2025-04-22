<?php require_once('../Connections/piercecty.php'); 
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../includes/converter.inc.php');

$site = "-1";
if (isset($_GET['sitetemp']) and $_GET['sitetemp']<>'') { $site = $_GET['sitetemp']; }
if(isset($_GET['sitedrop']) and $_GET['sitedrop']<>'') { $site = $_GET['sitedrop']; }

$siteq = "SELECT * FROM sites WHERE ID_site = $site";
$rsSite = mysqli_query($piercecty, $siteq)  or die(mysqli_error($piercecty));
$siterow = mysqli_fetch_assoc($rsSite);
$harvestsq = "SELECT ID_leader, harvests.ID_harvest, h_date, totwgt, pick_num, harvests.otherinfo, lname, fname FROM harvests, pickers WHERE ID_site=$site and pickers.ID_picker=harvests.ID_leader order by h_date desc";
$rsharvests = mysqli_query($piercecty, $harvestsq)  or die(mysqli_error($piercecty));
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
<?php require_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
  <p><span class="pagehead">Site details</span> <a href="siteupdate.php?sitetemp=<?php echo $site; ?>">Go to site update</a></p>
      <form action="sitedetail.php" method="get" name="filtersform">
      <p>Enter site ID:
        <input name="sitetemp" type="text" size="4" id="temp1" value="<?php if(isset($site) and $site>0) echo $site;?>" />
         or select from list:
		<select name="sitedrop">
             <option value="" selected="selected"></option>
              <?php $sitesq="select ID_site, farm, address, city, crops from sites where Active='Yes' order by farm";
                    $rsSites=mysqli_query($piercecty, $sitesq) or die(mysqli_error($piercecty));
                    while ($droprow=mysqli_fetch_assoc($rsSites)) { ?>
              <option value="<?php echo $droprow['ID_site']; ?>" <?php if($droprow['ID_site']==$site) echo 'selected="selected" '; ?>><?php echo $droprow['farm'].", ".$droprow['address'].", ".$droprow['city'].", ".$droprow['crops'];?></option>
	 		  <?php } ?>
        </select>
      <input type="submit" name="submit" id="submit" value="Show details" />
      </p>
      </form>  
  <table width="600" border="2" align="center">
    <tr align="center">
      <td><a href="sitedetail-plaintext.php?sitetemp=<?php echo $site; ?>">Plaintext version for<br  />
        copying into email</a></td>
<!--      <td><a href="harvestPlanning.php?site=<?php echo $site; ?>">harvest<br  />
        Planning form</a></td> -->
      <td><a href="EntryAuthorization.php?site=<?php echo $site; ?>">Entry<br  />
        Authorization form</a></td>
    </tr>
  </table>
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
	 <th>Site Leader</th>
    <th>General area</th>
    <th>Venue</th>
    <th>Active?</th>
    <th>Status</th>
    <th>Authorization<br />Date</th>
   </tr>
   <tr>
     <td><?php echo volname($siterow['siteleader']); ?></td>
     <td><?php echo $siterow['region']; ?></td>
     <td><?php echo $siterow['venue']; ?></td>
       <td><?php echo $siterow['Active']; ?></td>
       <td><?php echo $siterow['status']; ?></td>
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
    <th>Type</th>
  	  </tr>
    <tr>
      <td><?php echo $siterow['howhear']; ?></td>
      <td><?php echo $siterow['website']; ?></td>
      <td><?php echo $siterow['type']; ?></td>
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
    <?php while ($harvestsrow = mysqli_fetch_assoc($rsharvests)) { ?>
    <tr>
      <td align="center"><a href="../harvestroster.php?harvesttemp=<?php echo $harvestsrow['ID_harvest']; ?>"><?php echo $harvestsrow['h_date']; ?></a></td>
      <td><?php echo $harvestsrow['fname']; ?> <?php echo $harvestsrow['lname']; ?></td>
      <td align="center"><?php echo $harvestsrow['pick_num']; ?></td>
      <td align="center"><?php echo $harvestsrow['totwgt']; ?></td>
      <td><?php echo $harvestsrow['otherinfo']; ?></td>
    </tr>
    <?php } ?>
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
