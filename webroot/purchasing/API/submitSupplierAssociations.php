<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 7/7/2016
 * Time: 9:52 AM
 */
include '../../config.php';
$item = mysqli_real_escape_string($mysqli, $_POST['item_ID']);
foreach ($_POST as $key => $value) {
    if ($value != '' && $key != 'item_ID') {
        $key = mysqli_real_escape_string($mysqli, $key);
        $value = mysqli_real_escape_string($mysqli, $value);
        mysqli_query($mysqli, "INSERT INTO purchasing_items2suppliers (`ItemID`, `SupplierID`, `quotedPricePerUnit`) VALUES (" . $item . ", " . $key . ", '" . $value . "') ON DUPLICATE KEY UPDATE `quotedPricePerUnit`='" . $value . "'");
    } else if ($value == '') {
        mysqli_query($mysqli, "DELETE FROM purchasing_items2suppliers WHERE `SupplierID`='$key' AND `itemID`='$item'");
    }
}