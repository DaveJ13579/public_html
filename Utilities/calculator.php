<?php
// algebraic calculator that uses javascript
// for mouseover and clickon events
// and innerHTML to change text on the page

$result=''; $mem=0;
if(isset($_POST['kent'])) { 
	$wind=$_POST['wind'];
	if(preg_match('/^[. powE,()+*\-\/\d]+$/', $wind)) { 	
		$result=@eval("return ($wind);"); }
		else { $result="Illegal character"; }
	if($result=='') $result=$wind." ?";
	}
if(isset($_POST['mem'])) $mem=$_POST['mem']; 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>calculator</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<style type="text/css">
.key {
	cursor:pointer;
	font-size:24px;
	font-weight: bold;
	text-align: center;
	background-color: pink;
	width:50px;
	height:30px;
}
.wind2 {
font-size:20px;
font-weight: normal;
text-align: center;
}
.mem {
cursor:pointer;
font-size:20px;
font-weight: normal;
text-align: center;
background-color: #ccc;
width:50px;
height:30px;
}
.op2 {
cursor:pointer;
font-size:24px;
font-weight: bold;
text-align: center;
background-color: #ccc;
width:50px;
height:30px;
}
.op {
cursor:pointer;
font-size:24px;
font-weight: bold;
text-align: center;
background-color: lightgreen;
width:50px;
height:30px;
}
.kent {
	cursor:pointer;
	font-size:30px;
	font-weight: bold;
	text-align: center;
	background-color: lightblue;
	width:50px;
	height:70px;
}
#narrower2 {
	width: 370px;
	padding: 10px;
	background-color: #FFF;
	margin-left: auto;
	margin-right: auto;
	border: 4px solid #666666;
	border-radius: 2em;
}
.extras {
cursor:pointer;
text-align: center;
font-weight: bold;
background-color: lightblue;
}
.point { cursor:pointer; }
</style>
<script type="text/javascript">
function setFocus() { document.getElementById('nametemp').focus();}
mem=<?php echo is_numeric($mem) ? $mem : 0; ?>;
function rolloverBg(keyID,Bg) { keyID.style.backgroundColor = Bg; } 
function keyclick(Val) {
	cur=document.getElementById('wind').value;
	document.getElementById('wind').value=cur+Val;}
function clearit() { document.getElementById('wind').value='';}
function bspace() { 
	cur=document.getElementById('wind').value;
	len=cur.length-1; 
	bspaced=cur.substr(0,len);
	document.getElementById('wind').value=bspaced;}
function clearm() { 
	mem=0;
	document.getElementById('mem').innerHTML=mem;
	document.getElementById('memo').value=mem;}
function recallm() { 
	cur=document.getElementById('wind').value;
	document.getElementById('wind').value=cur+mem; }
function switchm() { 
	cur=document.getElementById('wind').value;
	document.getElementById('wind').value=mem;
	mem=cur;
	document.getElementById('mem').innerHTML=mem;
	document.getElementById('memo').value=mem;}
function addm() { 	
	cur=document.getElementById('wind').value;
	mem=parseFloat(cur)+parseFloat(mem);
	document.getElementById('mem').innerHTML=mem;
	document.getElementById('memo').value=mem;}
function subm() { 	
	cur=document.getElementById('wind').value;
	mem=parseFloat(mem)-parseFloat(cur); 
	document.getElementById('mem').innerHTML=mem;
	document.getElementById('memo').value=mem;}
function acres() {
	cur=document.getElementById('wind').value;
	document.getElementById('wind').value=cur*.000023;}
function parallel() {
	cur=document.getElementById('wind').value;
	document.getElementById('wind').value=Math.round(cur/22);}
function headin() {
	cur=document.getElementById('wind').value;
	document.getElementById('wind').value=Math.round(cur/9);}
function angled() {
	cur=document.getElementById('wind').value;
	document.getElementById('wind').value=Math.round(cur/14);}
function pickers() {
	cur=document.getElementById('wind').value;
	document.getElementById('wind').value=Math.round(cur*1.3);}

function wopen(url, name, w, h) {
w += 32;h += 32;
 var win = window.open(url,
  name,
  'width=' + w + ', height=' + h + ', ' +
  'location=no, menubar=no, ' +
  'status=no, toolbar=no, scrollbars=yes, resizable=yes, titlebar=no');
 win.resizeTo(w, h);
 win.focus();
}

var tempvar = 'less';
function moreOrLess(addon){ 
if(tempvar=='less') { 
	addon.style.display="block"
	document.getElementById('click').innerHTML='Less'; 
	tempvar='more'}
	else { 
	addon.style.display="none"
	document.getElementById('click').innerHTML='More';
	tempvar='less'}
}
function startup() {
	addon=document.getElementById('addon')
	addon.style.display="none"
	document.getElementById('wind').focus();
}

</script>
</head>
<body class="SH" onload="startup()"> 
<div id="help-container">
    <div id="narrower2">
    <form action="" method="post" name="calc">
    <table 	width="350" border="3" cellspacing:="10" cellpadding="0" align="center";>
  <tr>
    <td colspan="3"><strong>Calculator</strong></td>
    <td align="center" class="point" id="click" onclick=moreOrLess(addon)>More</td>
    <td align="center" class="point" onClick="wopen('../help/calculator-help.php', 'popup', 640, 780); return false;">Help</a></td>
    </tr>
  <tr>
    <td colspan="5" align="center" ><input class="wind2" id="wind" name="wind" type="text"  size="32" maxlength="40" value="<?php echo $result;?>"/></td>
    </tr>
  <tr>
    <td class="mem"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, '#ccc');" onclick="clearm()">MC</td>
    <td class="mem"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, '#ccc');" onclick="recallm()">MR</td>
    <td class="mem"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, '#ccc');" onclick="switchm()">MS</td>
    <td class="mem"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, '#ccc');" onclick="addm()">M+</td>
    <td class="mem"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, '#ccc');" onclick="subm()">M-</td>
  </tr>
  <tr>
    <td colspan="3" class="mem" id="mem"><?php echo $mem; ?></td>
    <td class="op"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'lightgreen');" onclick="clearit()">C</td>
    <td class="op"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'lightgreen');" onclick="bspace()" style="font-size:2.3em">&larr;</td>
  </tr>
  <tr>
    <td class="key"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'pink');"	onclick="keyclick('7')">7</td>
    <td class="key"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'pink');" onclick="keyclick('8')">8</td>
    <td class="key"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'pink');" onclick="keyclick('9')">9</td>
    <td class="op"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'lightgreen');" onclick="keyclick('/')">/</td>
    <td class="op"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'lightgreen');" onclick="keyclick('(')">(</td>
  </tr>
  <tr>
    <td class="key"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'pink');" onclick="keyclick('4')">4</td>
    <td class="key"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'pink');" onclick="keyclick('5')">5</td>
    <td class="key"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'pink');" onclick="keyclick('6')">6</td>
    <td class="op"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'lightgreen');" onclick="keyclick('*')">*</td>
    <td class="op"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'lightgreen');" onclick="keyclick(')')">)</td>
  </tr>
  <tr>
    <td class="key"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'pink');" onclick="keyclick('1')">1</td>
    <td class="key"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'pink');" onclick="keyclick('2')">2</td>
    <td class="key"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'pink');" onclick="keyclick('3')">3</td>
    <td class="op"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'lightgreen');" onclick="keyclick('-')">-</td>
    <td  rowspan="2" class="kent"   ><input onmouseover="rolloverBg(this, '#88f');" onmouseout="rolloverBg(this, 'lightblue');" name="kent" type="submit" class="kent" value="=" />
    <input id="memo" name="mem" type="hidden" value="" /></td>
  </tr>
  <tr>
    <td colspan="2" class="key"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'pink');" onclick="keyclick('0')">0</td>
    <td class="key"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'pink');" onclick="keyclick('.')">.</td>
    <td class="op"  onmouseover="rolloverBg(this, 'lightblue');" onmouseout="rolloverBg(this, 'lightgreen');" onclick="keyclick('+')">+</td>
    </tr>
 </table>
<table 	id="addon" width="350" border="3" cellspacing:="10" cellpadding="4" align="center";>
<tr><td class="extras" onclick="acres()">Convert square feet to acres</td>
<tr><td class="extras" onclick="parallel()">Convert feet to number of parked cars (parallel)</td></tr>
<tr><td class="extras" onclick="headin()">Convert feet to number of parked cars (head-in)</td></tr>
<tr><td class="extras" onclick="angled()">Convert feet to number of parked cars (angled)</td></tr>
<tr><td class="extras" onclick="pickers()">Convert parked cars to number of pickers</td></tr>
</table>
</form>
 </div> 
</div><!-- end #container -->
</body>
</html>
