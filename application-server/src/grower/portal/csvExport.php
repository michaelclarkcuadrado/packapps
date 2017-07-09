<?php
/**
 * Created by PhpStorm.
 * User: MAC
 * Date: 5/11/2015
 * Time: 12:36 PM
 */
include '../../config.php';
$userinfo = packapps_authenticate_grower();
$blockdata = mysqli_query($mysqli, "SELECT `Comm Desc`,VarDesc,FarmDesc,BlockDesc,`Str Desc`,format(".(date('Y')-5)."act,0),format(".(date('Y')-4)."act,0),format(".(date('Y')-3)."act,0),format(".(date('Y')-2)."act,0),format(".(date('Y')-1)."act,0),format(".date('Y')."est,0) FROM `grower_crop-estimates` WHERE Grower='" . $userinfo['GrowerCode'] . "' AND isDeleted = '0' ORDER BY `Comm Desc`, VarDesc, FarmDesc, BlockDesc, `Str Desc` ASC;");
if (!$blockdata) {
    echo "Oops. There seems to be an error. Try again later.";
} else {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Orchard_Report_' . $userinfo['GrowerName'] . '_' . date("m-d-Y") . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, explode('&', "This file is for your records. All estimates should be submitted through the grower portal."));
    fputcsv($output, explode('&', " "));
    fputcsv($output, array('Commodity', 'Variety', 'Farm', 'Block', 'Strain', (date('Y')-5).' Received', (date('Y')-4).' Received', (date('Y')-3).' Received', (date('Y')-2).' Received', (date('Y')-1).' Received', date('Y').' Current Estimate'));
    while ($blockarray = mysqli_fetch_assoc($blockdata)) fputcsv($output, $blockarray);
}