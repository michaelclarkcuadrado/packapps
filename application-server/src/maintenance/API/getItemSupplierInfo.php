<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/21/17
 * Time: 11:58 AM
 */
require '../../config.php';
$userinfo = packapps_authenticate_user('maintenance');
if(isset($_GET['itemID'])){
        $objToProcess = json_decode($_GET['itemID']);
        $returnArray = array();
        foreach($objToProcess as $item){
            //need to get itemName, supplierID, SupplierName
            //get item name
            $itemname = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT ItemDesc FROM purchasing_Items WHERE Item_ID = '$item'"))['ItemDesc'];
            //TODO
            echo json_encode($itemname);
        }
        echo json_encode($returnArray);
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 INTERNAL SERVER ERROR', true, 500);
    die();
}