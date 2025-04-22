<?php require_once('../Connections/piercecty.php');
$MM_authorizedUsers = "all,change,view,branch";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
include_once('../includes/converter.inc.php');

// find name and ID_picker from login
$fname=''; $lname='unknown'; $ID_picker=0; $err='';
$user_name=$_SESSION['MM_Username']; 
$q="select fname, lname, ID_picker from pickers,users where users.ID_user=pickers.ID_picker and user_name='$user_name'";

$rsQ=mysqli_query($piercecty,$q);
if(!$rsQ) {$err=' [The volunteer could not be identified from the login username. An administrator may need to update the database users table with the volunteer ID.]';}
else {
$r=mysqli_fetch_assoc($rsQ);
if($r) extract($r);
}
// is the volunteer a designated branch leader? Get branch name and ID_branch
$q="select ID_branch, branch as ldrbranch from branches where ID_leader=$ID_picker";
$rsQ=mysqli_query($piercecty, $q); 
if(mysqli_num_rows($rsQ)==0) {
	$ldrbranch='not assigned'; 
	$ID_branch=0;}
else {
	$r=mysqli_fetch_assoc($rsQ);
	extract($r);
}
$_SESSION['ldrbranch']=$ldrbranch;  // to be used on other pages

if(isset($_POST['branch']) && $_POST['branch']<>'All') {
	$branch=$_POST['branch'];
	$fbranch=" and branch='$branch' ";
} elseif(isset($ldrbranch) && $ldrbranch<>'not assigned') {
	$branch=$ldrbranch;
	$fbranch=" and branch='$branch' ";
} else {
	$branch='All';
	$fbranch='';
}

if(isset($_GET['sort'])) 
	{ 
	$sort=$_GET['sort'];
	$direct=$_GET['direct'];
	if($direct=='ASC') { $direct='DESC'; } else { $direct='ASC'; }
	}
 else { $sort='date';
 		$direct='DESC'; }

switch($sort) {
	case 'date': 		$orderby='harvests.h_date'; 		break;
	default: 		$orderby='harvests.h_date'; 		break;
	}

$harvyear=date('Y');
if(isset($_POST['harvyear'])) $harvyear=$_POST['harvyear'];

$query_rsGleans = "select harvests.ID_harvest, harvests.h_date, harvests.h_time, pickers.fname, pickers.lname, sites.ID_site, sites.farm, sites.address, harvests.totwgt, harvests.pick_num FROM harvests, pickers, sites where  harvests.ID_site=sites.ID_site and pickers.ID_picker=if(harvests.ID_leader is not NULL, harvests.ID_leader, 0) AND year(h_date)=$harvyear $fbranch ORDER BY $orderby $direct";
// echo $query_rsGleans;
$rsGleans = mysqli_query($piercecty, $query_rsGleans) or die(mysqli_error($piercecty));
$harvestrow = mysqli_fetch_assoc($rsGleans);
$totalRows_rsGleans = mysqli_num_rows($rsGleans);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Branch leader home</title>
<style type="text/css">
<!--
-->
</style>
<link href="../database.css" rel="stylesheet" type="text/css" />
<?php include('../includes/branchhelp.inc.php'); ?>
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav-branch.inc.php');?>
<div id="mainContent">
<a href="../help/help.php"  onClick="wopen('../help/help.php', 'popup', 640, 480); return false;"><div class="branchhelp">Page help</div></a>
<h2>Branch leader home</h2>
<p><strong>Name:</strong> <?php echo $fname.' '.$lname.' '.$err;?><br />
<strong>Leader's branch:</strong> <?php echo $ldrbranch;?></p>
<strong>Your harvests</strong>    
<form action="" method="post" name="year">
    Year <select name="harvyear">
    <?php 
	$yearquery="select distinct(year(h_date)) as year from harvests order by year desc";
	$rsYear=mysqli_query($piercecty, $yearquery);
	if($rsYear) {
		while ($yrow=mysqli_fetch_assoc($rsYear)) {
			$year=$yrow['year'];
			if($year>2010) {
				echo "<option value=$year ";
				if($year==$harvyear) echo 'selected="selected"';
				echo ">$year</option>";
			}
		}
	}
	?>
    </select>
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
<input name="submit" type="submit" value="Select" />
 </form>

<table width="1220" border="1">
      <tr class="center_cell">
        <th><p>Harvest<br />Details</p></th>
        <th><p>Harvest<br />Update</p></th>        
		  <th><p>Site Details</p></th>
        <th><p>Name</p></th>
        <th><a href="branch-home.php?sort=date&amp;direct=<?php echo $direct; ?>">Date</a></th>
        <th>Time</th>
        <th width="25%">Crops</a> harvested</th>
        <th>Leader</th>
        <th><p>Actual<br />pickers</p></th>
        <th>Total weight</th>
      </tr>
      <?php do { ?>
      <tr>
        <td class="centercell"><a href="harvestroster-branch.php?harvesttemp=<?php echo $harvestrow['ID_harvest']; ?>"><?php echo $harvestrow['ID_harvest']; ?></a></td>
        <td class="centercell"><a href="harvestupdate-branch.php?harvesttemp=<?php echo $harvestrow['ID_harvest']; ?>"><?php echo $harvestrow['ID_harvest']; ?></a></td>
        <td class="centercell"><a href="sitedetail-branch.php?sitetemp=<?php echo $harvestrow['ID_site']; ?>"><?php echo $harvestrow['ID_site']; ?></a></td>
        <td><?php echo $harvestrow['farm']; ?></td>
		  <td class="centercell"><?php echo $harvestrow['h_date']; ?></td>
        <td class="centercell"><?php echo $harvestrow['h_time']; ?></td>
        <td><?php
				$harvest=$harvestrow['ID_harvest'];
				$convarr=convarr($harvest);
				$croplist='';
		 		foreach($convarr as $convrow) {$croplist.=$convrow['name'].', ';}
            if($croplist=='') $croplist='<span style="color:red;"> --no crops selected--</span>  ';
				echo substr($croplist,0,strlen($croplist)-2);
		 ?></td>
        <td><?php echo $harvestrow['fname']; ?> <?php echo $harvestrow['lname']; ?></td>
        <?php 
		 $harvest=$harvestrow['ID_harvest'];
		 $query_Attend = "select count(status) as count from rosters where ID_harvest='$harvest' and (status='harvested' or status='assisted' or status='leader' or status='signup')";
		 $rsAttend = mysqli_query($piercecty, $query_Attend) or die(mysqli_error($piercecty));
		 $row_Attend = mysqli_fetch_assoc($rsAttend); ?>
  		<td class="centercell"><?php echo $row_Attend['count']; ?></td>
        <td class="centercell"><?php echo $harvestrow['totwgt']; ?></td>
      </tr>
      <?php } while ($harvestrow = mysqli_fetch_assoc($rsGleans)); ?>
    </table></div><!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
  <div id="footer">
    <!-- end #footer -->
  </div>
<!-- end #container --></div>
</body>
</html>
<?php
((mysqli_free_result($rsGleans) || (is_object($rsGleans) && (get_class($rsGleans) == "mysqli_result"))) ? true : false);
?>
