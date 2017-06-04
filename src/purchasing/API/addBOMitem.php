<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 8/22/2016
 * Time: 12:23 PM
 */
include '../../config.php';
if(isset($_GET['bomnum']) && isset($_GET['itemid'])){
    $bomnum = mysqli_real_escape_string($mysqli, $_GET['bomnum']);
    $itemID = mysqli_real_escape_string($mysqli, $_GET['itemid']);
    mysqli_query($mysqli, "INSERT INTO purchasing_EnvioAddon_EnvioAssets2purchasingItems (`AssetID`, `ItemID`) VALUES ('$bomnum', '$itemID')") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));
    echo mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT ItemDesc FROM purchasing_Items WHERE Item_ID=''"))['ItemDesc'];
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}
