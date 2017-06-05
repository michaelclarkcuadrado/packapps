<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 8/12/2016
 * Time: 3:26 PM
 */
include '../../config.php';

if(isset($_GET['itemID'])) {
    $item = mysqli_real_escape_string($mysqli, $_GET['itemID']);
    $history = mysqli_query($mysqli, "SELECT TimeReceived, Quantity FROM purchasing_Inventory_TimeSeries WHERE ItemID='$item' AND TimeReceived >= (NOW() - INTERVAL 6 MONTH) ORDER BY TimeReceived ASC");
    echo json_encode(mysqli_fetch_all($history, MYSQLI_ASSOC));
}