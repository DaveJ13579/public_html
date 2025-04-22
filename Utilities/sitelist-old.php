<?php require_once('../Connections/piercecty.php'); 
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$fname=''; $fstatus=" and Active='Yes' "; $fsort=" order by regdate desc "; // default filter terms
$postStatus='Active'; $postName=''; $postOrder="regdate";// default POSTed variables

if(isset($_POST['submit'])) { // filters are posted  -----------------------------------
$postOrder=$_POST['order'];
$postStatus=$_POST['Status'];  
$postName=$_POST['Name'];

$currentdate=date('Y-m-d');

// update filter values from posted filters

if($_POST['Status']=='All')  $fstatus= " ";
if($_POST['Status']=='Active') $fstatus=" and Active='Yes' "; 
if($_POST['Status']=='Inactive') $fstatus= " and Active='No' ";

if($postName=='')  {$fname= '';
} else {
$long=strlen(stripslashes($postName));
if($long==0) $long=1;
$fname=" and lcase(left(farm,$long))=lcase('$postName')"; }

if($postOrder=='farm') $fsort=' order by farm ';

} // end of isset filters ---------------------------------------------------------------

// get all data for pages
$query = "SELECT * from sites where 2=2 $fstatus $fname $fsort ";
$rsSites = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$numrows = mysqli_num_rows($rsSites);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Site list</title>
<link href="../database.css" rel="stylesheet" type="text/css" />
<style type="text/css">
</style>
<script type="text/javascript">
var tempvar = null;
function popup(show){
	show.style.display="block"
	if (tempvar && (tempvar !== show)) tempvar.style.display="none"
	tempvar=show
}
</script>
</head>
<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">

 <?php  // reset pointer and construct the popups for each site ------------------------------------------

if($numrows>0) mysqli_data_seek($rsSites, 0);

while ($row = mysqli_fetch_assoc($rsSites)) {   ?>
<div class="pop" id="pop<?php echo $row['ID_site']; ?>">
    <?php 
$cinfo="<br /><strong><center>Site number: ".$row['ID_site']."</center></strong><br /><br />";
$cinfo.="<strong>Name:</strong> ".$row['farm']."<br />";
$cinfo.="<strong>Contact1:</strong> ".$row['contact1']."<br />";
$cinfo.="<strong>Phone1:</strong> ".$row['phone1']." ";
$cinfo.="<strong>Email1:</strong> ".$row['email1']."<br />";
$cinfo.="<strong>Contact2:</strong> ".$row['contact2']."<br />";
$cinfo.="<strong>Phone2:</strong> ".$row['phone2']." ";
$cinfo.="<strong>Email2:</strong> ".$row['email2']."<br />";
$cinfo.="<strong>Contact3:</strong> ".$row['contact3']."<br />";
$cinfo.="<strong>Phone3:</strong> ".$row['phone3']." ";
$cinfo.="<strong>Email3:</strong> ".$row['email3']."<br />";
$cinfo.="<strong>Mailing address:</strong> ".$row['maddress'].", ".$row['mcity'].", ".$row['mstate']." ".$row['mzip']."<br /><br />";
$cinfo.="<strong>Branch:</strong> ".$row['branch'];
$cinfo.="<strong> Location:</strong> ".$row['location'];
$cinfo.="<strong> Size:</strong> ".$row['size']."<strong> Type:</strong><br />";
$cinfo.="<strong>Crops:</strong> ".$row['crops']."<br />";
$cinfo.="<strong>Address:</strong> ".$row['address'].", ".$row['city'].", ".$row['state']." ".$row['zip']."<br />";
$cinfo.="<strong>How heard of Pierce County Gleaning Project:</strong> ".$row['howhear']."<br />";

echo $cinfo."<br />";

// links included in the popup ---------------------------------------------------------
$site=$row['ID_site'];
$query2="select h_date, calcwgt from harvests where ID_site=$site and calcwgt>0 order by h_date desc";
$rsharvest = mysqli_query($piercecty, $query2) or die(mysqli_error($piercecty));
$row2 = mysqli_fetch_assoc($rsharvest);
$numrows2=mysqli_num_rows($rsharvest);
if($row2['h_date']<>'') { echo "<strong>Most recent harvest: </strong>".$row2['h_date'].", ".$row2['calcwgt']." pounds<br />"; }
	else { echo "<strong>No previous harvest.</strong><br />"; }
$query3="select ID_harvest from harvests where ID_site=$site and (h_date>curdate() or substring(h_date,-5)='00-00')";
$rsPending=mysqli_query($piercecty, $query3);
$numrows3=mysqli_num_rows($rsPending); 
if($numrows3>0) echo "There is a pending harvest of this site on the calendar.<br />";
if($row['Active']<>'No') { // show new harvest link only for active sites
?><a href="harvestinsert.php?sitetemp=<?php echo $row['ID_site']; ?>&submit=Show+records&MM_filter=filtersform">Add a new harvest</a>
<br /><br /><?php  } // end of else show new harvest link
?>

<a href="siteupdate.php?sitetemp=<?php echo $row['ID_site']; ?>">Update site details</a><br />
<a href="sitedetail.php?sitetemp=<?php echo $row['ID_site']; ?>">More site details</a><br />
<?php $googleadd=$row['address'].",".$row['city'].", ".$row['state']." ".$row['zip'];?>
<a href="https://maps.google.com/maps?hl=en&q=<?php echo $googleadd; ?>" target="_blank">Google Maps</a><br />
<br /><?php 
echo "<strong>Other information:</strong> ".$row['otherinfo'].'<br />';

?>
  </div>
  <!-- end of pop -->
  <?php } // end of build popups loop---------------------------------------------------------------------
  ?>
  <div id="filtswitchdiv"><!-- data filters (here mainly for  spacing ---------------------------- -->
    <div id="filtersdiv">
      <form action="" method="POST" name="filters">
        <table  border="0" align="center" cellpadding="5" cellspacing="5">
          <tr>
            <th>Status
              <select name="Status">
                <option value="All" <?php if($postStatus=='All') echo 'selected="selected"';?>>All</option>
                <option value="Active" <?php if($postStatus=='Active') echo 'selected="selected"';?>>Active</option>
                <option value="Inactive" <?php if($postStatus=='Inactive') echo 'selected="selected"';?>>Inactive</option>
              </select>
            </th>
            <th>Site name
             	<input width = "20"  type="text" name="Name" value="<?php if($postName<>'') echo $postName; ?>"/>
            </th>
          <th>Order by
          	<select name="order">
            		<option value="farm" <?php if($postOrder=='farm') echo 'selected="selected"';?>>Site Name</option>
		            <option value="regdate" <?php if($postOrder=='regdate') echo 'selected="selected"';?>>Registration date</option>
		    </select></th>
            <th><input name="submit" type="submit" value="Filter" /></th>
          </tr>
        </table>
      </form>
    </div><!-- end of filters div -->
  </div> <!-- end of filtswitchdiv -->
  <div id="list">
    <table id="harvests" width="700" align="center">
      <tr>
        <th>Site name</th>
        <th>Contact</th>
        <th>Crops</th>
        <th>Reg. date</th>
      </tr>
      <?php
// loop through harvests and build list table ---------------------------------------------------
if($numrows>0) mysqli_data_seek($rsSites, 0);
while ($row = mysqli_fetch_assoc($rsSites)) { 
extract($row);
?>
      <tr onmouseover="popup(pop<?php echo $ID_site;?>)">
        <td><?php echo "<a href='siteupdate.php?sitetemp=$ID_site'>$farm</a>"; ?></td>
        <td><?php echo $contact1; ?></td>
        <td><?php echo $crops; ?></td>
        <td><?php echo $regdate; ?></td>
      </tr>
      <?php } // end of harvests loop --------------------------------------------------------------
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
<?php
((mysqli_free_result($rsSites) || (is_object($rsSites) && (get_class($rsSites) == "mysqli_result"))) ? true : false);
?>
