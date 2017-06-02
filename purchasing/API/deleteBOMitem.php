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
    mysqli_query($mysqli, "DELETE FROM purchasing_EnvioAddon_EnvioAssets2purchasingItems WHERE AssetID = '$bomnum' AND ItemID = '$itemID'") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}
