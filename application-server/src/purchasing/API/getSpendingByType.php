<?
include '../../config.php';
$data = mysqli_query($mysqli, "SELECT Type_Description,sum(QuantityOrdered*PricePerUnit) as Spending FROM purchasing_purchase_history JOIN purchasing_purchases2items ON purchasing_purchase_history.Purchase_ID = purchasing_purchases2items.Purchase_ID JOIN purchasing_Items ON purchasing_purchases2items.Item_ID = purchasing_Items.Item_ID JOIN purchasing_ItemTypes ON purchasing_Items.Type_ID = purchasing_ItemTypes.Type_ID WHERE DateOrdered >= NOW() - INTERVAL 30 DAY GROUP BY purchasing_ItemTypes.Type_ID");
$finishedArray = [];
while ($array = mysqli_fetch_assoc($data))
{
    $finishedArray[$array['Type_Description']] = $array['Spending'];
}
header('Content-type: application/json');
echo json_encode($finishedArray);