<?php 
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');

if (!isset($_SESSION))  session_start(); 
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$delswitch='no';
if(isset($_POST['jobID']))  $jobID=$_POST['jobID'];

if(isset($_POST['update']) and $_POST['jobID']<>'') { // update job
$jobID=GetSQLValueString($_POST['jobID'], "int");

$updatequery = sprintf("UPDATE jobs SET jobname=%s, jobtext=%s where jobID=$jobID",
                       GetSQLValueString($_POST['jobname'], "text"),
                       GetSQLValueString($_POST['jobtext'], "text"));
$Result1 = mysqli_query($piercecty, $updatequery) or die(mysqli_error($piercecty));

} // end of update
$queryjobs="select * from jobs order by jobID";
$rsUsers = mysqli_query( $piercecty, $queryjobs) or die(mysqli_error($piercecty));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>jobs manager</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2><strong>Jobs manager</strong></h2>
    <table  border="1" cellpadding="1" cellspacing="1">
      <tr>
        <th>Index</th>
        <th>Job name</th>
        <th>Job text</th>
        <th>&nbsp;</th>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($rsUsers)) { 
	  	if($delswitch=='no' or ($delswitch='yes' and $row['jobID']==$jobID)) {
	  ?>
	    <form action="jobsmanager.php" name="jobs" method="post">
        <tr class="centercell">
          <td><input name="jobID" type="hidden" value="<?php echo $row['jobID']; ?>" /><?php echo $row['jobID']; ?></td>
          <td><input name="jobname" type="text" value="<?php echo $row['jobname']; ?>" size="15" maxlength="15" /></td>
          <td><input name="jobtext" type="text" value="<?php echo $row['jobtext']; ?>" size="100" maxlength="200" /></td>
          <td>&nbsp;<input type="submit" name="update" value="update" />&nbsp;</td>
          <input type="hidden" name="jobID" value="<?php echo $row['jobID'];?>" />
        </tr>
          </form>
        <?php } // end of if delswitch
		  } // end of all jobs loop
		 ?>
</table>
  <br class="clearfloat" />
  <!-- end #container --></div>
</div>
</body>
</html>
