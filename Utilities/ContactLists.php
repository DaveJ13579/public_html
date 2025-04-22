<?php
if (!isset($_SESSION)) session_start(); 
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../Connections/piercecty.php');
require_once('../includes/branch.inc.php');

$query=''; $where=''; $err=''; $branch='';
$Listtitle='no title yet'; $format='';
$numrows=0;

//start of form 
if (isset($_POST['submit']))  {

$harvnum = isset($_POST['harvnum']) ? $_POST['harvnum'] : '';
$branch=(isset($_POST['branch']) and $_POST['branch']<>'') ? $_POST['branch'] : '';
$choice = isset($_POST['choice']) ? $_POST['choice']: '';

switch($choice) {
case 'volsharv':
	$Listtitle = "Volunteers who led"; 	$where="rosters.status = 'leader'"; break;
case 'sign':
	$Listtitle = "Volunteers who signed up for"; $where="rosters.status = 'signup'"; 	break;
case 'harv':
	$Listtitle = "Volunteers who harvested at"; $where="rosters.status = 'harvested'"; break;
case 'abse':
	$Listtitle = "Volunteers who were absent from"; $where="rosters.status = 'absent'"; break;
case 'adde':
	$Listtitle = "Volunteers who were added to"; $where="rosters.status = 'added'"; 	break;
case 'part':
	$Listtitle = "All participants at"; $where="rosters.status <> 'absent'  and rosters.status <> 'cancel'";
	} // end of switch
if($where) {	
	$query="select distinct(pickers.ID_picker), pickers.fname as first, pickers.lname as last, email, address, city, state, zip, phone from rosters, pickers where rosters.ID_picker=pickers.ID_picker and ".$where;
	$titleadd=' all harvests';
	if($harvnum) { $query.=' and rosters.ID_harvest='.$harvnum;
		$titleadd=' harvest '.$harvnum;
	}
$Listtitle.=$titleadd;
} // end of if any harvest options selected

if(!$where){ // start of volunteer lists
switch($choice) {
case 'pick':
	$Listtitle = "Registered volunteers"; $where = "1=1"; break;
case 'lead':
	$Listtitle = "Harvest leaders"; $where = "leader<>''"; break;
} // end of volunteers switch
	if($where){ $query="select distinct(pickers.ID_picker), fname as first, lname as last, email, address, city, state, zip, phone from pickers where ".$where;} 
} // end of check volunteers lists

if($choice=='owne') { // crop owners
	$Listtitle = "Site owners"; $query="select distinct(maddress) as address, contact1 as first, farm as last, mcity as city, mstate as state, mzip as zip, phone1 as phone, email1 as email from sites where Active='Yes'";
}

if($branch) {
$zipsq="select zips from branches where branch='$branch'";
$rsZips=mysqli_query($piercecty,$zipsq) or die(mysqli_error($piercecty));
$ziprow=mysqli_fetch_assoc($rsZips);
$zips=$ziprow['zips'];
$zips = preg_replace("/[^0-9\,]+/", "", $zips);
$query="select fname as first, lname as last, email, address, city, state, zip, phone from pickers where zip in ($zips)";
}
// echo $query.'<br />';
if($query=='') { $err='no query yet. '; } else
{ $rsList = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
if(!$rsList) $err='No lines in report - check inputs';
$numrows = $rsList ? mysqli_num_rows($rsList) : 0;
}
// fields
$firstfield=isset($_POST['first']) ? $_POST['first'] : '';
$lastfield=isset($_POST['last']) ? $_POST['last'] : '';
$bothfield=isset($_POST['both']) ? $_POST['both'] : '';
$addressfield=isset($_POST['address']) ? $_POST['address'].' ' : '';
$emailfield=isset($_POST['email']) ? $_POST['email'] : '';
$phonefield=isset($_POST['phone']) ? $_POST['phone'] : '';
// format
$format=isset($_POST['format']) ? $_POST['format'] : '';
} // end of if Submit

// csv formats
if(($format=='csv' || $format=='csv-noquotes') && $err=='') { 
if($format=='csv') { // csv with quotes
	$str='';
while ($row = mysqli_fetch_assoc($rsList)) {	
	$fname= isset($row['first']) ? $row['first'] : ' ';
	$lname= isset($row['last']) ? $row['last'] : ' ';
	$both= isset($row['last']) ? $row['first'].' '.$row['last'] : ' ';
	$address= isset($row['address']) ? $row['address'] : ' ';
	$city= isset($row['city']) ? $row['city'] : ' ';
	$state= isset($row['state']) ? $row['state'] : ' ';
	$zip= isset($row['zip']) ? $row['zip'] : ' ';
	$email= isset($row['email']) ? $row['email'] : ' ';
	$phone= isset($row['phone']) ? $row['phone'] : ' ';

	if($firstfield) $str.=chr(34).$fname.chr(34).',';
	if($lastfield)  $str.=chr(34).$lname.chr(34).',';
	if($bothfield)  $str.=chr(34).$both.chr(34).',';
	if($phonefield) $str.=chr(34).$phone.chr(34).',';
	if($emailfield) $str.=chr(34).$email.chr(34).',';
	if($addressfield) $str.=chr(34).$address.chr(34).','.chr(34).$city.chr(34).','.chr(34).$state.chr(34).','.chr(34).$zip.chr(34).',';
	$str=substr($str,0,-1).chr(13).chr(10); 
} // end of while $row
} // end of csv with quotes

if($format=='csv-noquotes' && $err=='') { 
	$str='';
while ($row = mysqli_fetch_assoc($rsList)) {	
	$fname= isset($row['first']) ? str_replace(',',' ',$row['first']) : ' ';
	$lname= isset($row['last']) ? str_replace(',',' ',$row['last']) : ' ';
	$both= isset($row['last']) ? str_replace(',',' ',$row['first'].' '.$row['last']) : ' ';
	$address= isset($row['address']) ? str_replace(',',' ',$row['address']) : ' ';
	$city= isset($row['city']) ? str_replace(',',' ',$row['city']): ' ';
	$state= isset($row['state']) ? str_replace(',',' ',$row['state']) : ' ';
	$zip= isset($row['zip']) ? str_replace(',',' ',$row['zip']) : ' ';
	$email= isset($row['email']) ? str_replace(',',' ',$row['email']) : ' ';
	$phone= isset($row['phone']) ? str_replace(',',' ',$row['phone']) : ' ';

	if($firstfield) $str.=$fname.',';
	if($lastfield)  $str.=$lname.',';
	if($bothfield)  $str.=$both.',';
	if($phonefield) $str.=$phone.',';
	if($emailfield) $str.=$email.',';
	if($addressfield) $str.=$address.','.$city.','.$state.','.$zip.',';
	$str=substr($str,0,-1).chr(13).chr(10); 
} // end of while $row
} // end of csv no quotes

// save csv string as file templist.csv
$filename="templist.csv";
$file=fopen($filename,'w');
fwrite($file,$str);
fclose($file);
//download file
if (file_exists($filename)) { 
ob_end_clean();
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.basename($filename));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));
ob_clean();
flush();
readfile($filename);
exit;
} else {echo 'file not found'; }
} // end of csv formats

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Email Lists</title>
<style type="text/css">
<!--
-->
</style>
<link href="../database.css" rel="stylesheet" type="text/css" />
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2 class="SH"><strong>Email, mailing and phone lists</strong></h2>
    <h3><center>
      <p>Select group, fields to include, and output format.</p>
    </center></h3>
    <div> <!-- start of input section div -->
      <form name="criteriapick" method="post" action="">
        <table width="600" border="5" align="center" cellpadding="5" cellspacing="5">
          <tr>
<th>From Harvest Rosters</th>
                  <th>From Lists</th>
		  </tr>
          <tr><td><input name="harvnum" type="text" value="" size="5" maxlength="4" />
          harvest number (blank = all)</td>
          <td><label><input type="radio" name="choice" value="pick" />
          All registered volunteers</label></td>
          </tr>          
          <tr><td><label>
            <input name="choice" type="radio" value="volsharv"  />            
          All leaders</label></td>
          <td>
			Volunteers in branch 
			<select name="branch">
         <option value=''> </option>
<?php 
$branchq="select branch from branches order by branch";
$rsBranch=mysqli_query($piercecty,$branchq);
while($branchrow=mysqli_fetch_assoc($rsBranch)) { ?>          			
	<option value="<?php echo $branchrow['branch']; ?>" <?php if(isset($branch) and $branch==$branchrow['branch']) echo 'selected="selected"'; ?>><?php echo $branchrow['branch'];?></option>
<?php } ?>
			</select>
			</td> 
</tr>
          <tr><td><label><input type="radio" name="choice" value="part" />
          All participants</label></td>
          <td><label><input type="radio" name="choice" value="owne" />
          Harvesting sites (Active only)</label></td></tr>
          
          <tr><td><label><input type="radio" name="choice" value="sign" />
          'Signup' volunteers</label></td>
			<td><label><input type="radio" name="choice" value="lead" />
          Harvest leaders</label></td></tr>
          
          <tr><td><label><input type="radio" name="choice" value="harv" />
          'Harvested' volunteers</label></td>
          <td></td></tr>
            
          <tr><td><label><input type="radio" name="choice" value="abse" />
          'Absent' volunteers</label></td>
          <td></td></tr>

          <tr><td><label><input type="radio" name="choice" value="adde"/>
          'Added' volunteers</label></td>
          <td></td></tr>
          <tr>
          <th colspan="2">Select fields to include</th>
          </tr>
          <tr>
          <td  colspan="2" style="text-align:center;">
              <input type="checkbox" name="first" value="first"/>First Name&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="checkbox" name="last" value="last"/>Last Name&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="checkbox" name="both" value="both"/>First and Last&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="checkbox" name="email" value="email"/>Email address&nbsp;&nbsp;&nbsp;&nbsp;<br />
              <input type="checkbox" name="address" value="address"/>Mailing address&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="checkbox" name="phone" value="phone"/>Telephone number&nbsp;&nbsp;&nbsp;&nbsp;
			</td>
          </tr>
          <tr>
          <th colspan="2">Select output format</th>
          </tr>
          <tr>
          <td  colspan="2" style="text-align:center;">
              <input type="radio" name="format" value="app"/>Email app&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="format" value="excel"/>Table&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="format" value="csv"/>.csv file (w/ quotes)&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="format" value="csv-noquotes"/>.csv file (w/o quotes)&nbsp;&nbsp;&nbsp;&nbsp;
			</td>
          </tr>
        </table>
        <p class="centercell">
		<input  type="submit" name="submit" value="Make the list" />
        </p>
	  </form>
    </div> <!-- end of input section div -->
    <br class="clearfloat" />
    <div id="output">
<?php 
echo "<h3 align='center'>".$Listtitle."</h3>";
// output the count 
echo "<p  align='center'>total = ".$numrows.'</p>';
// echo $query.'<br />';

// email app format
if($format=='app' && $err==''){
echo '<div style="text-align:center;">';
while ($row = mysqli_fetch_assoc($rsList)) {	
	$fname= isset($row['first']) ? $row['first'] : '';
	$lname= isset($row['last']) ? $row['last'] : '';
	$email= isset($row['email']) ? $row['email'] : '';

	$str='';
	if($firstfield) $str.=$fname.' ';
	if($lastfield)  $str.=$lname.' ';
	if($emailfield) $str.='&lt;'.$email.'&gt;<br />';
	if($email) echo $str;
} // end of while $row
echo '</div>';
} // end of email format

//table format
if($format=='excel' && $err=='') { 
echo '<table align="center" border="0" cellspacing="0" cellpadding="1">';

while ($row = mysqli_fetch_assoc($rsList)) {	
	$fname= isset($row['first']) ? $row['first'] : '';
	$lname= isset($row['last']) ? $row['last'] : '';
	$both= isset($row['last']) ? $row['first'].' '.$row['last'] : ' ';
	$address= isset($row['address']) ? $row['address'] : '';
	$city= isset($row['city']) ? $row['city'] : '';
	$state= isset($row['state']) ? $row['state'] : '';
	$zip= isset($row['zip']) ? $row['zip'] : '';
	$email= isset($row['email']) ? $row['email'] : '';
	$phone= isset($row['phone']) ? $row['phone'] : '';

	$str='<tr>';
	if($firstfield) $str.='<td>'.$fname.'</td>';
	if($lastfield)  $str.='<td>'.$lname.'</td>';
	if($bothfield)  $str.='<td>'.$both.'</td>';
	if($phonefield) $str.='<td>'.$phone.'</td>';
	if($emailfield) $str.='<td>'.$email.'</td>';
	if($addressfield) $str.='<td>'.$address.'<td>'.$city.'<td>'.$state.'<td>'.$zip.'</td>';
	$str.='</tr>';
	if($str<>'<tr></tr>') echo $str;
} // end of while $row
	echo '</table>';
} // end of table format

echo '<h3>'.$err.'</h3>';

?>
</div><!-- end of output div -->
</div><!-- end #main content -->
	  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
	  <br class="clearfloat" />
</div><!-- end #container -->
</body>
</html>
