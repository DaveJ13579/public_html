<script>
<!--
function wopen(url, name, w, h)
{
w += 32;h += 32;
 var win = window.open(url,
  name,
  'width=' + w + ', height=' + h + ', ' +
  'location=no, menubar=no, ' +
  'status=no, toolbar=no, scrollbars=yes, resizable=yes, titlebar=no');
 win.resizeTo(w, h);
 win.focus();
}
// -->
</script>
<?php $_SESSION['from'] = isset($_SERVER['PHP_SELF'])  ? $_SERVER['PHP_SELF'] : ""; ?>
