<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 6/13/2016
 * Time: 1:15 PM
 */
require '../config.php';
packapps_authenticate_user('quality');

if(isset($_FILES['photo']))
{
    mysqli_query($mysqli, "UPDATE quality_run_inspections SET isPhotographed=1 WHERE RunID='".mysqli_real_escape_string($mysqli, $_POST['RunID'])."'");
    packapps_uploadToS3($availableBuckets['quality'], $_FILES['photo']['tmp_name'], 'runPhoto-runid-'.$_POST['RunID'].'.jpg');
    echo "<script>location.replace('runPhoto.php?success=1')</script>";
} else {
    echo 'Photo did not upload!';
}

