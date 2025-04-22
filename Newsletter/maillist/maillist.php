<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "all,change,view";


$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');

require_once('../../Connections/piercecty.php'); 
require_once('../../includes/sqlcleaner.php'); 



$query = "SELECT * FROM maillist ORDER BY lname";
$rsMail = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$row = mysqli_fetch_assoc($rsMail);
$Numrows = mysqli_num_rows($rsMail);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>supplemental mailing list</title>
<style type="text/css">
<!--
-->
</style>
<link href="../../database.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH">
<div id="container">
<?php include_once('../../includes/AdminNav3.inc.php');?>
<div id="mainContent">
    <h2 class="SH"><strong>Supplemental mailing list</strong></h2>
    <a href="mailinsert.php">Add a record</a>
    <table width="1200" border="1" align="center">
      <tr class="center_cell">
        <th>Name</th>
        <th>Organization</th>
        <th>Email</th>
        <th>Phones</th>
        <th>Address</th>
        <th>Other information</th>
      </tr>
      <?php do { ?>
		<tr>        
        	<td><a href="mailupdate.php?temp1=<?php echo $row['ID_mail']; ?>"><?php echo $row['lname'];
				if($row['fname']<>'') { echo ", ".$row['fname']; }?></a></td>
        	<td><?php echo $row['organization']?></td>
        	<td><?php echo $row['email']?></td>
        	<td><?php echo $row['phone']."<br />".$row['phone2'] ?></td>
        	<td><?php echo $row['address']."<br />".$row['city'].", ".$row['state']." ".$row['zip'] ?></td>
        	<td><?php echo $row['otherinfo']?></td>
      </tr>
       <?php } while ($row = mysqli_fetch_assoc($rsMail)); ?>
    </table></div><!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
  <div id="footer">
    <!-- end #footer -->
  </div>
<!-- end #container --></div>
</body>
</html>
<?php
((mysqli_free_result($rsMail) || (is_object($rsMail) && (get_class($rsMail) == "mysqli_result"))) ? true : false);
?>
