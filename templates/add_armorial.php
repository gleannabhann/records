<?php


if (permissions("Ruby")<3){
    // We don't have permission to add images so let's just exit now.
    echo '<p class="error"> This page has been accessed in error; it is available only to Ruby Heralds</p>';
    exit_with_footer();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET["id"])) {
        $id_person = $_GET["id"];
    } else {
        $id_person = -1;
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST["id"])) {
        $id_person = $_POST["id"];
    } else {
        $id_person = -1;
    }
}


if (($_SERVER['REQUEST_METHOD'] == 'POST')  && (permissions("Ruby")>=3)){ //portion commented out for testing

  /* note: db connection created by header.php or header_main.php */  

    // We have a form submission.
    // Note: we allow for addition of multiple devices which is why the blank
    //        form will reappear at the bottom of the page

$uploadOk = 1; // set the "OK" flag to 1. Future IF tests may change it to 0

// declare some variables
$blazon = sanitize_mysql($_POST["blazon"]);
$fname = $_FILES["imagefile"]["name"];
$fsize = $_FILES["imagefile"]["size"];
$ftype = $_FILES["imagefile"]["type"];
$imgheight = NULL;
$imgwidth = NULL;
$image = NULL;

// Check if image file is an actual image or fake image

    $check = getimagesize($_FILES["imagefile"]["tmp_name"]);


    if($check !== false) {
      $imgwidth = $check[0];
      $imgheight = $check[1];

        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["imagefile"]["size"] > 500000) {
       echo "<p class='error'>Sorry, your file is too large. Max file size is 500kb</p>";
       $uploadOk = 0;
    }
    if ($imgheight > 300 || $imgwidth > 300){
      echo "<p class='error'>Sorry, your file exceeds the maximum dimensions of 300x300 pixels.</p>";
      $uploadOk = 0;
    }
    // Allow only certain file formats
    if($ftype != "image/jpg" 
            && $ftype != "image/png" 
            && $ftype != "image/jpeg" 
            && $ftype != "image/gif" ) {
        echo "<p class='error'>Sorry, only JPG, JPEG, PNG & GIF files are allowed. The file you attempted to upload is " . var_dump($ftype) . "</p>";
        $uploadOk = 0;
    }
// read the image file into a string
    $image = file_get_contents($_FILES["imagefile"]["tmp_name"]);
    $image = base64_encode($image);

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        $query = "INSERT INTO Armorials"
            . "(id_armorial,blazon_armorial,image_armorial,"
                . "fname_armorial,fsize_armorial,ftype_armorial, timestamp_armorial) "
            . "VALUES (NULL, '$blazon', '$image', '$fname', $fsize, '$ftype', NOW() )";

        $result = update_query($cxn, $query);
        if ($result !== 1) {
            echo "Error updating record: " . mysqli_error($cxn);
        } else {
            echo "Successfully added ". $fname ." to the database<p>";
            echo button_link("awards.php", "Return to awards page"); // TODO Identify where we should send users with this button
            echo "or continue adding new devices below<p>";
        }
      }

  /* footer.php will close the database connection for us */
}
?>

<div class='row'><div class='col-md-8 col-md-offset-2'>
<?php if ($id_person > 0) {
    echo button_link("edit_person.php?id=$id_person", "Return to Edit Person Page");
}   ?>     
<form action="add_armorial.php" method="post" enctype="multipart/form-data">
  <?php echo form_title("Adding a New Device or Badge"); ?>
  <?php echo  '<input type="hidden" name="id" value="'.$id_person.'">'; ?>
  <table class='table table-condensed table-bordered'>
      <tr>
          <td class="text-right">Blazon<br>(Required)</td>
          <td><input type="text" name="blazon" id="blazon" size="50" maxlength="128" required>
          </td>
      </tr>
      <tr>
        <td>Select image file<br>
        (Max 300x300 pixels, 500 kb)</td>
        <td><input type="file" name="imagefile" id="imagefile">
      </tr>
  </table>
  <input type="submit" value="Upload Image">
</form>
    </div></div>
