<?php require_once('Connections/piercecty.php'); 
require_once('includes/sqlcleaner.php'); 
require_once('includes/cron.inc.php'); 

// *** Validate request to login to this site.
if (!isset($_SESSION)) {   session_start(); }

$IP=$_SERVER['REMOTE_ADDR'];

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['user_name'])) {
  $loginUsername= ctype_alnum($_POST['user_name']) ? $_POST['user_name'] : 'not legal';
  $password= ctype_alnum($_POST['user_password']) ? $_POST['user_password'] : 'not legal';
  $MM_fldUserAuthorization = "level";
  $MM_redirectLoginSuccess = "Utilities/PagesIndex.php";
  $MM_redirectLoginFailed = "login.php";
  $MM_redirecttoReferrer = true;
  	
  $LoginRS__query=sprintf("SELECT user_name, user_password, level FROM users WHERE user_name=%s AND user_password=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysqli_query($piercecty, $LoginRS__query) or die(mysqli_error($piercecty));
  $loginFoundUser = mysqli_num_rows($LoginRS);
  if ($loginFoundUser) {
  $row=mysqli_fetch_assoc($LoginRS);
    $loginStrGroup  = $row['level'];
	
    // insert "okay" into loginlog table unless is username 'admin'
	$status = "Okay";
	if($loginUsername<>'admin') { // skip admin logins
	$insertlogin = sprintf("INSERT INTO loginlog (username, password, datein, timein, status, IPaddress) VALUES (%s,%s, curdate(), curtime(), %s, '$IP')",
			GetSQLValueString($loginUsername, "text"),
			GetSQLValueString($password, "text"),
			GetSQLValueString($status, "text"));  
	$result = mysqli_query($piercecty, $insertlogin) or die (mysqli_error($piercecty));
	} // end of skip admin logins
	$lastloginq="update users set lastlogin=now() where user_name='$loginUsername'"; 
	$result2=mysqli_query($piercecty,$lastloginq) or die(mysqli_error($piercecty));
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && true) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
if($loginStrGroup=='branch')  $MM_redirectLoginSuccess = "Branch/branch-home.php"; // branch leaders go only to the branch-home.php page
header("Location: " . $MM_redirectLoginSuccess );
} // end of found user
  else {
    // insert into loginlog table "Failed"
	$status = "Failed";	
	$insertlogin = sprintf("INSERT INTO loginlog (username, password, datein, timein, status, IPaddress) VALUES (%s,%s, curdate(), curtime(), %s, '$IP')",
			GetSQLValueString($loginUsername, "text"),
			GetSQLValueString($password, "text"),
			GetSQLValueString($status, "text"));  
	$result = mysqli_query($piercecty, $insertlogin) or die (mysqli_error($piercecty));
	  
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH">

<div id="container">
<div id="mainContent">
    <h3><strong>Please Log In</strong></h3>
    <p>This section of the website is only for authorized users who have been assigned passwords, and not for the general public.</p>
    <form id="login" name="login" method="POST" action="<?php echo $loginFormAction; ?>">
      <table border="0" align="center" cellpadding="2" cellspacing="1" id="logintable">
        <tr>
          <td>User Name:</td>
          <td><input name="user_name" type="text" id="user_name" size="30" /></td>
        </tr>
        <tr>
          <td>Password:</td>
          <td><input name="user_password" type="password" id="user_password" size="30" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" name="Submit" id="Submit" value="Log In" /></td>
        </tr>
      </table>
    </form>
    <p>&nbsp;</p>
<p>&nbsp;</p>
  <!-- end #mainContent --></div>
	<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
<!-- end #container --></div>
</body>
</html>
