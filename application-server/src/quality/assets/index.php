<?
if (!$_GET['RT'])
{
    echo "<script>location.replace('../')</script>";
}
else{
include '../../config.php';
packapps_authenticate_user();

$_GET['RT'] = mysqli_real_escape_string($mysqli, $_GET['RT']);
$blockphotos = mysqli_query($mysqli, "
SELECT
  concat('//" . $availableBuckets['quality'] . $amazonAWSURL . "quality-rtnum-', `id`, '.jpg') AS link,
  `id`                         AS RT,
  (CASE WHEN `BlockDesc` = ''
    THEN '[No Block]'
   ELSE `BlockDesc` END)      AS `Block Desc`,
  GrowerName                   AS `Grower Name`,
  (CASE WHEN `farmName` = ''
    THEN '[No Farm]'
   ELSE `farmName` END)       AS `Farm Desc`,
  VarietyName AS `Var Desc`,
  (CASE WHEN `strainName` = ''
    THEN 'No Strain'
   ELSE rtrim(`strainName`) END) AS `Str Desc`
FROM storage_grower_receipts
  JOIN `grower_gfbvs-listing` ON storage_grower_receipts.grower_block = `grower_gfbvs-listing`.PK
 WHERE PK = (SELECT PK FROM storage_grower_receipts JOIN `grower_crop-estimates` ON storage_grower_receipts.grower_block = `grower_crop-estimates`.PK WHERE id = '1')
ORDER BY `id` DESC
LIMIT 20;
") or die(mysqli_errno($mysqli));
$blockphotoarray = mysqli_fetch_assoc($blockphotos);
?>

<!DOCTYPE html>
<html style="image-orientation: from-image;background-color: lemonchiffon; text-align:center; img {display: inline-block; float: left}">
<title>Block Viewer</title>
<? if (mysqli_num_rows($blockphotos) == 0) {
    die ("<h2>That block doesn't have any data in the Quality system.</h2>");
};
echo "<h1>Currently Viewing:</h1><h2> The last " . mysqli_num_rows($blockphotos) . " receiving photos of " . $blockphotoarray['Grower Name'] . "'s " . $blockphotoarray['Var Desc'] . " (" . $blockphotoarray['Str Desc'] . ") from " . $blockphotoarray['Farm Desc'] . ", " . $blockphotoarray['Block Desc'] . "</h2><h4>(Sorted Newest to Oldest)</h4>"; ?>

<?
$blockphotos->data_seek(0);
while ($blockphotoarray = mysqli_fetch_assoc($blockphotos)) {
    echo "<a href='" . $blockphotoarray['link'] . "'><img width='33%' src='" . $blockphotoarray['link'] . "'><a> ";
}
} ?>
</html>


