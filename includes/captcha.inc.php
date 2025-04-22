<?php if(!isset($_SESSION)) session_start();
$code1=rand(10,99);
$code2=rand(1,10);
$codestr=$code1.'+'.$code2.'=';
$_SESSION["code"]=$code1+$code2;
$im = imagecreatetruecolor(50, 24);
$bg = imagecolorallocate($im, 22, 165, 86); //background color green
$fg = imagecolorallocate($im, 255, 255, 255);//text color white
imagefill($im, 0, 0, $bg);
imagestring($im, 5, 5, 5,  $codestr, $fg);
//header("Cache-Control: no-cache, must-revalidate");
header('Content-type: image/png');
imagepng($im);
imagedestroy($im);
?>