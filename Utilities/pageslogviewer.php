<?php
if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../Connections/piercecty.php'); 

$users="select who, max(whenview) as recent  from pageslog group by who order by max(whenview) desc";
$rsUsers=mysqli_query($piercecty, $users);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pages tracking</title>
    <style type="text/css">
<!--
.supportingP {
display: none;
margin-left: 4em;
 }
-->
</style>
<script type="text/javascript" charset="utf-8">
   function showSP(sid,morelink){
	  var el = document.getElementById(sid);
	  el.style.display = (el.style.display!='block') ? 'block' : 'none';
	  morelink.innerHTML = (morelink.innerHTML==='show') ? 'hide' : 'show';
	  }
</script>

<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2><strong>Pages tracking log viewer</strong></h2>

<?php while ($row2=mysqli_fetch_assoc($rsUsers)) {
	$who=$row2['who']; $recent=$row2['recent'];
	$views="select * from pageslog where who='$who' order by whenview desc";
	$rsViews=mysqli_query($piercecty, $views);
	
	echo "<p style='font-size: 14pt'>".$who."<br />".$recent." ";?><a href='#' onClick="showSP('<?php echo $who;?>',this);return false">show</a></p>
	<div id="<?php echo $who;?>" class="supportingP">
	<?php 
		echo "<table border='1' cellpadding='3' cellspacing='2'>";
			while ($row=mysqli_fetch_assoc($rsViews)) { 
			echo "<tr><td>".$row['pageview']."</td><td>".$row['whenview']."</td></tr>";
			}?>
		</table>
	</div>
    <p>
      <?php } //end of names
?>
    </p>
    <p>&nbsp;</p>
</div>
</div>
</body>
</html>
