<?php require_once('../Connections/piercecty.php'); 

require_once('../includes/dencode.inc.php');
if (!isset($_SESSION)) {   session_start(); }

$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

$postGroup='All';  // default POSTed variable
$fGroup=' order by lname, fname'; // default filter term
$numrows=0;
$encoded='';

if(isset($_POST['submit']) and $_POST['nametemp']=='') { // filters are posted  -----------------------------------

$postGroup=$_POST['Group'];
// update filter values from posted filters
if($postGroup=='All') $fGroup=" order by lname, fname";
if($postGroup=='Recent') $fGroup="  and contactdate>date_sub(now(), interval 1 year)  order by lname, fname";
if($postGroup=='Recent-date') $fGroup=" and contactdate>date_sub(now(), interval 1 year)  order by contactdate desc";
if($postGroup=='Leaders') $fGroup=" and leader='Yes' order by lname, fname";
} // end of isset filters ---------------------------------------------------------------

// get data for list
$query = "SELECT ID_picker, fname, lname, email, phone from pickers where ID_picker<>0 $fGroup ";
// echo $query;
$rsPickers = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$numrows = mysqli_num_rows($rsPickers);

$colname_rsName = "";
if (isset($_POST['nametemp']) and $_POST['nametemp']<>'') { 
$colname_rsName = $_POST['nametemp'];

$sfield='lname'; 
if(substr($colname_rsName,0,1)==' ') { $sfield='fname'; $colname_rsName=trim($colname_rsName); }
if(substr($colname_rsName,0,1)=='-') { $sfield='email'; $colname_rsName=substr($colname_rsName,1,strlen($colname_rsName)-1); }
if(substr($colname_rsName,0,1)=='+') { $sfield='decode'; $colname_rsName=substr($colname_rsName,1,strlen($colname_rsName)-1); }
if(substr($colname_rsName,0,1)=='.') { $sfield='encode'; $colname_rsName=substr($colname_rsName,1,strlen($colname_rsName)-1); }
if($sfield=='decode') $colname_rsName=decode($colname_rsName);
if($sfield=='encode')  $encoded=encode($colname_rsName); 

if (is_numeric($colname_rsName)) {  // POST input (or decoded input) is numeric
$ID_picker = intval($colname_rsName);
$query_rsName = sprintf("SELECT * FROM pickers WHERE ID_picker = '$ID_picker' ORDER BY ID_picker ASC");
$rsPickers = mysqli_query($piercecty, $query_rsName) or die(mysqli_error($piercecty));
$numrows=mysqli_num_rows($rsPickers);

} else {  // input by POST is not numeric

$long=strlen(stripslashes($colname_rsName));
if($long==0) {$long=1; }
$query_rsName = "SELECT * FROM pickers WHERE left($sfield,'$long') = '$colname_rsName' ORDER BY lname ASC";
$rsPickers = mysqli_query($piercecty, $query_rsName) or die(mysqli_error($piercecty));
$numrows=mysqli_num_rows($rsPickers);
} // end of post not numeric
} // end of post nametemp
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Picker list</title>
<link href="../database.css" rel="stylesheet" type="text/css" />

<style type="text/css">
.ajaxdetails { float: right; 	width: 465px; height: 655px; overflow: auto; padding: 5px; }
#filtswitchdiv2 { width: 700px; 	float: left; }
#textfield { float: left; width: 700px; }
.list2 { 	float: left; height: 500px; width: 720px; overflow: scroll; }
</style>

<script type="text/javascript">
function getdetails(ID){
request = new XMLHttpRequest();
request.onreadystatechange = function() 
{	if (request.readyState == 4)
	{	if (request.status >= 200 && request.status< 300 || request.status == 304) 
		{ document.getElementById('pickerinfo').innerHTML = this.responseText }
	}
}
request.open("GET", "pickerlist-ajax.php?ID="+ID, true);
request.send(null);
// send picker number to server to process into div text
// get back div text and insert it
}
</script>
</head>
<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">

<div class="ajaxdetails">
<p id="pickerinfo">Mouse over lines in the table for details</p>
</div>
 <!-- end of pop -->
 
     <form action="" method="POST" name="filters">
<div id="filtswitchdiv2"><!-- data filters (here mainly for  spacing ---------------------------- -->
    <div id="filtersdiv">
        <table  border="0" align="center" cellpadding="5" cellspacing="5">
          <tr>
            <th>Group
              <select name="Group">
                <option value="Recent" <?php if($postGroup=='Recent') echo 'selected="selected"';?>>Recent a-z</option>
                <option value="Recent-date" <?php if($postGroup=='Recent-date') echo 'selected="selected"';?>>Recent by date</option>
                <option value="All" <?php if($postGroup=='All') echo 'selected="selected"';?>>All</option>
                <option value="Leaders" <?php if($postGroup=='Leaders') echo 'selected="selected"';?>>Leaders</option>
              </select>
            </th>
            <th>Search</th>
            
             <th><input name="submit" type="submit" value="Filter" /></th> 
          </tr>
        </table>
    </div><!-- end of filters div -->
  <div id="textfield"> <!-- text field div -->
        <input width = "500"  type="text" style="background-color:#ddd" name="nametemp" id="nametemp" />
        Type letters from the start of the last name OR the ID number OR letters from the start of the first name preceded by a space OR the start of the email address preceded by a dash(-) and press 'Enter' [Advanced users: '.' precedes encode a number; '+' precedes decode a number]<p/>
   </div> <!-- end of textfield div -->
   </div> <!-- end of filtswitchdiv -->
      </form>
  <div class="list2">
    <table id="harvests" width="700" align="center">
      <tr>
        <th>ID number</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
      </tr>
      <?php
// loop through pickers and build list table ---------------------------------------------------
if($numrows>0) {
mysqli_data_seek($rsPickers, 0);
while ($row = mysqli_fetch_assoc($rsPickers)) { 
?>
      <tr onmouseover="getdetails(<?php echo $row['ID_picker'];?>)">
        <td><?php echo $row['ID_picker']; if($encoded<>'') echo " [ ".$encoded." ]"; ?></td>
        <td><?php echo $row['lname'].", ".$row['fname']; ?></td>
        <td><?php echo $row['email'];?></td>
        <td><?php echo $row['phone']; ?></td>
      </tr>
<?php } // end of pickers loop 
} // end of if numrows>0 --------------------------------------------------------------
?>
    </table>
  <!-- end of list div----------------------------------------------- -->
</div>
</div><!-- end of mainContent -->
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
<br class="clearfloat" />
</div><!-- end #container -->
</body>
</html>
