<?php
include '../../config.php';
packapps_authenticate_user('quality');
//fetch pre-finished info
$ReceiptNum = mysqli_real_escape_string($mysqli, $_GET['q']);
$query = mysqli_query($mysqli, "
    SELECT
  GrowerName,
  commodity_name                                         AS CommDesc,
  VarietyName                                            AS VarDesc,
  strainName                                             AS StrDesc,
  farmName                                               AS FarmDesc,
  BlockDesc,
  DATE(Date)                                             AS Date,
  COUNT(bin_id)                                          AS Qty,
  COUNT(bin_id) * storage_grower_fruit_bins.bushelsInBin AS Bu,
  'Bulk Fruit'                                           AS ReceiptType,
  `Color Quality`                                        AS `ColorQuality`,
  Blush,
  `#Samples`                                             AS NumSamples,
  `Bruise`,
  `BitterPit`,
  `Russet`,
  `SanJoseScale`,
  `SunBurn`,
  `Scab`,
  `StinkBug`,
  `Note`,
  date(`DateInspected`)                                  AS DateInspected,
  `Real Name`                                            AS `InspectedBy`,
  `FTAup`,
  `DAFinished`,
  `StarchFinished`
FROM quality_InspectedRTs
  JOIN storage_grower_receipts ON quality_InspectedRTs.receiptNum = storage_grower_receipts.id
  JOIN storage_grower_fruit_bins ON storage_grower_receipts.id = storage_grower_fruit_bins.grower_receipt_id
  JOIN `grower_gfbvs-listing` ON `grower_gfbvs-listing`.PK = `storage_grower_receipts`.grower_block
  JOIN packapps_master_users ON InspectedBy = username
WHERE `receiptNum` = $ReceiptNum
");
if (mysqli_num_rows($query) == '0') {
    die(http_response_code(500));
}
$array = mysqli_fetch_assoc($query);

//fetch FTA/DA data
$stmt = mysqli_query($mysqli, "SELECT SampleNum, ifnull(Pressure1, 0) AS Pressure1, ifnull(Pressure2, 0) AS Pressure2, ifnull(Weight, 0) AS Weight FROM quality_AppleSamples WHERE `receiptNum` = '" . $ReceiptNum . "'");
$stmt2 = mysqli_fetch_all($stmt, 1);

$output = array_merge(array_map('rtrim', $array), $stmt2);
echo json_encode($output);
