<?php require_once('../Connections/piercecty.php');
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

$query_rsDistsites = "select * from distsites order by name";
$rsDistsites = mysqli_query($piercecty, $query_rsDistsites) or die(mysqli_error($piercecty));
$totalRows_rsDistsites= mysqli_num_rows($rsDistsites);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Distribution sites list</title>
<link href="../database.css" rel="stylesheet" type="text/css" />
</head>
<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php'); ?>

<div id="mainContent">
<h2 class="SH"><strong>Distribution sites list</strong></h2>
<?php echo $totalRows_rsDistsites.' distribution sites'; ?>
<table width="100%" border="1">
      <tr class="center_cell">
        <th>Update<br />link</th>
        <th>Name</th>
        <th>Address</th>
        <th>Contact</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Type</th>
      </tr>
      <?php 
      while($distsiterow = mysqli_fetch_assoc($rsDistsites)) { ?>
      <tr>
        <td class="centercell"><a href="distributionsitesmanager.php?distsitetemp=<?php echo $distsiterow['distsite']; ?>" target="_blank"><?php echo $distsiterow['distsite']; ?></a></td>
        <td><?php echo $distsiterow['name']; ?></td>
        <td><?php echo $distsiterow['address']; ?></td>
        <td class="centercell"><?php echo $distsiterow['contact']; ?></td>
        <td class="centercell"><?php echo $distsiterow['phone']; ?></td>
        <td class="centercell"><?php echo $distsiterow['email']; ?></td>
        <td class="centercell"><?php echo $distsiterow['distsitetype']; ?></td>
      </tr>
      <?php 
	  } // end of rsDistsites
	  ?>
 </table></div>
<br class="clearfloat" />
  <div id="footer">
    <!-- end #footer -->
  </div>
<!-- end #container --></div>
</body>
</html>
