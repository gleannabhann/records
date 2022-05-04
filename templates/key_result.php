

<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">

<?php
if (permissions("Admin")<5) {
 // We don't have sufficient permissions for this page.
 echo '<p class="alert alert-warning error"> This page has been accessed in error.</p>';
 echo 'Please use your back arrow to return to the previous page.';
 exit_with_footer();
} else {
echo "<div class='alert alert-info'><p>The key for $email is <br/><b>$key</b>.<p><p>This key expires on <b>$expires</b>.</div>";
}
  ?>
  </div>
  </div>
