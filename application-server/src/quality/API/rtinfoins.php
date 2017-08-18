<?php
include '../../config.php';
packapps_authenticate_user('quality');
//fetch pre-finished info
$RT = mysqli_real_escape_string($mysqli, $_GET['q']);
$query = mysqli_query($mysqli, "
SELECT
  GrowerName,
  commodity_name                          AS CommDesc,
  CASE WHEN date(date) = curdate()
    THEN 1
  ELSE 0 END                              AS Today,
  VarietyName                             AS VarDesc,
  strainName                              AS StrDesc,
  farmName                                AS FarmDesc,
  BlockDesc,
  PK,
  isGoldApple,
  IFNULL(room_name, 'Unassigned')           AS Location,
  COUNT(storage_grower_fruit_bins.bin_id) AS QtyOnHand
FROM storage_grower_receipts
  JOIN storage_grower_fruit_bins ON storage_grower_receipts.id = storage_grower_fruit_bins.grower_receipt_id
  LEFT JOIN storage_rooms ON storage_grower_fruit_bins.curRoom = storage_rooms.room_id
  JOIN `grower_crop-estimates` ON storage_grower_receipts.grower_block = `grower_crop-estimates`.PK
  JOIN grower_strains ON `grower_crop-estimates`.strainID = grower_strains.strain_ID
  JOIN grower_varieties ON grower_strains.variety_ID = grower_varieties.VarietyID
  JOIN grower_commodities ON grower_varieties.commodityID = grower_commodities.commodity_ID
  JOIN grower_farms ON `grower_crop-estimates`.farmID = grower_farms.farmID
  JOIN grower_GrowerLogins ON grower_farms.growerID = grower_GrowerLogins.GrowerID
WHERE id = $RT
");
if (mysqli_num_rows($query) == '0') {
    die(http_response_code(500));
}
$output = mysqli_fetch_assoc($query);

//check if this RT has already been done
$alreadyDoneSamples = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `#Samples` FROM quality_InspectedRTs WHERE `receiptNum` = '$RT'"))['#Samples'];
if ($alreadyDoneSamples == null) {
    //if this sample has been done today, request 5 samples. if not, request 10
    $query2 = mysqli_query($mysqli, "
      SELECT (CASE WHEN (count(`receiptNum`)) > 0
      THEN 5
          ELSE 10 END) AS NumSamplesRequired
      FROM quality_InspectedRTs
      JOIN storage_grower_receipts ON receiptNum
      JOIN `grower_crop-estimates` ON storage_grower_receipts.grower_block = `grower_crop-estimates`.PK
      WHERE PK = '".$output['PK']."' AND DATE(`date`) = curdate()
    ");
    $output2 = mysqli_fetch_assoc($query2);

//perform additional test to see if it is first in a year. If it is, request 20 apples
    if ($output2['NumSamplesRequired'] == 10 && ($output['CommDesc'] != 'Peach' && $output['CommDesc'] != 'Nectarine')) {
        $query2 = mysqli_query($mysqli, "
          SELECT (CASE WHEN (count(`receiptNum`)) > 0
            THEN 10
              ELSE 20 END) AS NumSamplesRequired,
            COUNT(*)
            FROM quality_InspectedRTs
            JOIN storage_grower_receipts ON receiptNum
            JOIN `grower_crop-estimates` ON storage_grower_receipts.grower_block = `grower_crop-estimates`.PK
            WHERE PK = '".$output['PK']."' AND YEAR(`date`) = YEAR(CURDATE());
        ");
        $output2 = mysqli_fetch_assoc($query2);
    }

    $final = array_merge($output, $output2);
} else {
    $final = array_merge($output, array('NumSamplesRequired' => $alreadyDoneSamples, 'isDone' => 1));
}

echo json_encode($final);