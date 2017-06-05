<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 6/30/2016
 * Time: 2:57 PM
 */
include '../../config.php';

echo json_encode(mysqli_fetch_all(mysqli_query($mysqli, "SELECT Item_ID, ItemDesc FROM purchasing_Items WHERE `isDisabled` = 1"), MYSQLI_ASSOC));