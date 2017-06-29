<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 6/28/2016
 * Time: 2:54 PM
 */
include '../../config.php';
if (isset($_GET['offset'])) {
    $offset = mysqli_real_escape_string($mysqli, $_GET['offset']);
} else {
    $offset = 0;
}
//filter view by supplier
if(isset($_GET['suppID']) && $_GET['suppID'] != -1)
{
    $suppID = mysqli_real_escape_string($mysqli, $_GET['suppID']);
}

//filter view by item
if(isset($_GET['item_ID']))
{
    $itemID = mysqli_real_escape_string($mysqli, $_GET['item_ID']);
}

$orders = mysqli_fetch_all(mysqli_query($mysqli, "SELECT `histTable`.`Purchase_ID`, DATE_FORMAT(`histTable`.DateOrdered, '%b %d %Y %h:%i %p') as DateOrdered, purchasing_Suppliers.Name, DATE_FORMAT(histTable.DateReceived, '%b %d %Y %h:%i %p') as DateReceived, histTable.invoice_attached, histTable.pack_slip_attached, `histTable`.InitiatedBy, `histTable`.isReceived, `histTable`.SupplierID, purchasing_purchases2items.Item_ID, purchasing_purchases2items.QuantityOrdered, purchasing_purchases2items.PricePerUnit, ItemDesc FROM (SELECT * FROM operationsData.purchasing_purchase_history ".(isset($suppID) ? "WHERE SupplierID=".$suppID : '')."  LIMIT 20 OFFSET $offset ) histTable JOIN `operationsData`.purchasing_purchases2items ON histTable.Purchase_ID=`operationsData`.`purchasing_purchases2items`.Purchase_ID LEFT JOIN `operationsData`.purchasing_Items ON purchasing_purchases2items.Item_ID = purchasing_Items.Item_ID LEFT JOIN operationsData.purchasing_Suppliers ON histTable.SupplierID = purchasing_Suppliers.SupplierID"), MYSQLI_ASSOC);
echo mysqli_error($mysqli);
$finalArray = array();

foreach ($orders as $data) {
    if (!array_key_exists($data['Purchase_ID'], $finalArray)) {
        $finalArray[$data['Purchase_ID']] = $data;
        $finalArray[$data['Purchase_ID']]['ItemsOrdered'] = array($data['Item_ID'] => array('QuantityOrdered' => $data['QuantityOrdered'], 'PricePerUnit' => $data['PricePerUnit'], 'ItemDesc' => $data['ItemDesc']));
        unset($finalArray[$data['Purchase_ID']]['Item_ID']);
        unset($finalArray[$data['Purchase_ID']]['QuantityOrdered']);
        unset($finalArray[$data['Purchase_ID']]['PricePerUnit']);
        unset($finalArray[$data['Purchase_ID']]['ItemDesc']);
    } else {
        $finalArray[$data['Purchase_ID']]['ItemsOrdered'][$data['Item_ID']] = array('QuantityOrdered' => $data['QuantityOrdered'], 'PricePerUnit' => $data['PricePerUnit'], 'ItemDesc' => $data['ItemDesc']);
    }
}
header('Content-type: application/json');
//reverse the array because the order by in sql wasn't working :(
echo json_encode(array_reverse($finalArray));
