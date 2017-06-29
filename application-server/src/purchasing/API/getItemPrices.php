<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 7/20/2016
 * Time: 10:41 AM
 */
include '../../config.php';
if(isset($_POST['suppName']) && isset($_POST['items']) && isset($_POST['suppID']))
{
    $finalArray = array();
    foreach($_POST['items'] as $key => $value)
    {
        $key = mysqli_real_escape_string($mysqli, $key);
        $supplierID = mysqli_real_escape_string($mysqli, $_POST['suppID']);
        $supplier = mysqli_real_escape_string($mysqli, $_POST['suppName']);
        $array = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT quotedPricePerUnit FROM purchasing_items2suppliers WHERE ItemID=$key AND SupplierID = $supplierID"));
        //$inventory = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT AmountInStock FROM purchasing_Items WHERE Item_ID=$key"))['AmountInStock'];
        $array['suppName'] = $supplier;
        $array['ID'] = $key;
        $array['Name'] = $value;
        $finalArray[$key] = $array;
    }
    header('Content-type: application/json');
    echo json_encode($finalArray);
}