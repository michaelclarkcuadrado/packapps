<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 7/7/2016
 * Time: 10:29 AM
 */
include "../../config.php";

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
}
if (isset($_POST['type']) && isset($_POST['newItemDesc']) && isset($_POST['newQuantity']) ){
    mysqli_query($mysqli, "INSERT INTO purchasing_Items (`Item_ID`, `Type_ID`, `ItemDesc`, `QtyPerUnit`) VALUES (DEFAULT, '".mysqli_real_escape_string($mysqli, $_POST['type'])."', '".mysqli_real_escape_string($mysqli, str_replace( array( '"',"'" ),'',$_POST['newItemDesc'] ))."', '".mysqli_real_escape_string($mysqli, $_POST['newQuantity'])."')") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));
    $itemID = mysqli_insert_id($mysqli);
    mkdir('../assets/Item_Docs/'.$itemID, 0700, true);
}