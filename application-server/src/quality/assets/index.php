<?
if (!$_GET['RT'])
{
    echo "<script>location.replace('../')</script>";
}
else{
include '../../config.php';
$blockphotos = mysqli_query($mysqli, "SELECT concat('uploadedimages/',`RT#`,'.jpg') AS link, `RT#` AS RT, (CASE WHEN `Block Desc`='' THEN '[No Block]' ELSE `Block Desc` END) AS `Block Desc`, rtrim(`Grower Name`) AS `Grower Name`, (CASE WHEN `Farm Desc`='' THEN '[No Farm]' ELSE `Farm Desc` END) AS `Farm Desc`, `Var Desc`, (CASE WHEN `Str Desc`='' THEN 'No Strain' ELSE rtrim(`Str Desc`) END) AS `Str Desc` FROM (SELECT SortCode FROM BULKOHCSV WHERE `RT#`='" . $_GET['RT'] . "') AS t JOIN RTsWQuality ON t.SortCode=`RTsWQuality`.SortCode WHERE isQA='TRUE' ORDER BY `RT#` DESC LIMIT 20;") or die(mysqli_errno($mysqli));
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


