<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "all";


$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
?>
<?php require_once('../Connections/piercecty.php'); 

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Error logs</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
<p><strong>Error Logs - display and truncate</strong></p>
<?php 
if(isset($_POST['fname'])) {$fname=$_POST['fname'];} else {$fname="../error_log"; }; 
// make array of folders to check for error log files
$folders[1]=array("","Utilities");
$folders[2]=array("../Newsletter/maillist/","Maillist");
$folders[3]=array("../MailAPI/","MailAPI");
$folders[4]=array("../Swatches/","Swatches");
$folders[5]=array("../blog/","Blog");
$folders[6]=array("../graphs/","Graphs");
$folders[7]=array("TestingPHP/","TestingPHP");

?>			 
<form action="ErrorLogs.php" method="post" name="select">
<select name="fname">
    <option value="../error_log" <?php if($fname=="../error_log") { ?>selected="selected" <?php } ?>>Main folder </option>
     <?php for($m=1; $m<=7; ++$m) { 
	 	$testfile=$folders[$m][0]."error_log";
		if(file_exists($testfile)) { ?>
     <option value="<?php echo $testfile; ?>" <?php if($fname==$testfile) { ?>selected="selected" <?php } ?>><?php echo $folders[$m][1];?></option>
     <?php } // end if file exists
	 }       // end of $m loop through folder options
	 ?>
</select>
<input name="submit" type="submit" value="Select folder" />
</form>

<?php
// handle selected folder 
if(isset($_POST['fname'])) { $fname=$_POST['fname']; 

// handle delete form post 
if(isset($_POST['submit']) && ($_POST['submit']=="Delete")) { 
	$numlines=$_POST['numlines'];
	$from=$_POST['from']; $to=$_POST['to']; // page range to delete is gotten from POST and range is checked
	if(($from>0) && ($from<=$numlines) && ($to>=$from) && ($to>0) && ($to<=$numlines)) {
		$newfile=""; // set up tempfile to hold saved rows
			if(file_exists($fname)) { echo "Deleting rows ",$from." to ".$to."<br />";
			$fh=fopen($fname, 'r');
				for($k=1; $k<=$numlines; ++$k) { // cycle through rows of the file to save those not being deleted
				$line=fgets($fh);
					if(($k>$to) || ($k<$from)) { $newfile.=$line; }
				} // end of k loop through all lines to find ones to save in $newfile
			fclose($fh);
			$fh=fopen($fname, 'w');
			fwrite($fh, "$newfile");
			fclose($fh);
			} // end of if file exists for delete
	} // end of it from and to are in range
} // end of if submit delete


// display error log
if(file_exists($fname)) { echo $fname." exists<br />";
$fh=fopen($fname, 'r');
// get end of line displacements into array $endlines[1 to last line]
$endlines=array();
$size=filesize($fname);
$j=1;
for ($i=1; $i<=$size; ++$i) { 
$char=fread($fh,1); $ord=ord($char);
if($ord==10) { $endlines[$j]=$i; ++$j;}
} // end of for $i=1 to $size

// show all lines
rewind($fh);
for($ct=1; $ct<$j; ++$ct) {
$line=fgets($fh);
echo "line#".$ct." ".$line."<br />"; }
fclose($fh);
?>
<form action="ErrorLogs.php" method="post" name="deleterows">
Delete rows from <input name="from" type="text" size="4" maxlength="4" /> through <input name="to" type="text" value="<?php echo $ct-1;?>" size="4" maxlength="4" />
<input name="submit" type="submit" value="Delete" />
<input name="numlines" type="hidden" value="<?php echo ($ct); ?>" />
<input name="fname" type="hidden" value="<?php echo $fname; ?>" />
</form>
<?php } // end of if file exists for displayhing file rows
} // end of if isset fname
?>

<p>&nbsp;</p>
</div>
</div>
</body>
</html>
