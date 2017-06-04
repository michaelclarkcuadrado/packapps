<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 6/29/2016
 * Time: 9:55 AM
 */
include "../../config.php";

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('../')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('../')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
}
if(isset($_POST['editID'])) {
    mysqli_query($mysqli, "UPDATE purchasing_Suppliers SET `Name`='".mysqli_real_escape_string($mysqli, $_POST['newCompanyName'])."', `ContactName`='".mysqli_real_escape_string($mysqli, $_POST['newContactName'])."', `ContactPhone`='".mysqli_real_escape_string($mysqli, $_POST['newContactPhone'])."', `ContactEmail`='".mysqli_real_escape_string($mysqli, $_POST['newContactEmail'])."', `InternalContact`='".mysqli_real_escape_string($mysqli, $_POST['newInternalContact'])."' WHERE `SupplierID`='".mysqli_real_escape_string($mysqli, $_POST['editID'])."'") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));
} elseif (isset($_POST['newCompanyName']) && isset($_POST['newContactName']) && isset($_POST['newContactPhone']) && isset($_POST['newContactEmail']) && isset($_POST['newInternalContact'])){
    mysqli_query($mysqli, "INSERT INTO purchasing_Suppliers (`Name`, `ContactName`, `ContactPhone`, `ContactEmail`, `InternalContact`) VALUES ('".mysqli_real_escape_string($mysqli, $_POST['newCompanyName'])."', '".mysqli_real_escape_string($mysqli, $_POST['newContactName'])."',  '".mysqli_real_escape_string($mysqli, $_POST['newContactPhone'])."', '".mysqli_real_escape_string($mysqli, $_POST['newContactEmail'])."', '".mysqli_real_escape_string($mysqli, $_POST['newInternalContact'])."' )") or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));
}