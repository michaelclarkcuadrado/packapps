<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 8/25/2016
 * Time: 8:13 AM
 */

function subtract($bomnumber, $quantity){
    include '../../config.php';

    $items = mysqli_fetch_all(mysqli_query($mysqli, "SELECT ItemID, numItemAtomsInAsset FROM purchasing_EnvioAddon_EnvioAssets2purchasingItems WHERE AssetID='$bomnumber'"), MYSQLI_ASSOC);
    //get info

    //add to atoms counted against

    //subtract from inventory

    //invrement last made date
}