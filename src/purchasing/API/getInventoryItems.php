<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 6/30/2016
 * Time: 2:57 PM
 */
include '../../config.php';
//all edits should be reflected in getItemsBySupplier, inventorySearch, and getInventoryItems
if (isset($_GET['type'])) {
    $items = mysqli_query($mysqli, "SELECT Item_ID, ItemDesc, AmountInStock, format(QtyPerUnit, 0) as QtyPerUnit, UnitOfMeasure, format(quotedPricePerUnit,2) as quotedPricePerUnit, `purchasing_items2suppliers`.SupplierID, `purchasing_Suppliers`.Name FROM purchasing_Items LEFT JOIN purchasing_items2suppliers ON `purchasing_Items`.Item_ID=`purchasing_items2suppliers`.ItemID LEFT JOIN purchasing_Suppliers ON `purchasing_items2suppliers`.SupplierID=`purchasing_Suppliers`.SupplierID LEFT JOIN purchasing_ItemTypes ON `purchasing_Items`.`Type_ID`=`purchasing_ItemTypes`.`Type_ID` WHERE `isDisabled` = 0 AND `purchasing_Items`.Type_ID='" . mysqli_real_escape_string($mysqli, $_GET['type']) . "'");
    $array = mysqli_fetch_all($items, MYSQLI_ASSOC);

    $finishedArray = array();
    foreach ($array as $key) {
        if (!array_key_exists($key['Item_ID'], $finishedArray)) {
            $finishedArray[$key['Item_ID']] = $key;
            if (isset($key['SupplierID'])) {
                $finishedArray[$key['Item_ID']]['Suppliers'] = array($key['SupplierID'] => array("SupplierID"=> $key['SupplierID'], "Name" => $key['Name'], "quotedPricePerUnit" => $key['quotedPricePerUnit']));
            }
            unset($finishedArray[$key['Item_ID']]['Name']);
            unset($finishedArray[$key['Item_ID']]['quotedPricePerUnit']);
            unset($finishedArray[$key['Item_ID']]['SupplierID']);
        } else {
            $finishedArray[$key['Item_ID']]['Suppliers'][$key['SupplierID']] = array("SupplierID"=> $key['SupplierID'], "Name" => $key['Name'], "quotedPricePerUnit" => $key['quotedPricePerUnit']);
        }
    }
    echo json_encode($finishedArray);
}