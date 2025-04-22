<?php require_once('../Connections/piercecty.php'); 

$site = "-1";
if (isset($_GET['sitetemp'])) {
  $site = $_GET['sitetemp'];
}

$siteq = "SELECT * FROM sites WHERE ID_site = $site";
$rsSite = mysqli_query($piercecty, $siteq);
$siterow = mysqli_fetch_assoc($rsSite);

$site = "-1";
if (isset($_GET['sitetemp'])) { $site = $_GET['sitetemp'];}

$harvestsq = "SELECT harvests.ID_leader, harvests.h_date, harvests.totwgt, present, harvests.pick_num, harvests.otherinfo, pickers.lname, pickers.fname FROM harvests, pickers WHERE ID_site = $site and pickers.ID_picker = harvests.ID_leader";
$rsharvests = mysqli_query($piercecty, $harvestsq);
if($rsharvests) $harvestrow = mysqli_fetch_assoc($rsharvests);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>site detail</title>
<style type="text/css">
<!--
-->
</style>
<link href="../database.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH">
<div id="container">
<div id="mainContent">
    <h2 class="SH"><strong>site detail</strong></h2>
    <p><strong>Owner</strong></p>
<p>
Site registration ID: <?php echo $siterow['ID_site']; ?><br />
Site name: <?php echo $siterow['farm']; ?><br />
Contact: <?php echo $siterow['contact1']; ?><br />
Address: <?php echo $siterow['address'].'. '.$siterow['city'].', '.$siterow['state'].' '.$siterow['zip']; ?><br />
Phones: <?php echo $siterow['phone1']; ?>, <?php echo $siterow['phone2']; ?><br />
Email: <?php echo $siterow['email1']; ?><br />
Location: <?php echo $siterow['location']; ?><br />
Owner present: <?php echo $siterow['present']; ?><br />
Crops: <?php echo $siterow['crops']; ?><br />
</p>
<p><strong>site</strong></p>
<p>
Venue: <?php echo $siterow['venue']; ?><br />
</p>
<p><strong>Owner's info and Site Scout notes</strong></p>
<p>
<?php echo $siterow['otherinfo']; ?>
</p>
<p><strong>harvest history</strong></p>
<p>
<?php if($rsharvests) {
	do { ?>
<p>
Date: <?php echo $harvestrow['h_date']; ?><br />
Leader: <?php echo $harvestrow['fname']; ?> <?php echo $harvestrow['lname']; ?><br />
Pickers: <?php echo $harvestrow['pick_num']; ?><br />
Total weight: <?php echo $harvestrow['totwgt']; ?><br />
Other info: <?php echo $harvestrow['otherinfo']; ?><br />
</p>
<?php } while ($harvestrow = mysqli_fetch_assoc($rsharvests)); 
} // end of iff rsharvests?>
</div>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <!-- end #container -->
</div>
</body>
</html>
<?php
((mysqli_free_result($rsSite) || (is_object($rsSite) && (get_class($rsSite) == "mysqli_result"))) ? true : false);
?>
