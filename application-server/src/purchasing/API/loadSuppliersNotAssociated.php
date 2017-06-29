<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 7/6/2016
 * Time: 12:26 PM
 */
include '../../config.php';
if (isset($_GET['exclude_Item_ID'])) {
    $exclude = mysqli_real_escape_string($mysqli, $_GET['exclude_Item_ID']);
    header('Content-type: application/json');
    echo json_encode(mysqli_fetch_all(mysqli_query($mysqli, "SELECT `purchasing_Suppliers`.`SupplierID`, `Name` FROM purchasing_Suppliers LEFT JOIN purchasing_items2suppliers ON purchasing_Suppliers.SupplierID=purchasing_items2suppliers.SupplierID WHERE ItemID <> " . $exclude . " OR ItemID IS NULL ORDER BY Name ASC"), MYSQLI_ASSOC));
}