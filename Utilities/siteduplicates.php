<?php 
require_once('../Connections/piercecty.php'); 
if (!isset($_SESSION)) { session_start(); }
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../includes/sqlcleaner.php');

if(isset($_POST['merge']) and $_POST['old']>0 and $_POST['new']>0) {
$old=$_POST['old'];
$new=$_POST['new'];

// get row of new data
$querynew="select * from sites where ID_site=$new";
$rsNew = mysqli_query($piercecty, $querynew) or die(mysqli_error($piercecty));
$rownew = mysqli_fetch_assoc($rsNew);

// get row of field names
$queryfields = "Describe sites";
$rsFields = mysqli_query($piercecty, $queryfields) or die(mysqli_error($piercecty));
$numfields = mysqli_num_rows($rsFields);
$rowfields = mysqli_fetch_assoc($rsFields); // pull a row to skip over ID_site number field

// build update query string from new data and fields names
$update="update sites set ";
while($rowfields = mysqli_fetch_assoc($rsFields)) { 
	$field=$rowfields['Field'];
	if($rownew["$field"]<>'') { $update.=$field."=".GetSQLValueString($rownew["$field"],"text").", "; }
	}
$update=substr($update,0,-2); // chop off extra space and comma
$update.=" where ID_site=".$old;
//echo '<br />'.$update;
$rsUpdate=mysqli_query($piercecty, $update) or die(mysqli_error($piercecty));

// delete discarded site
$querydelete="delete from sites where ID_site=$new";
//echo '<br />'.$querydelete;
$rsDelete=mysqli_query($piercecty, $querydelete) or die(mysqli_error($piercecty));

// change glean entries
$queryharvests="update harvests set ID_site=$old where ID_site=$new";
//echo '<br />'.$queryharvests;
$rsGleans=mysqli_query($piercecty, $queryharvests) or die(mysqli_error($piercecty));
} // end of merge posted

$query="select ID_site, farm, address, contact1,phone1, regdate from sites order by farm, substr(address,0,4), regdate";
$rsSites = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Site duplicates manager</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2><strong>Site duplicates manager</strong></h2>
    <p>Pairs of sites (possible duplicates) are based on name and the first four characters of the address. If pairs of sites appear on the page, compare the name, address and contact information of both sites. Then:</p>
<p><strong>If they really are two different sites with the same name and start of address (very unlikely)</strong>: It is simplest to change the name of one of them, using the Site Update link so that they do not keep appearing on this page. For instance, change 'Fred Farmer' to 'Fred Farmer (2)'.</p>
<p><strong>If they are actually two separate entries in the database of the same site:</strong> This can be resolved by clicking on 'Merge all...&quot; This puts all of the newer site information into the older site record, deletes the newer record, and assigns any harvests at the newer site to the older site. Be <em><strong>very certain</strong></em> of duplicates before using this function. NOTE: The page may not execute if there are single quotes in any fields.</p>
<table width="600px" border="1" align="center" cellpadding="2" cellspacing="2">
<tr><th colspan="3"></th></tr>
            
      <?php
	  $temparr1 = mysqli_fetch_assoc($rsSites);
	  $temparr2 = mysqli_fetch_assoc($rsSites);
	  for ($i=1; $i < mysqli_num_rows($rsSites); ++$i) { // do one less than the total numbr of sites since always need 2
		if(trim($temparr2['farm']) == trim($temparr1['farm']) && substr(trim($temparr2['address']),0,4)==substr(trim($temparr1['address']),0,4)) {  
   // echo '<br />'.trim($temparr2['farm']).'-'.trim($temparr1['regdate']).' '.trim($temparr1['farm']).'-'.trim($temparr1['regdate']);
// two consecutive sites have same name and start of address so build a table section?>
    <tr>
    	<td>Site  ID</td>
        <td><a href="siteupdate.php?sitetemp=<?php echo $temparr1['ID_site']; ?>">update site #<?php echo $temparr1['ID_site']; ?></a></td>
        <td><a href="siteupdate.php?sitetemp=<?php echo $temparr2['ID_site']; ?>">update site #<?php echo $temparr2['ID_site']; ?></a></td>
	</tr>
    <tr>    
    <td><form action="siteduplicates.php" method="post" >
    	<input  type="submit" name="merge" value="Merge new into old" />
    	<input name="old" type="hidden" value="<?php echo $temparr1['ID_site']; ?>" />
    	<input name="new" type="hidden" value="<?php echo $temparr2['ID_site']; ?>" />
		</form></td>
    <td></td>
    <td></td>
</tr>
<tr>
<td>farm</td>
        <td><?php echo $temparr1['farm'];?></td>
        <td><?php echo $temparr2['farm'];?></td>
	</tr>
	<tr>
 		<td>address</td>
        <td><?php echo $temparr1['address'];?></td>
        <td><?php echo $temparr2['address'];?></td>
</tr>
	<tr>
 		<td>contact</td>
        <td><?php echo $temparr1['contact1'];?></td>
        <td><?php echo $temparr2['contact1'];?></td>
</tr>
<tr>
 		<td>phone</td>
      <td><?php echo $temparr1['phone1'];?></td>
       <td><?php echo $temparr2['phone1'];?></td>
</tr>              
	<?php // look up harvest histories of both 
	$site1 = $temparr1['ID_site'];
	$query1 = "select ID_harvest from harvests where ID_site = $site1";
	$rsGleans1 = mysqli_query($piercecty, $query1) or die(mysqli_error($piercecty));
	$harvests1 = mysqli_num_rows($rsGleans1);
	
	$site2 = $temparr2['ID_site'];
	$query2 = "select ID_harvest from harvests where ID_site = $site2";
	$rsGleans2 = mysqli_query($piercecty, $query2) or die(mysqli_error($piercecty));
	$harvests2 = mysqli_num_rows($rsGleans2); ?>

	<tr><td>harvests</td><td><?php echo $harvests1 ?></td><td><?php echo $harvests2 ?></td></tr>
             
    <tr><th colspan = 3>&nbsp;</th></tr>
        <?php } // end of one pair of pickers so shift arrays and look for the next pair
  		$temparr1 = $temparr2;
	    $temparr2 = mysqli_fetch_assoc($rsSites);
         } // done with all sites ?> 
</table>
<p>&nbsp;</p>
</div>
 <!-- end #container -->
</div>
</body>
</html>
