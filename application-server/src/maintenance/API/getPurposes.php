<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 6/27/17
 * Time: 8:42 AM
 */
include '../../config.php';
$purposes = mysqli_query($mysqli, "SELECT purpose_id, Purpose FROM maintenance_purposes");

$output = array();
while($purpose = mysqli_fetch_assoc($purposes)){
    $output[$purpose['purpose_id']] = $purpose['Purpose'];
}
echo json_encode($output);