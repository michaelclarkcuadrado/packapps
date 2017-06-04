<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 6/9/2016
 * Time: 12:04 PM
 */
if (isset($_FILES['0'])) {
    include '../config.php';
    $result = exec("zbarimg -q --raw " . $_FILES['0']['tmp_name']);
    if(strlen($result) != 12)
    {
        die("Barcode did not scan.");
    }
    $result = substr($result, 0, 6);
    $info = mysqli_query($mysqli, "SELECT * FROM PSOHCSV WHERE `Ticket#` = " . $result) or die(mysqli_error($mysqli));
    if (mysqli_num_rows($info) == 0) {
        echo "Try scanning again.";
    } else {
        $info = mysqli_fetch_array($info);
        echo $info['Grower Desc'] . ", " . $info['Var Desc'];
    }
}