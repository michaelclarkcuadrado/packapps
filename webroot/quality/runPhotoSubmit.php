<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 6/13/2016
 * Time: 1:15 PM
 */
include '../config.php';

if(isset($_FILES['photo']))
{
    mysqli_query($mysqli, "UPDATE run_inspections SET isPhotographed=1 WHERE RunID='".mysqli_real_escape_string($mysqli, $_POST['RunID'])."'");
    if (file_exists("assets/uploadedimages/runs/" . escapeshellcmd($_POST['RunID']) . ".jpg"))
    {
        unlink("assets/uploadedimages/run/" . escapeshellcmd($_POST['RunID']) . ".jpg");
    }
    move_uploaded_file($_FILES['photo']['tmp_name'], "assets/uploadedimages/runs/" . escapeshellcmd($_POST['RunID']) . ".jpg");
}

echo "<script>location.replace('runPhoto.php?success=1')</script>";