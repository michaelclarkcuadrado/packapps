<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 8/24/2016
 * Time: 12:54 PM
 */
include '../../config.php';

if(isset($_GET['itemID']) && isset($_GET['bomSerial']) && isset($_GET['newAtomsVal'])) {
    $itemID = mysqli_real_escape_string($mysqli, $_GET['itemID']);
    $bomSerial = mysqli_real_escape_string($mysqli, $_GET['bomSerial']);
    $newVal = mysqli_real_escape_string($mysqli, $_GET['newAtomsVal']);
    mysqli_query($mysqli, "UPDATE purchasing_EnvioAddon_EnvioAssets2purchasingItems SET numItemAtomsInAsset = '$newVal' WHERE AssetID = '$bomSerial' AND ItemID = '$itemID'") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}
