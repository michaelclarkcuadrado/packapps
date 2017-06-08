<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 7/11/2016
 * Time: 1:38 PM
 */
include '../../config.php';
if (isset($_POST['renameID']) && isset($_POST['newName']) && $_POST['newName'] != '') {
    $newName = mysqli_real_escape_string($mysqli, str_replace( array( '"',"'" ),'',$_POST['newName'] ));
    $renameID = mysqli_real_escape_string($mysqli, $_POST['renameID']);
    mysqli_query($mysqli, "UPDATE `purchasing_Items` SET `ItemDesc`='$newName' WHERE `Item_ID` = ".$renameID) or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));
}