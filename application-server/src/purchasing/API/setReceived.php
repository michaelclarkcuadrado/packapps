<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 7/13/2016
 * Time: 1:05 PM
 */
include '../../config.php';
if (isset($_GET['q'])) {
    $id = mysqli_real_escape_string($mysqli, $_GET['q']);
    mysqli_query($mysqli, "UPDATE operationsData.purchasing_purchase_history SET DateReceived=DEFAULT, isReceived=1 WHERE Purchase_ID='$id'") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));

    //update inventories
    $itemsInvolved = mysqli_fetch_all(mysqli_query($mysqli, "SELECT Item_ID, QuantityOrdered FROM purchasing_purchases2items WHERE Purchase_ID=$id"), MYSQLI_ASSOC);
    $finalItemCount = 0;
    foreach($itemsInvolved as $item)
    {
        mysqli_query($mysqli, "UPDATE operationsData.purchasing_Items SET AmountInStock = (AmountInStock + ".$item['QuantityOrdered'].") WHERE Item_ID = ".$item['Item_ID']);
        $newInventoryAmt = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT AmountInStock FROM operationsData.purchasing_Items WHERE Item_ID = ".$item['Item_ID']))['AmountInStock'];
        mysqli_query($mysqli, "INSERT INTO operationsData.purchasing_Inventory_TimeSeries (`TimeReceived`, ItemID, Quantity, Type) VALUES (NOW(), '".$item['Item_ID']."', '".$newInventoryAmt."', 'PackageReceived')");
        $finalItemCount += $item['QuantityOrdered'];
    }
    echo $finalItemCount;
} elseif (isset($_GET['undo'])) {
    $id = mysqli_real_escape_string($mysqli, $_GET['undo']);
    mysqli_query($mysqli, "UPDATE operationsData.purchasing_purchase_history SET DateReceived=DEFAULT, isReceived=0 WHERE Purchase_ID='$id'") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));

    //update inventories
    $itemsInvolved = mysqli_fetch_all(mysqli_query($mysqli, "SELECT Item_ID, QuantityOrdered FROM purchasing_purchases2items WHERE Purchase_ID=$id"), MYSQLI_ASSOC);
    foreach($itemsInvolved as $item)
    {
        mysqli_query($mysqli, "UPDATE operationsData.purchasing_Items SET AmountInStock = (AmountInStock - ".$item['QuantityOrdered'].") WHERE Item_ID = ".$item['Item_ID']);
        mysqli_query($mysqli, "DELETE FROM operationsData.purchasing_Inventory_TimeSeries WHERE ItemID='".$item['Item_ID']."' AND `TimeReceived` >= (NOW() - INTERVAL 8 SECOND)");
    }
}