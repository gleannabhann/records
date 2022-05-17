<?php
//echo json_encode(get_defined_vars());
$msg = '<p class="lead">Sorry!</p>'
  . '<p>'. htmlspecialchars($message).'</p>'
  . '<a href="javascript:history.go(-1);">Back</a>';
echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
if (isset($alert)) {
  bs_alert($msg, $alert);
} else {
  echo $msg;
}
echo "</div></div>";
?>
