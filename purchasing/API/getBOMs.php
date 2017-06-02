<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 8/22/2016
 * Time: 7:57 AM
 */
include '../../config.php';

$rawBomData = mysqli_query($mysqli, "SELECT SKU_ID, SKU_desc, lastChecked_Date, assetID, ItemID, numItemAtomsInAsset, concat(Type_Description, ': ', purchasing_Items.ItemDesc) as ItemDesc FROM purchasing_EnvioAddon_envioAssets LEFT JOIN purchasing_EnvioAddon_EnvioAssets2purchasingItems ON purchasing_EnvioAddon_envioAssets.SKU_ID = purchasing_EnvioAddon_EnvioAssets2purchasingItems.AssetID LEFT JOIN purchasing_Items ON purchasing_EnvioAddon_EnvioAssets2purchasingItems.ItemID = purchasing_Items.Item_ID LEFT JOIN purchasing_ItemTypes ON purchasing_Items.Type_ID = purchasing_ItemTypes.Type_ID");
$finalData = array();
$currentEditingBom = array();
while ($bom = mysqli_fetch_assoc($rawBomData)) {
    if (count($currentEditingBom) == 0) {
        //first iteration
        $currentEditingBom = $bom;
        if($bom['ItemID'] != null) {
            $currentEditingBom['items'] = array(array('ItemID' => $bom['ItemID'], 'numItemAtomsInAsset' => $bom['numItemAtomsInAsset'], 'ItemDesc' => $bom['ItemDesc']));
        }
        unset($currentEditingBom['assetID']);
        unset($currentEditingBom['ItemID']);
        unset($currentEditingBom['numItemAtomsInAsset']);
        unset($currentEditingBom['ItemDesc']);
    } elseif ($bom['SKU_ID'] == $currentEditingBom['SKU_ID']) {
        //subsequent iterations over same bom
        array_push($currentEditingBom['items'], array('ItemID' => $bom['ItemID'], 'numItemAtomsInAsset' => $bom['numItemAtomsInAsset'], 'ItemDesc' => $bom['ItemDesc']));
    } else {
        //changing boms in list
        array_push($finalData, $currentEditingBom);
        $currentEditingBom = $bom;
        if($bom['ItemID'] != null) {
            $currentEditingBom['items'] = array(array('ItemID' => $bom['ItemID'], 'numItemAtomsInAsset' => $bom['numItemAtomsInAsset'], 'ItemDesc' => $bom['ItemDesc']));
        }
        unset($currentEditingBom['assetID']);
        unset($currentEditingBom['ItemID']);
        unset($currentEditingBom['numItemAtomsInAsset']);
        unset($currentEditingBom['ItemDesc']);
    }
}
if(count($currentEditingBom) != 0){
    array_push($finalData, $currentEditingBom);
}
echo json_encode($finalData);