<?php
if (!permissions("Admin")>=5) {
  // We don't have sufficient permissions for this page.
  
  if (DEBUG) {
    $admin = permissions("Admin");
    echo "Value of Admin perms is $admin";
  }
echo '<p class ="alert alert-warning error"> This page has been accessed in error.</p>';
echo 'Please use your back arrow to return to the previous page.';
exit_with_footer();
}

if (isset($error)) {
echo "<div class='row'>";
echo "<div class='col-md-8 col-md-offset-2 block-center'>";
echo "<div class='alert alert-danger'>$error</div>";
echo "</div></div>";

}
?>
<div class="row">
<div class="col-md-8 col-md-offset-2 text-center">

Type an email in the box to obtain an invitation key:
<form action="key.php" method="post">
                <input type="text" class="form-control" placeholder="Enter Email Address" name="email">
              <button type="submit" class="btn btn-default">Submit</button>
            </form>
          </div> <!-- ./col-md-8 -->
</div> <!-- ./row -->
