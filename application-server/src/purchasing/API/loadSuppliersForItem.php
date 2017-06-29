<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 7/6/2016
 * Time: 12:26 PM
 */
include '../../config.php';
if (isset($_GET['Item_ID'])) {
    $item = mysqli_real_escape_string($mysqli, $_GET['Item_ID']);
    header('Content-type: application/json');
    echo json_encode(mysqli_fetch_all(mysqli_query($mysqli, "SELECT `purchasing_Suppliers`.`SupplierID`, `Name`, ifnull(quotedPricePerUnit, '') as quotedPricePerUnit FROM purchasing_Suppliers LEFT JOIN purchasing_items2suppliers ON purchasing_Suppliers.SupplierID=purchasing_items2suppliers.SupplierID AND ItemID = " . $item . "  ORDER BY Name ASC"), MYSQLI_ASSOC));
}