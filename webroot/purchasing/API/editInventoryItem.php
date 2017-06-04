<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 7/5/2016
 * Time: 2:45 PM
 */
include '../../config.php';

//check if disabling item
if(isset($_GET['disableItem'])) {
    $itemNum = mysqli_real_escape_string($mysqli, $_GET['disableItem']);
    mysqli_query($mysqli, "UPDATE purchasing_Items SET isDisabled = !isDisabled WHERE Item_ID = '$itemNum'") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));
} else {
    //change inventory amount
    foreach ($_POST as $item_ID => $inventory) {
        if ($inventory != '') {
            $item_ID = mysqli_real_escape_string($mysqli, $item_ID);
            $inventory = mysqli_real_escape_string($mysqli, $inventory);
            mysqli_query($mysqli, "UPDATE `purchasing_Items` SET AmountInStock=" . $inventory . " WHERE `Item_ID` = '" . $item_ID . "'") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));
            //add change to timeseries for estimation
            mysqli_query($mysqli, "INSERT INTO `purchasing_Inventory_TimeSeries` (`ItemID`, Quantity, TimeReceived, Type) VALUES ('$item_ID', '$inventory', NOW(), 'ManualInventoryChange')");
        }
    }
}