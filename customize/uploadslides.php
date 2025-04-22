<?php
if (!isset($_SESSION)) {   session_start(); }
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

$slideerrmsg='';
if(!isset($_POST['jpgs'])) 
	{$slideerrmsg='no slide position selected'; }
	else {$slide = $_POST['jpgs']; // slide slot selected so check upload request
// process  upload request
if(isset($_POST['submitslide'])) { //  file upload request submitted 
// Where the file is going to be placed 
$target_path = "../images/slides/".$slide.".jpg";
if(!move_uploaded_file($_FILES['uploadedslide']['tmp_name'], $target_path)) { $slideerrmsg='There was an error uploading the file'; } 
elseif($_FILES['uploadedslide']['size']>300000) 
	{ $slideerrmsg="Maximum file size exceeded";  unlink($target_path);}
else { $slideerrmsg='Slide uploaded'; 
$filename=basename( $_FILES['uploadedslide']['name']);
$ext=substr(strrchr($filename,'.'),1); 
if($ext<>'jpg' and $ext<>'JPG')  { $slideerrmsg='Illegal file type';  unlink($target_path);}
} // end of file uploaded
} // end of file upload request submitted
} // end of slide slot selected

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>theme update</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="../jscolor/jscolor.js"></script>

<style type="text/css">
<!--
.SH #container #sample {
	float: right;
	width: 745px;
	border: 3px solid #000000;
	margin-top: 70px;
	margin-right:5px;
 	}
.SH #container #mainContent #themediv {
	float: left;
	width: 480px;	}
-->
</style>
<script type="text/javascript">
<!--
function dtheme() { if(!confirm("Are you sure you want to delete?")) return false; }
function ucss() { if(!confirm("Are you sure you want to change the live stylesheet? This will affect all public pages")) return false; }
// -->
</script>
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
 <h2><strong>Slide uploader</strong></h2>
 <p>Home page slide show photos must be .jpg format with dimensions of 500 pixels wide by 200 pixels high (or larger with those same proportions), with a size of less than 300 KB. Click 'Browse...' to find the file on your computer, click the button for the slot that you want to replace the photo of, and then click 'Upload slide.' You will then have to refresh/reload the page to see the slide that has been uploaded.</p>
<form enctype="multipart/form-data" name="slideuploader" method="POST">
<table width="1000px" align="center">
<tr>
<td align="center"><img src="../images/slides/1.jpg" alt="not found" width="300px" height="120px"/></td>
<td align="center"><img src="../images/slides/2.jpg" alt="not found" width="300px" height="120px"/></td>
<td align="center"><img src="../images/slides/3.jpg" alt="not found" width="300px" height="120px"/></td>
</tr>
<tr>
<td align="center"><input type="radio" name="jpgs" value="1" id="jpgs_1" /></td>
<td align="center"><input type="radio" name="jpgs" value="2" id="jpgs_2" /></td>
<td align="center"><input type="radio" name="jpgs" value="3" id="jpgs_3" /></td>
</tr>
<tr>
<td align="center"><img src="../images/slides/4.jpg" alt="not found" width="300px" height="120px"/></td>
<td align="center"><img src="../images/slides/5.jpg" alt="not found" width="300px" height="120px"/></td>
<td align="center"><img src="../images/slides/6.jpg" alt="not found" width="300px" height="120px"/></td>
</tr>
<tr>
<td align="center"><input type="radio" name="jpgs" value="4" id="jpgs_4" /></td>
<td align="center"><input type="radio" name="jpgs" value="5" id="jpgs_5" /></td>
<td align="center"><input type="radio" name="jpgs" value="6" id="jpgs_6" /></td>
</tr>
<tr>
<td align="center"><img src="../images/slides/7.jpg" alt="not found" width="300px" height="120px"/></td>
<td align="center"><img src="../images/slides/8.jpg" alt="not found" width="300px" height="120px"/></td>
<td align="center"><img src="../images/slides/9.jpg" alt="not found" width="300px" height="120px"/></td>
</tr>
<tr>
<td align="center"><input type="radio" name="jpgs" value="7" id="jpgs_7" /></td>
<td align="center"><input type="radio" name="jpgs" value="8" id="jpgs_8" /></td>
<td align="center"><input type="radio" name="jpgs" value="9" id="jpgs_9" /></td>
</tr>
</table>
 <p>
  <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
  <input name="uploadedslide" type="file" /><br />
  <input name="submitslide" type="submit" value="Upload Slide" />
  <?php if($slideerrmsg<>'') echo $slideerrmsg;?>
</form>
 </p>
 <p>&nbsp;</p>

</div><!-- end of mainContent -->
</div><!-- end of container -->
</body>
</html>
