<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mini php tester</title>
</head>
<body>
<?php

require_once('../Connections/piercecty.php');

$x='';
$y=NULL;
if($x==NULL) echo 'empty string is the same as NULL<br />';
if($y=='') echo 'NULL is the same as empty string<br />';
exit;

$q="select ";
for($x=1;$x<10;++$x){ $q.="crop$x,wgt$x,";}
$q=substr($q,0,-1).' from harvests where ID_harvest=352';
echo $q;
exit;

	$row['address1']='123 Grand';
	$row['address2']='456 Main';
	$x=1;
	echo $row["address$x"].'<br />';  // echoes '123 Grand'
	$x=2;
	echo $row["address$x"]; // echoes '456 Main'
exit;


$str=ucfirst(''); echo'---'; exit;

$key='variable';
${$key}=47;
echo $variable;
exit;


 require_once('../Connections/piercecty.php'); 

echo '<pre>';
$desc="show columns from harvests";
$rs=mysqli_query($piercecty,$desc);

exit;





$ts=1414998000; 
 echo date('M j',$ts).'<br />'.$ts;
exit;




/*
$one="1";
$two="2";
$test['test']=array($one, $two);
echo $test['test'][1].'<br />';
*/

$server=$_SERVER['SERVER_NAME'];
$host=$_SERVER['HTTP_HOST'];
$addr=$_SERVER['SERVER_ADDR'];
$root=$_SERVER['DOCUMENT_ROOT'];
echo "<br />SERVER['SERVER_NAME'] = ".$server;
echo "<br />SERVER['HTTP_HOST'] = ".$host;
echo "<br />SERVER['SERVER_ADDR'] = ".$addr;
echo "<br />SERVER['DOCUMENT_ROOT'] = ".$root;

//$harvday=date('F j, Y g:i A',mktime(13, 23, 0, 10, 6, 2013));
//echo '<br />'.$harvday.'<br />';

/*
$harvunix=strtotime('2013-12-21'.' '.'14:32');
$harvday=date('F j, Y g:i A',$harvunix);
echo '    '.$harvday."<br />";
*/
?>
</body>
</html>