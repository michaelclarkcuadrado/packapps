<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 7/22/2016
 * Time: 2:53 PM
 */
include '../../config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
}
$RealName = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Real Name`, isAuthorizedForPurchases FROM purchasing_UserData JOIN master_users ON master_users.username=purchasing_UserData.UserName WHERE purchasing_UserData.UserName='" . $SecuredUserName . "'"));
if($RealName['isAuthorizedForPurchases'] == 0) {
    die();
}
$RealName = $RealName['Real Name'];

//get posted objects
$itemDataObj = $_POST;
$supplierID = mysqli_real_escape_string($mysqli, $itemDataObj['SuppID']);
unset($itemDataObj['suppID']);

//update supplier activity
mysqli_query($mysqli, "UPDATE purchasing_Suppliers SET lastInteracted=NOW() where SupplierID = '$supplierID'");
//insert main history
mysqli_query($mysqli, "INSERT INTO purchasing_purchase_history (`InitiatedBy`, `SupplierID`) VALUES ('$RealName', $supplierID)") or die(mysqli_error($mysqli));
$purchaseID = mysqli_insert_id($mysqli);
//loop into each item, inserting it
foreach($itemDataObj as $key => $value) {
    $itemID = mysqli_real_escape_string($mysqli, $key);
    $quantityOrdered = mysqli_real_escape_string($mysqli, $value['quantityWanted']);
    $pricePerUnit = mysqli_real_escape_string($mysqli, $value['pricePerUnit']);
    //check if price is the new quote
    if($value['changeQuote'] == 'true'){
        mysqli_query($mysqli, "UPDATE purchasing_items2suppliers SET `quotedPricePerUnit` = '$pricePerUnit' WHERE `ItemID` = $itemID AND `SupplierID` = $supplierID");
    }
    mysqli_query($mysqli, "INSERT INTO purchasing_purchases2items (`Purchase_ID`, `Item_ID`, `QuantityOrdered`, `PricePerUnit`) VALUES ($purchaseID, $itemID, $quantityOrdered, $pricePerUnit)");
}

