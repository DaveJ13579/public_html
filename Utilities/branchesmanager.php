<?php 
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
include_once('../includes/branch.inc.php');

function unquote($zips) { 
	$result = preg_replace("/[^0-9\,]+/", "", $zips); 
	return $result;
	}
function requote($zips) {
	$zipsarr=explode(',',$zips);
	$zips='';
	foreach($zipsarr as $zip) { $zips.=$zip."','";}
	$zips=substr("'".$zips,0,-2);
	return $zips;
	}	

if (!isset($_SESSION))  session_start(); 
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

// update all sites
if(isset($_POST['updatesites']) and $_POST['updatesites']=='Update All Sites') {
$sitesq="select ID_site, zip from sites";
$rsSites=mysqli_query($piercecty,$sitesq);
while($sitesrow=mysqli_fetch_assoc($rsSites)) {
extract($sitesrow);
$branch=sitebranch($ID_site);
$updatesiteq="update sites set branch='$branch' where ID_site=$ID_site";
// echo $updatesiteq.'<br />';
$rsUpdatesites=mysqli_query($piercecty,$updatesiteq);
} // end of all sites
echo '<br />All sites updated with current branch';
} // end of update clicked

$branchout='';$zipin='';
if(isset($_GET['zipin'])) {
	$zipin=substr(trim($_GET['zipin']),0,5);
	$branchout=zipbranch(GetSQLValueString($zipin, "text"));
}
$delswitch='no';
if(isset($_POST['ID_branch']))  $ID_branch=$_POST['ID_branch'];
if(isset($_POST['delete'])) { // delete branch
$delswitch=$_POST['delswitch'];
if($delswitch=='no') {$delswitch='yes';}  
elseif($delswitch=='yes') {
	$dquery="delete from branches where ID_branch=$ID_branch";
	$rsDelete=mysqli_query($piercecty, $dquery);
	$delswitch='no';
	}
} // end of if delete

if(isset($_POST['update']) and $_POST['branch']<>'') { // update branch
	$branch=GetSQLValueString($_POST['branch'], "text");
	$zips= '"'.requote($_POST['zips']).'"';
	$ID_leader=$_POST['ID_leader'] ? $_POST['ID_leader']  : 0;
	$updatequery = "UPDATE branches SET branch=$branch, zips=$zips, ID_leader=$ID_leader where ID_branch=$ID_branch";
// echo '<br />'.$updatequery;
	
	$Result1 = mysqli_query($piercecty, $updatequery) or die(mysqli_error($piercecty));
}

if(isset($_POST['insert'])) {
$insertquery="INSERT INTO branches (branch) VALUES (' - New branch - ')";
$Result1 = mysqli_query( $piercecty, $insertquery);
} // end of insert

$querybranches="select * from branches order by branch";
$rsBranches = mysqli_query( $piercecty, $querybranches) or die(mysqli_error($piercecty));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>branches manager</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2><strong>Branches manager</strong></h2>
	 <p>Enter zip codes for each branch, separated by commas, and with no spaces.</p>
    <table  width="1200px" border="1" cellpadding="1" cellspacing="1">
      <tr>
        <th>Branch name</th>
        <th>Zip codes</th>
        <th>Branch Leader</th>
        <th>&nbsp;</th>
   	  <th><form action="branchesmanager.php" name="inserting" method="post"><input type="submit" name="insert" value="Add a branch" /></form></th>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($rsBranches)) { 
	  	if($delswitch=='no' or ($delswitch='yes' and $row['ID_branch']==$ID_branch)) {
	  ?>
	    <form action="branchesmanager.php" name="branches" method="post">
        <tr class="centercell">
          <td><input name="branch" type="text" value="<?php echo $row['branch']; ?>" size="30" maxlength="50" /></td>
		  <td><textarea name="zips" cols="50" rows="4"><?php echo unquote($row['zips']); ?></textarea></td>
          <td> <select name="ID_leader">
          			<option value=''> </option>
					<?php 
					$ldrsq="select fname, lname, ID_picker from pickers where leader='Yes' order by lname, fname";
					$rsLdrs=mysqli_query($piercecty,$ldrsq);
					while($ldrsdrop=mysqli_fetch_assoc($rsLdrs)) { ?>          			
            		<option value="<?php echo $ldrsdrop['ID_picker']; ?>" <?php if($row['ID_leader']==$ldrsdrop['ID_picker']) echo 'selected="selected"'; ?>><?php echo $ldrsdrop['lname'].','.$ldrsdrop['fname'];?></option>
            		<?php } ?>
		            </select></td>          
			<td>&nbsp;<input type="submit" name="update" value="update" />&nbsp;</td>
          <?php if($delswitch=='yes')  {?>
          <td style="color:red; background-color:pink">Are you very, very sure?<br />(See <a href="../help/branchesmanager-help.php">Page Help</a>)<br /><input type="submit" name="cancel" value="cancel" /></td>
          <?php } ?>
		  <td><input type="submit" name="delete" value="delete" /></td>
          <input type="hidden" name="delswitch" value="<?php echo $delswitch;?>" />
          <input type="hidden" name="ID_branch" value="<?php echo $row['ID_branch'];?>" />
        </tr>
          </form>
        <?php } // end of if delswitch
		  } // end of all branches loop
		 ?>
</table>
<form>
<p><strong>Enter zip code to look up branch:</strong> 
  <input name="zipin" type="text" size="30" maxlength="50" value="<?php echo $zipin.' '.$branchout; ?>">
and hit the 'Enter' key. The branch name will appear where you typed the zip code.</p>
</form>
<p style="font-size: 1.3em"><strong>Update all sites</strong></p>
<p>Whenever there are changes to any branch names or lists of zip codes, the changes need to be migrated to all of the existing sites. Clicking the 'Update' below will make all of those changes. Be sure that all of the names are correct and the zip codes for each branch are separated by commas and with no spaces within the list or at the end. </p>
<form method="POST">
<input type="submit" name="updatesites" value="Update All Sites">
</form>
<br />
<!-- end #container --></div>
</div>
</body>
</html>
