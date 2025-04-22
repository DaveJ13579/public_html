<?php
if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../Connections/piercecty.php'); 
require_once('../includes/dencode.inc.php');
require_once('../includes/sqlcleaner.php');

$colname_rsName = "";
if (isset($_GET['nametemp'])) { $colname_rsName = $_GET['nametemp']; }

$sfield='lname'; $encoded='';
if(substr($colname_rsName,0,1)==' ') { $sfield='fname'; $colname_rsName=trim($colname_rsName); }
if(substr($colname_rsName,0,1)=='-') { $sfield='email'; $colname_rsName=substr($colname_rsName,1,strlen($colname_rsName)-1); }
if(substr($colname_rsName,0,1)=='+') { $sfield='decode'; $colname_rsName=substr($colname_rsName,1,strlen($colname_rsName)-1); }
if(substr($colname_rsName,0,1)=='.') { $sfield='encode'; $colname_rsName=substr($colname_rsName,1,strlen($colname_rsName)-1); }
if($sfield=='decode') $colname_rsName=decode($colname_rsName);
if($sfield=='encode')  $encoded=encode($colname_rsName); 
if (is_numeric($colname_rsName)) {  // GET input (or decoded input) is numeric

$ID_picker = intval($colname_rsName);
$query_rsName = sprintf("SELECT * FROM pickers WHERE ID_picker = '$ID_picker' ORDER BY ID_picker ASC");
$rsName = mysqli_query($piercecty, $query_rsName) or die(mysqli_error($piercecty));
$numrows=mysqli_num_rows($rsName);

} else {  // input by get is not numeric

$long=strlen(stripslashes($colname_rsName));
if($long==0) {$long=1; }
$colname_rsName=GetSQLValueString($colname_rsName, "text"); 
$query_rsName = "SELECT * FROM pickers WHERE left($sfield,'$long') = $colname_rsName ORDER BY lname ASC";
$rsName = mysqli_query($piercecty, $query_rsName) or die(mysqli_error($piercecty));
$numrows=mysqli_num_rows($rsName);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Picker finder</title>
    <style type="text/css">
<!--
.details {
	padding: 10px;
	display: none;
	float: right;
	width: 400px;
	border-top-width: 1px;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	position: relative;
}
-->
    </style>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript">
<!--
function hide_popup(popupToHide){popupToHide.style.display="none"}
function show_popup(popupToShow){popupToShow.style.display="block"}
function setFocus() { document.getElementById('nametemp').focus();}
//-->
</script>
</head>
<body class="SH" onLoad="setFocus()">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php'); ?>
  <div id="mainContent">
<h2><strong>Picker finder</strong></h2>

      <form id="lastname" name="lastname" method="get">
      <label>Type letters from the start of the last name OR the ID number OR letters from the start of the first name preceded by a space OR the start of the email address preceded by a dash(-). [ Advanced users: '.' precedes encode a number; '+' precedes decode a number]<br />
        <input width = "500"  type="text" style="background-color:#aaf969" name="nametemp" id="nametemp" />
      </label>
     and press 'Enter'
      </form>
	<div width="1200">Number found: <?php if(isset($_GET['nametemp'])) { if(isset($numrows)) echo $numrows; } ?></div>
	<?php
	// build detail blocks as divs of class 'details'
	while ($row = mysqli_fetch_assoc($rsName))  { //build all detail blocks
	$ID=$row['ID_picker'];
		// build this block
		$block=$row['fname']." ".$row['lname']."<br />";
		$block.=$row['email']."<br />";
		$block.=$row['phone']."<br />";
		$block.=$row['address'].", ".$row['city'].", ".$row['state']." ".$row['zip']."<br />";
		$block.="Harvest leader? ".$row['leader']."<br />";
		$block.="Leader? ".$row['leader']."<br />";
		$block.="Tree scout? ".$row['scout']."<br />";
		$block.="Most recent contact: ".$row['contactdate']."<br />";
		$block.="Registration date: ".$row['regdate']."<br />";
		$block.="Duplicate name? ".$row['dupname']."<br />";
		
	?><div class="details" id="d<?php echo $ID;?>">
    	<?php echo $block; ?></div>

	<?php }  // end of build detail blocks ?>
 <div>
    <table width="800" border="1" cellpadding="5" cellspacing="5" id="Pickerlist"> 
        <tr>
        <?php
		if($numrows>0) mysqli_data_seek($rsName,  0);
		$colct=1; // initialize column count
	  	while ($row = mysqli_fetch_assoc($rsName)) {  ?> 
				<td  onmouseover="show_popup(<?php echo "d".$row['ID_picker'];?>)" 
          		 onmouseout="hide_popup(<?php echo "d".$row['ID_picker'];?>)">
                 <a href="voldetail.php?voltemp=<?php echo $row['ID_picker'];?>"><?php echo $row['ID_picker']." ".$row['fname']." ".$row['lname']." ".$encoded;?></a></td>
                 
        <?php ++$colct; if($colct==6) {$colct=1; echo "</tr><tr>"; } // if done 3 columns go to new row
         }  // end of do all matching names ?>      
        </tr>
    </table>
    <p>&nbsp;</p>
    </div>
  </div>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
<?php
((mysqli_free_result($rsName) || (is_object($rsName) && (get_class($rsName) == "mysqli_result"))) ? true : false);
?>
