<?php
require '../config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT allowedPurchasing, `Real Name` as RealName, Role FROM packapps_master_users JOIN purchasing_UserData ON packapps_master_users.username=purchasing_UserData.Username WHERE packapps_master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedPurchasing'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
    }
}
// end authentication
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content='Purchasing dashboard'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Maintenance Dashboard</title>

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="../styles-common/materialIcons/material-icons.css">
    <link rel="stylesheet" href="../styles-common/material.min.css">
    <link rel="stylesheet" href="../styles-common/styles.css">
    <link rel="stylesheet" href="../styles-common/select2.min.css">
</head>
<body>
<div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
    <header class="demo-header mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
        <div class="mdl-layout__header-row">
            <span class="mdl-layout-title">Bills of Material (Finished Product)</span>
            <div class="mdl-layout-spacer"></div>
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
                <label class="mdl-button mdl-js-button mdl-button--icon" for="search">
                    <i class="material-icons">search</i>
                </label>
                <div class="mdl-textfield__expandable-holder">
                    <input class="mdl-textfield__input" type="text" id="search" placeholder="Search Inventory">
                </div>
            </div>
        </div>
    </header>
    <div class="demo-drawer mdl-layout__drawer mdl-color--blue-grey-900 mdl-color-text--blue-grey-50">
        <header class="demo-drawer-header">
            <div class="demo-avatar-dropdown">
                <i style="margin: 2px" class="material-icons">account_circle</i>
                <span style='text-align: center;'><? echo $RealName['RealName'] ?></span>
                <div class="mdl-layout-spacer"></div>
                <button id="accbtn" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon">
                    <i class="material-icons" role="presentation">arrow_drop_down</i>
                    <span class="visuallyhidden">Accounts</span>
                </button>
                <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="accbtn">
                    <? echo($RealName['Role'] > 1 ? "<li class=\"mdl-menu__item\"><i class=\"material-icons\">verified_user</i>Authorized for Purchases</li>" : '') ?>
                    <li onclick="location.href = '/appMenu.php'" class="mdl-menu__item"><i class="material-icons">exit_to_app</i>Exit
                        to menu
                    </li>
                </ul>
            </div>
        </header>
        <nav class="demo-navigation mdl-navigation mdl-color--blue-grey-800">
            <div id="shopping_cart_tag" style='text-align: center; display: none'>
                <button onclick="$('.mdl-card').fadeOut('fast'),location.href='checkout.php'"
                        class='mdl-button mdl-js-ripple-effect mdl-js-button mdl-button--raised mdl-button--accent'><i
                        style='vertical-align: middle' class='material-icons'>shopping_cart</i> Checkout orders
                </button>
                <p style="margin-top: 3px; margin-bottom: 0; font-size: smaller; color: rgba(255, 255, 255, 0.46); cursor: pointer"
                   onclick="clearShoppingCart()">(Delete Cart)</p></div>
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="index.php"><i
                    class="mdl-color-text--teal-400 material-icons"
                    role="presentation">home</i>Home</a>
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="inventory.php"><i
                    class="mdl-color-text--deep-orange-400 material-icons"
                    role="presentation">view_comfy</i>Inventory</a>
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="purchasehistory.php"><i
                    class="mdl-color-text--yellow-400 material-icons" role="presentation">history</i>Purchases</a>
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="suppliers.php"><i
                    class="mdl-color-text--deep-purple-400 material-icons"
                    role="presentation">contacts</i>Suppliers</a>
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="filemanager.php"><i
                    class="mdl-color-text--amber-400 material-icons"
                    role="presentation">folder</i>Shared Folder</a>
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="bomEditor.php"><i
                    class="mdl-color-text--blue-grey-400 material-icons"
                    role="presentation">receipt</i>BOMs</a>
        </nav>
    </div>
    <main class="mdl-layout__content mdl-color--grey-400">
        <div class="widthfixer mdl-grid" id="bomsaway">
            <!-- bom cards injected here -->
        </div>
    </main>
</div>
<div id='snack' style='z-index: 100' class="mdl-js-snackbar mdl-snackbar">
    <div class="mdl-snackbar__text"></div>
    <button class="mdl-snackbar__action" type="button"></button>
</div>
<script src="../scripts-common/material.min.js"></script>
<script src="../scripts-common/jquery.min.js"></script>
<script src="../scripts-common/select2.min.js"></script>
<script>
    $(document).ready(function () {
        checkShoppingCart();
        generateBOMCards();
    });

    function generateBOMCards() {
        $.getJSON('API/getBOMs.php', function (data) {
            var string = "";
            for (var bom in data) {
                if (data.hasOwnProperty(bom)) {
                    string += "<div class='mdl-card mdl-cell mdl-cell--6-col-desktop mdl-cell--4-col mdl-shadow--4dp'><div class='mdl-card__title mdl-color--cyan-300'><h2 class='mdl-card__title-text'>"
                        + data[bom]['SKU_desc']
                        + "</h2></div><div class='mdl-card__supporting-text'><h5>Items in this SKU:</h5><ul class='mdl-list' id='itemList_"
                        + data[bom]['SKU_ID']
                        + "'><hr>"
                        + generateBOMItemRows(data[bom])
                        + "</ul><button onclick='revealHiddenRowSubmitter("
                        + data[bom]['SKU_ID']
                        + "),$(this).fadeOut()' class='mdl-button mdl-button--colored mdl-button--raised mdl-js-ripple-effect mdl-js-button'>Add Item</button><div id='hiddenNewItem_"
                        + data[bom]['SKU_ID']
                        + "' style='display: none; float: right'>Item: <select id='selectNewItem_"
                        + data[bom]['SKU_ID']
                        + "'><option selected></option></select><button onclick='addRow("
                        + data[bom]['SKU_ID']
                        + ")' id='newRowSubmit_"
                        + data[bom]['SKU_ID']
                        + "' disabled type='button' class='mdl-button mdl-js-button mdl-color--teal-300 mdl-button--fab mdl-button--mini-fab'><i class='material-icons'>arrow_forward</i></button></div><br><Br><span class='mdl-card__subtitle-text'>Envio ProductID: "
                        + data[bom]['SKU_ID']
                        + ", Last Made: "
                        + data[bom]['lastChecked_Date']
                        + "</span></div></div>"
                }
            }
            $('#bomsaway').append(string);
        });
    }

    function generateBOMItemRows(bom) {
        var rowString = "";
        for (var item in bom['items']) {
            if (bom['items'].hasOwnProperty(item)) {
                rowString += "<li class='mdl-list__item mdl-list__item--two-line' id='"
                    + bom['SKU_ID']
                    + "_Item_"
                    + bom['items'][item]['ItemID']
                    + "'><span class='mdl-list__item-primary-content'><span><i onclick='deleteRow("
                    + bom['SKU_ID']
                    + ", "
                    + bom['items'][item]['ItemID']
                    + ")' style='margin-right: 10px' class='material-icons mdl-list__item-icon'>close</i>"
                    + bom['items'][item]['ItemDesc']
                    + "</span><span class='mdl-list__item-sub-title'>Item Type</span></span><span class='mdl-list__item-secondary-content'><input type='number' id='"
                    + bom['SKU_ID']
                    + "_atomInput_"
                    + bom['items'][item]['ItemID']
                    + "' placeholder='0' style='width: 50px' onchange=\"updateItemAtomsPerProduct('"
                    + bom['SKU_ID']
                    + "', '"
                    + bom['items'][item]['ItemID']
                    + "')\"><span class='mdl-list__item-secondary-info'>Units per SKU</span></span></li><hr id='"
                    + bom['SKU_ID']
                    + "_hr_"
                    + bom['items'][item]['ItemID']
                    + "'>";
            }
        }
        return rowString;
    }

    function checkShoppingCart() {
        if (sessionStorage.length > 0) {
            $('#shopping_cart_tag').show();
        } else {
            $('#shopping_cart_tag').fadeOut();
        }
    }

    function addRow(bomNumber) {
        var itemID = $('#selectNewItem_' + bomNumber).val();
        $.get('API/addBOMitem.php?bomnum=' + bomNumber + '&itemid=' + itemID, function () {
            var rowString = "<li class='mdl-list__item mdl-list__item--two-line' id='"
                + bomNumber
                + "_Item_"
                + itemID
                + "'><span class='mdl-list__item-primary-content'><span><i onclick='deleteRow("
                + bomNumber
                + ", "
                + itemID
                + ")' style='margin-right: 10px' class='material-icons mdl-list__item-icon'>close</i>"
                + $('#selectNewItem_' + bomNumber).select2('data')[0].text
                + "</span><span class='mdl-list__item-sub-title'>Item Type</span></span><span class='mdl-list__item-secondary-content'><input type='number' id='"
                + bomNumber
                + "_atomInput_"
                + itemID
                + "' placeholder='0' style='width: 50px' onchange=\"updateItemAtomsPerProduct('"
                + bomNumber
                + "', '"
                + itemID
                + "')\"><span class='mdl-list__item-secondary-info'>Units per SKU</span></span></li><hr id='"
                + bomNumber
                + "_hr_"
                + itemID
                + "'>";
            $("#itemList_" + bomNumber).append(rowString);
        }).error(function () {
            var notification = document.querySelector('.mdl-js-snackbar');
            notification.MaterialSnackbar.showSnackbar(
                {
                    message: "That's already on the list...",
                    timeout: 5000
                });
        });
    }

    function updateItemAtomsPerProduct(bomnum, ItemID) {
        var newAtomsVal = $("#" + bomnum + "_atomInput_" + ItemID).val();
        $.get('API/adjustBomItemAtoms.php?newAtomsVal='+newAtomsVal+"&itemID="+ItemID+"&bomSerial="+bomnum, function() {
            var notification = document.querySelector('.mdl-js-snackbar');
            notification.MaterialSnackbar.showSnackbar(
                {
                    message: 'Units Adjusted.',
                    timeout: 3000
                });
        });
    }

    function deleteRow(bomnumber, itemID) {
        $.get('API/deleteBOMitem.php?bomnum=' + bomnumber + '&itemid=' + itemID, function () {
            $("#" + bomnumber + "_Item_" + itemID).fadeOut(function () {
                $(this).remove()
            });
            $("#" + bomnumber + "_hr_" + itemID).fadeOut(function () {
                $(this).remove()
            });
            var notification = document.querySelector('.mdl-js-snackbar');
            notification.MaterialSnackbar.showSnackbar(
                {
                    message: 'Item removed from BOM.',
                    timeout: 3000
                });
        });
    }

    function revealHiddenRowSubmitter(bomnumber) {
        $.getJSON('API/bomItemSearchProvider.php', function (data) {
            $("#selectNewItem_" + bomnumber).select2({
                data: data,
                placeholder: 'Select an Item Description'
            }).on('change', function () {
                $("#newRowSubmit_" + bomnumber).attr('disabled', false);
            });
            $("#hiddenNewItem_" + bomnumber).fadeIn('fast');
        });
    }

    function clearShoppingCart() {
        for (var key in sessionStorage) {
            if (sessionStorage.hasOwnProperty(key)) {
                sessionStorage.removeItem(key);
            }
        }
        $('#shopping_cart_tag').slideUp();
    }
</script>
</body>
</html>