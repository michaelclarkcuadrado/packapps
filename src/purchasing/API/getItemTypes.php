<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 6/29/2016
 * Time: 3:40 PM
 */
include '../../config.php';
echo json_encode(mysqli_fetch_all(mysqli_query($mysqli, "SELECT purchasing_ItemTypes.Type_ID,Type_Description,count(Item_ID) as ItemCount FROM purchasing_ItemTypes LEFT JOIN operationsData.purchasing_Items ON purchasing_ItemTypes.Type_ID=purchasing_Items.Type_ID GROUP BY Type_ID"), MYSQLI_ASSOC));
