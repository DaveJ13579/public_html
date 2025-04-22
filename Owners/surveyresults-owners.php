<?php 
require_once('../Connections/piercecty.php'); 
include_once('../includes/converter.inc.php');

if (!isset($_SESSION)) { session_start(); }
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$sw='sort by crop owner';
if(isset($_POST['switch'])) $sw=$_POST['switch'];

$query = "SELECT surveydate, surveyowner.ID_harvest, most, better, other, quote, h_date, farm, contact1 FROM surveyowner, harvests, sites where surveyowner.ID_harvest=harvests.ID_harvest and harvests.ID_site=sites.ID_site order by h_date desc";
$rsSurvey = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$numrows=mysqli_num_rows($rsSurvey);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Survey results</title>
    <style type="text/css">
<!--
#results tr th {
	text-align: left;
	padding-left: 50px;
}
-->
    </style>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php'); ?>
    <div id="mainContent">
    <h2>Crop owners survey results</h2>
    
<?php if($sw=='Sort by survey question') { ?>
<form method="POST" ><table><tr><td><input name="switch" type="submit" value="Sort by crop owner" /></td></tr></table></form>
    <table width="1240" border="1" cellpadding="1" cellspacing="1" id="results">
      <tr>
        <th>What did you like the most about your harvest?</th></tr>
      <tr><td><?php mysqli_data_seek( $rsSurvey, 0);
	  		while ($row = mysqli_fetch_assoc($rsSurvey))
			{ echo $row['most']."<br />"; }  ?>
      </td></tr>
      
      <tr><th>What could we have done better?</th></tr>
      <tr><td><?php mysqli_data_seek( $rsSurvey, 0);
	  		while ($row = mysqli_fetch_assoc($rsSurvey))
			{ echo $row['better']."<br />"; }  ?>
      </td></tr>
      
      <tr><th>Do you have any other comments or suggestions?</th></tr>
      <tr><td><?php mysqli_data_seek( $rsSurvey, 0);
	  		while ($row = mysqli_fetch_assoc($rsSurvey)) 
			{ echo $row['other']."<br />"; }  ?>
      </td></tr>
    </table> 
<?php } //end of sort by question
else { ?>
	<form method="POST"><table><tr><td><input name="switch" type="submit" value="Sort by survey question" /></td></tr></table></form>
<table width="1220" border="1" cellpadding="1" cellspacing="1" id="results">
	  	<?php 
        while ($row = mysqli_fetch_assoc($rsSurvey)) { ?>
			<tr>
			<th width="100">Harvest date:<br  /><?php echo $row['h_date']; ?></th>
			<th width="150">Crop owner:<br  /><?php echo $row['contact1']." ".$row['farm']; ?></th>
			<th>Crop type: <?php $crops=cropstring($row['ID_harvest']); echo $crops; ?></th>
			<th>Harvest number: <?php echo $row['ID_harvest']; ?></th>
			</tr>
			<tr><td></td>
			<th>Liked most:</th>
			<td colspan='3'><?php echo $row['most']; ?></td></tr>
			<tr><td></td>
			<th>Better next year:</th>
			<td colspan='3'> <?php echo$row['better']; ?></td></tr>
			<tr><td></td>
			<th>Other comments:</th>
			<td colspan='3'> <?php echo $row['other']; ?></td></tr>
			<tr><td></td>
			<th>May quote me?</th>
			<td colspan='3'> <?php echo $row['quote']=='Yes' ? 'Yes' : 'No'; ?>
			</td></tr>
            <tr><td colspan="4"></td></tr>
			<?php } // end of while rows ?>
	</table>

<?php  } // end of else sort by owner ?>
  </div>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
<?php
((mysqli_free_result($rsSurvey) || (is_object($rsSurvey) && (get_class($rsSurvey) == "mysqli_result"))) ? true : false);
?>
