<?php require_once('Connections/piercecty.php');
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "login.php";
require_once('includes/levelcheck.php');
include_once('includes/converter.inc.php');

$branch='All'; 
// find user to limit to one branch.
$username=$_SESSION['MM_Username'];
$userq="select branch, ID_picker from users, pickers, branches where users.ID_user=pickers.ID_picker and branches.ID_leader=pickers.ID_picker and user_name='$username'";
$rsUser=mysqli_query($piercecty, $userq) or die(mysqli_error($piercecty));
if(mysqli_num_rows($rsUser)>0) {
$userrow=mysqli_fetch_assoc($rsUser);
$branch=$userrow['branch'];
$fbranch=" and branch='$branch' ";
// exception for Lizz Marks and Chris Madden
$ID_picker=$userrow['ID_picker'];
if($ID_picker==834 || $ID_picker==956 || $ID_picker==921) {$fbranch=''; $branch='All';}
}
// echo $branch;
if(isset($_GET['sort'])) 
	{ 
	$sort=$_GET['sort'];
	$direct=$_GET['direct'];
	if($direct=='ASC') { $direct='DESC'; } else { $direct='ASC'; }
	}
 else { $sort='date';
 		$direct='DESC'; }

switch($sort) {
	case 'ID': 		$orderby='harvests.ID_harvest'; 		break;
	case 'ID_site': 		$orderby='sites.ID_site'; 		break;
	case 'Name': 		$orderby='sites.farm'; 		break;
	case 'date': 		$orderby='harvests.h_date'; 		break;
	case 'leader': 		$orderby='pickers.lname'; 		break;
	case 'pickers': 		$orderby='harvests.pick_num'; 		break;
	case 'totwgt': 		$orderby='harvests.totwgt'; 		break;
	default: 		$orderby='harvests.h_date'; 		break;
	}

$harvyear=date('Y');
if(isset($_POST['harvyear'])) $harvyear=$_POST['harvyear'];
if(isset($_POST['branch'])) $branch=$_POST['branch'];

$fbranch= $branch=='All' ? '' : " and branch='$branch' ";

$query_rsGleans = "select harvests.ID_harvest, harvests.h_date, harvests.h_time, pickers.fname, pickers.lname, sites.ID_site, sites.farm, sites.address, harvests.totwgt, harvests.pick_num, where_to FROM harvests, pickers, sites where  harvests.ID_site=sites.ID_site and pickers.ID_picker=if(harvests.ID_leader is not NULL, harvests.ID_leader, 0) AND year(h_date)='$harvyear' $fbranch ORDER BY $orderby $direct";
// echo $query_rsGleans;
$rsGleans = mysqli_query($piercecty, $query_rsGleans) or die(mysqli_error($piercecty));
$totalRows_rsGleans = mysqli_num_rows($rsGleans);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Master harvest list</title>
<style type="text/css">
<!--
-->
</style>
<link href="database.css" rel="stylesheet" type="text/css" />
</head>
<body class="SH">
<div id="container">
  <?php include_once('includes/AdminNav1.inc.php'); ?>

<div id="mainContent">
    <h2 class="SH"><strong>Harvest master list</strong></h2>
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
	 
	 
    <table width="95%" border="1">
      <tr class="center_cell">
        <th><p><a href="harvestlist-master.php?sort=ID&amp;direct=<?php echo $direct; ?>">Harvest<br />
        Details</a></p></th>
        <th><p><a href="harvestlist-master.php?sort=ID&amp;direct=<?php echo $direct; ?>">Harvest<br />
        Update</a></p></th>        
		  <th><p><a href="harvestlist-master.php?sort=ID_site&amp;direct=<?php echo $direct; ?>">Site ID</a></p></th>
        <th><p><a href="harvestlist-master.php?sort=Name&amp;direct=<?php echo $direct; ?>">Name</a></p></th>
        <th><a href="harvestlist-master.php?sort=date&amp;direct=<?php echo $direct; ?>">Date</a></th>
        <th>Time</th>
        <th width="25%">Crops</a> harvested</th>
        <th><a href="harvestlist-master.php?sort=leader&amp;direct=<?php echo $direct; ?>">Leader</a></th>
        <th><p><a href="harvestlist-master.php?sort=pickers&amp;direct=<?php echo $direct; ?>">Actual<br />
        pickers</a></p></th>
        <th><a href="harvestlist-master.php?sort=totwgt&amp;direct=<?php echo $direct; ?>">Total weight</a></th>
      </tr>
      <?php while ($harvestrow = mysqli_fetch_assoc($rsGleans)) { ?>
      <tr>
        <td class="centercell"><a href="harvestroster.php?harvesttemp=<?php echo $harvestrow['ID_harvest']; ?>"><?php echo $harvestrow['ID_harvest']; ?></a></td>
        <td class="centercell"><a href="Utilities/harvestupdate.php?harvesttemp=<?php echo $harvestrow['ID_harvest']; ?>"><?php echo $harvestrow['ID_harvest']; ?></a></td>
		  <td class="centercell"><a href="Utilities/sitedetail.php?sitetemp=<?php echo $harvestrow['ID_site']; ?>"><?php echo $harvestrow['ID_site']; ?></a></td>
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
		 if($harvestrow['where_to']<>'' and $harvestrow['h_date']>'2022-12-31') { echo '<span style="color:red;"> distribution agency needed</span>'; }
		  
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
      <?php }  ?>
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
