<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 6/29/2016
 * Time: 10:59 AM
 */
include '../../config.php';
if(!isset($_GET['supplier'])) {
    echo json_encode(mysqli_fetch_all(mysqli_query($mysqli, "SELECT * FROM purchasing_Suppliers ORDER BY Name ASC"), MYSQLI_ASSOC));
} else {
    $supplier = mysqli_real_escape_string($mysqli, $_GET['supplier']);
    echo json_encode(mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM purchasing_Suppliers WHERE `SupplierID` = $supplier")));
}