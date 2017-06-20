<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 6/20/17
 * Time: 2:27 PM
 */
require_once '../../config.php';
packapps_authenticate_user('maintenance');

$pendingIssues = mysqli_query($mysqli, "SELECT item_id, ItemDesc 
      FROM purchasing_Items 
      JOIN maintenance_part_info 
        ON purchasing_Items.Item_ID = maintenance_part_info.item_id 
      JOIN maintenance_issues2purchasing_items ");
