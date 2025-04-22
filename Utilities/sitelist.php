<?php require_once('../Connections/piercecty.php'); 
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php"; 
require_once('../includes/levelcheck.php');
$ftype=" "; $fname=''; $fstatus=" "; $factive=" and Active='Yes' "; $fbranch=''; $fsort=" order by regdate desc "; // default filter terms
$postStatus='All'; $postType='All'; $postActive='All'; $postName=''; $postOrder="regdate"; $postBranch='All'; // default POSTed variables

// find user to limit to one branch.
$branch='';
$username=$_SESSION['MM_Username'];
$userq="select ID_picker, branch from users, pickers, branches where users.ID_user=pickers.ID_picker and branches.ID_leader=pickers.ID_picker and user_name='$username'";
$rsUser=mysqli_query($piercecty, $userq) or die(mysqli_error($piercecty));
if(mysqli_num_rows($rsUser)>0) {
$userrow=mysqli_fetch_assoc($rsUser);
$branch=$userrow['branch'];
$fbranch=" and branch='$branch' ";
// exception for Lizz Marks and Chris Madden
$ID_picker=$userrow['ID_picker'];
if($ID_picker==834 || $ID_picker==956 || $ID_picker==921) {$fbranch=''; $branch='All';}
}

// assign site leader
if(isset($_POST['assignleader'])) {
	$siteleader=$_POST['siteleader'];
	$ID_site=$_POST['ID_site'];
	$ldrq="update sites set siteleader=$siteleader where ID_site=$ID_site";
	$rsLdr=mysqli_query($piercecty, $ldrq) or die(mysqli_error($piercecty).' could not assign leader');
}

if(isset($_POST['submit'])) { // filters are posted  -----------------------------------
$postOrder=$_POST['order'];
$postStatus=$_POST['status'];  
$postType=$_POST['type'];  
$postActive=$_POST['Active'];  
$postName=$_POST['Name'];
$branch=$_POST['branch'];

$currentdate=date('Y-m-d');

// update filter values from posted filters

if($postActive=='All')  $factive= " ";
if($postActive=='Active') $factive=" and Active='Yes' "; 
if($postActive=='Inactive') $factive= " and Active='No' ";

if($postStatus=='All')  $fstatus= " ";
if($postStatus=='needs maintenance')   $fstatus=" and status='needs maintenance' "; 
if($postStatus=='needs removal') $fstatus=" and status='needs removal' "; 
if($postStatus=='good to pick') $fstatus= " and status='good to pick' ";

if($postType=='All')  $ftype= " ";
if($postType=='Farm produce')   $ftype=" and type='Farm produce' "; 
if($postType=='Fruit trees') $ftype=" and type='Fruit trees' "; 
if($postType=='Mixed') $ftype= " and type='Mixed' ";

$fbranch = $branch=='All' ? '' : " and branch='$branch' ";

if($postName=='')  {$fname= '';
} else {
$long=strlen(stripslashes($postName));
if($long==0) $long=1;
$fname=" and lcase(left(farm,$long))=lcase('$postName')"; }

if($postOrder=='farm') $fsort=' order by farm ';
if($postOrder=='soon') $fsort=' order by soon ';

} // end of isset filters ---------------------------------------------------------------

// get all data for pages to calculate soon dates
$soonquery = "SELECT ID_site from sites where 2=2 $fstatus $fname $fbranch $ftype";
// echo $soonquery.'<br />'; 
$rsSoon = mysqli_query($piercecty, $soonquery) or die(mysqli_error($piercecty));

while($soonrow=mysqli_fetch_assoc($rsSoon)) {
   $ID_site=$soonrow['ID_site'];

// calculate and update sites with days to first harvest in previous years
// find if a harvest was done this year already
$thisyr=date('Y');
$harvq="select h_date from harvests, sites where harvests.ID_site=sites.ID_site and year(h_date)=$thisyr and sites.ID_site=$ID_site and totwgt>0 and harvests.soon<>'No'";
$rsharv=mysqli_query($piercecty,$harvq);
if(mysqli_num_rows($rsharv)>0) {$soon=999;} // harvested this year

else { // find earliest  harvest date from previous years

$harvq="select min(substr(h_date,-5)) as mthday, h_date from harvests, sites where harvests.ID_site=sites.ID_site  and sites.ID_site=$ID_site and totwgt>0 and harvests.soon<>'No'";
$rsharv=mysqli_query($piercecty, $harvq);
$harvrow=mysqli_fetch_assoc($rsharv);
if($harvrow['h_date']=='')  { $soon=-999;} // never harvested
else {
   $date2=strtotime($thisyr.'-'.$harvrow['mthday']);
   $date1=strtotime(date('Y-m-d'));
   $soon=round(($date2 - $date1)/60/60/24);
   }
} // end of check previous years
$soonupdateq="update sites set soon=$soon where ID_site=$ID_site";
$rsSoonupdate=mysqli_query($piercecty,$soonupdateq);

// echo '<br />ID_site:'.$ID_site.' $soon:'.$soon;
} // end of all sites to calculate due days

// get all data for sites
$query = "SELECT ID_site, farm, contact1, address, branch, crops, regdate, siteleader, soon, type from sites where 2=2 $factive $fstatus $fname $fbranch $ftype $fsort ";
// echo $query.'<br />'; 
$rsSites = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$numrows= mysqli_num_rows($rsSites);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Site list</title>
<link href="../database.css" rel="stylesheet" type="text/css" />
<style type="text/css">
table td, th {border: 1px solid #666666; border-collapse: collapse;}
</style>
</head>
<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
<h2>Site list</h2>
    <div>
	 (When checking for duplicates, set Status to 'All'.)
      <form action="" method="POST" name="filters">
        <table style="border: 1px solid #666666; border-collapse: collapse;">
          <tr>
<th>Branch <select name="branch">
	<option value="All" <?php if($postBranch=='All') echo 'selected="selected"';?>>All</option>
	<?php 
	$branchq="select branch as branchitem from branches order by branch";
	$rsBranch=mysqli_query($piercecty, $branchq);
	while($branchrow=mysqli_fetch_assoc($rsBranch)) {
	extract($branchrow);  ?>
	<option value="<?php echo $branchitem;?>" <?php if($branchitem==$branch) echo 'selected="selected"';?>><?php echo $branchitem;?></option>";
<?php	} ?>
</select>
</th>
<th>Active?
              <select name="Active">
                <option value="All" <?php if($postActive=='All') echo 'selected="selected"';?>>All</option>
                <option value="Active" <?php if($postActive=='Active') echo 'selected="selected"';?>>Active</option>
                <option value="Inactive" <?php if($postActive=='Inactive') echo 'selected="selected"';?>>Inactive</option>
              </select>
</th>
<th>Status
              <select name="status">
                <option value="All" <?php if($postStatus=='All') echo 'selected="selected"';?>>All</option>
                <option value="needs removal" <?php if($postStatus=='needs removal') echo 'selected="selected"';?>>needs removal</option>
                <option value="needs maintenance" <?php if($postStatus=='needs maintenance') echo 'selected="selected"';?>>needs maintenance</option>
                <option value="good to pick" <?php if($postStatus=='good to pick') echo 'selected="selected"';?>>good to pick</option>
              </select>
</th>
<th>Type
              <select name="type">
                <option value="All" <?php if($postType=='All') echo 'selected="selected"';?>>All</option>
                <option value="Farm produce" <?php if($postType=='Farm produce') echo 'selected="selected"';?>>Farm produce</option>
                <option value="Fruit trees" <?php if($postType=='Fruit trees') echo 'selected="selected"';?>>Fruit trees</option>
                <option value="Mixed" <?php if($postType=='Mixed') echo 'selected="selected"';?>>Mixed</option>
              </select>
</th>
<th>Site name
             	<input width = "20"  type="text" name="Name" value="<?php if($postName<>'') echo $postName; ?>"/>
</th>
<th>Order by
          	<select name="order">
		            <option value="regdate" <?php if($postOrder=='regdate') echo 'selected="selected"';?>>Registration date</option>
            		<option value="farm" <?php if($postOrder=='farm') echo 'selected="selected"';?>>Site Name</option>
            		<option value="soon" <?php if($postOrder=='soon') echo 'selected="selected"';?>>Days to previous harvest</option>
		    </select>
</th>
<th><input name="submit" type="submit" value="Filter" /></th>
          </tr>
        </table>
      </form>
    </div><!-- end of filters div -->
  <br />
  Found <?php echo $numrows;?> sites
  <div style="width:100%">
    <table style="width:95%; margin:auto; border: 1px solid #666666; border-collapse: collapse;">
      <tr>
        <th>Site name</th>
        <th>Contact</th>
        <th>Address</th>
        <th>Branch</th>
	  <th>Site Leader</th>
        <th>Crops</th>
        <th>Registration<br />date</th>
        <th>Days to earliest previous<br />harvest date</th>
    </tr>
 <?php
while ($row = mysqli_fetch_assoc($rsSites)) { 
extract($row);
?>
      <tr>
        <td><?php echo "<a href='siteupdate.php?sitetemp=$ID_site' target='_blank'>$farm</a>"; ?></td>
        <td><?php echo $contact1; ?></td>
        <td><?php echo $address; ?></td>
        <td><?php echo $branch; ?></td>
          <td> 
	<form method="post">		    
		    <select name="siteleader">
          			<option value='0'> -none- </option>
					<?php 
					$ldrsq="select fname, lname, ID_picker from pickers where leader='Yes' order by lname, fname";
					$rsLdrs=mysqli_query($piercecty,$ldrsq);
					while($ldrsdrop=mysqli_fetch_assoc($rsLdrs)) { ?>          			
            		<option value="<?php echo $ldrsdrop['ID_picker']; ?>" <?php if($siteleader==$ldrsdrop['ID_picker']) echo 'selected="selected"'; ?>><?php echo $ldrsdrop['lname'].','.$ldrsdrop['fname'];?></option>
            		<?php } ?>
	           </select>
			<input type="submit" name="assignleader" value="Save"/>
		    <input type="hidden" name="ID_site" value="<?php echo $ID_site;?>"/>
	</form>
	 </td>
	<td><?php echo substr($crops,0,30); ?></td>
        <td><?php echo $regdate; ?></td>
<?php 
if($soon==999) { echo '<td style="text-align:center;">done</td>';} 
elseif($soon==-999) { echo '<td style="background-color:#ff8888; text-align:center;">never</td>'; }
else {
   if($soon>29)  {$level="background-color: green; color:white;text-align:center;";} 
   elseif($soon>13) {$level="background-color: yellow; color:black;text-align:center;";} 
   else {$level="background-color: red; color:white;text-align:center;";} 
   echo "<td style='$level'>$soon</td>"; 
   }
?> 
</tr>
 <?php } // end of sites --------------------------------------------------------------
 ?>
 </table>
  </div>
  <!-- end of list div----------------------------------------------- -->

</div><!-- end of mainContent -->
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
<br class="clearfloat" />
</div><!-- end #container -->
</body>
</html>
