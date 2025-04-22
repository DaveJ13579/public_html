<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
?>
<?php require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');


$IP = "";
if (isset($_GET['IPtemp'])) { $IP = $_GET['IPtemp']; }

$queryIPpicker = "SELECT ID_picker, lname, fname, email, IP_picker FROM pickers WHERE '$IP'=IP_picker";
$rsPicker = mysqli_query($piercecty, $queryIPpicker) or die(mysqli_error($piercecty));
$pickerrow = mysqli_fetch_assoc($rsPicker);

$queryIProster = "SELECT rosters.ID_picker, pickers.fname, pickers.lname, pickers.email, IPaddress, ID_harvest from pickers, rosters where pickers.ID_picker=rosters.ID_picker and '$IP'=IPaddress";
$rsRoster = mysqli_query($piercecty, $queryIProster) or die(mysqli_error($piercecty));
$rosterrow= mysqli_fetch_assoc($rsRoster);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IP address finder</title>
    <style type="text/css">
<!--
-->
    </style>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2><strong>IP Address finder</strong></h2>

      <form id="IP" name="IP" method="get" action="IPfinder.php">
      <label>IP address
        <input width = "500"  type="text" style="background-color:#aaf969" name="IPtemp" id="IPtemp" value="<?php echo $IP ?>"/>
      </label>
     and press 'Enter'
      </form>

    <p>&nbsp;</p>
    <table width="1220" border="1" cellpadding="2" cellspacing="2" id="Pickerlist">
      <tr>
        <th>Detail<br />
          Page</th>
        <th>Name</th>
        <th>Email</th>
        <th></th>
      </tr>
      
      <?php do { ?>
        <tr>
        <td class="centercell"><a href="voldetail.php?voltemp=<?php echo $pickerrow['ID_picker']; ?>"><strong><?php echo $pickerrow['ID_picker']; ?></strong></a></td>
        <td><?php echo $pickerrow['fname']; ?> <?php echo $pickerrow['lname'];?></td>
        <td><?php echo $pickerrow['email'];?></td>
        </tr>
        <?php } while ($pickerrow = mysqli_fetch_assoc($rsPicker)); ?>     
      <tr>
        <th>Detail<br />
          Page</th>
        <th>Name</th>
        <th>Email</th>
        <th>Harvest number</th>
      </tr>
      
      <?php do { ?>
        <tr>
        <td class="centercell"><a href="voldetail.php?voltemp=<?php echo $rosterrow['ID_picker']; ?>"><strong><?php echo $rosterrow['ID_picker']; ?></strong></a></td>
        <td><?php echo $rosterrow['fname']; ?> <?php echo $rosterrow['lname'];?></td>
        <td><?php echo $rosterrow['email'];?></td>
        <td><?php echo $rosterrow['ID_harvest'];?></td>
        </tr>
        <?php } while ($rosterrow = mysqli_fetch_assoc($rsRoster)); ?>     


     
    </table>
    <p>&nbsp;</p>
    </div>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
<?php
((mysqli_free_result($rsPicker) || (is_object($rsPicker) && (get_class($rsPicker) == "mysqli_result"))) ? true : false);
((mysqli_free_result($rsRoster) || (is_object($rsRoster) && (get_class($rsRoster) == "mysqli_result"))) ? true : false);
?>
