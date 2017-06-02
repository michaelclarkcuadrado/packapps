<?php
include '../config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT allowedPurchasing, `Real Name` as RealName, isAuthorizedForPurchases FROM master_users JOIN purchasing_UserData ON master_users.username=purchasing_UserData.Username WHERE master_users.username = '$SecuredUserName'"));
    if(!$checkAllowed['allowedPurchasing'] > 0) {
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
    <title>Purchasing Dashboard</title>

    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="PackApps">
    <link rel="apple-touch-icon" sizes="57x57" href="favicons/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="favicons/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="favicons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="favicons/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="favicons/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="favicons/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="favicons/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="favicons/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="favicons/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="favicons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="favicons/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="manifest.json">
    <link rel="mask-icon" href="favicons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-TileImage" content="/mstile-144x144.png">

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="styles/materialIcons/material-icons.css">
    <link rel="stylesheet" href="styles/material.min.css">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
<style>
    .mdl-card__actions  a {
        color: #e91e63 !important;
    }
    label > a {
        color: #e91e63 !important;
    }
</style>
<div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
    <header class="demo-header mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
        <div class="mdl-layout__header-row">
            <span class="mdl-layout-title">Purchases</span>
            <div class="mdl-layout-spacer"></div>
            <div id="tag"></div>
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
                    <? echo($RealName['isAuthorizedForPurchases'] != 0 ? "<li class=\"mdl-menu__item\"><i class=\"material-icons\">verified_user</i>Authorized for Purchases</li>" : '') ?>
                    <li onclick="location.href = '/appMenu.php'" class="mdl-menu__item"><i class="material-icons">exit_to_app</i>Exit to menu</li>
                </ul>
            </div>
        </header>
        <nav class="demo-navigation mdl-navigation mdl-color--blue-grey-800">
            <div id="shopping_cart_tag" style='text-align: center; display: none'><button onclick="$('.mdl-card').fadeOut('fast'),location.href='checkout.php'" class='mdl-button mdl-js-ripple-effect mdl-js-button mdl-button--raised mdl-button--accent'><i style='vertical-align: middle' class='material-icons'>shopping_cart</i> Checkout orders</button><p style="margin-top: 3px; margin-bottom: 0; font-size: smaller; color: rgba(255, 255, 255, 0.46); cursor: pointer" onclick="clearShoppingCart()">(Delete Cart)</p></div>
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
    <main class='mdl-layout__content mdl-color--grey-400'>
        <div id="fillMeWithPurchaseHistory" class='widthfixer mdl-grid demo-cards'>

        </div>
    </main>
</div>
<div id='recvd_snack' class="mdl-js-snackbar mdl-snackbar">
    <div class="mdl-snackbar__text"></div>
    <button class="mdl-snackbar__action" type="button"></button>
</div>
<script src="scripts/material.min.js"></script>
<script src="scripts/jquery.min.js"></script>
<script>
    var curHistoryPos = 0;
    var passedSuppID = <?echo (isset($_GET['suppID']) ? $_GET['suppID'] : -1)?>;
    var passedSuppName = <?echo (isset($_GET['suppName']) ? "'".$_GET['suppName']."'" : "''")?>;
    var runningTotalPaid = 0;

    $(document).ready(function () {
        checkShoppingCart();
        loadPurchaseHistory(curHistoryPos, passedSuppID, passedSuppName);

        $('main').on('scroll', function()
        {
            if($(this).scrollTop() + $(this).innerHeight()>=$(this)[0].scrollHeight)
            {
                loadPurchaseHistory(curHistoryPos, passedSuppID, passedSuppName)
            }
        })
    });

    function loadPurchaseHistory(historyOffsetPos, suppID, supplierName) {
        $.getJSON('API/getPurchaseHistory.php?offset=' + historyOffsetPos + '&suppID='+suppID, function (data) {
            var string = "";
            if(suppID != -1)
            {
                $('#tag').append("<h2 style='display: initial; vertical-align: text-top; font-size: 14px; padding: 5px; margin-right: 15px; border-radius: 15px; color: white' class='mdl-color--deep-purple-300'><i onclick=\"location.replace('purchasehistory.php')\" style='cursor: pointer; vertical-align: middle' class='material-icons'>close</i> Supplier: " + supplierName + "</h2>");
            }
            if(!jQuery.isEmptyObject(data))
            {
                for (var history in data) {
                    if (data.hasOwnProperty(history)) {
                        var itemList = generateItemList(data[history]['ItemsOrdered']);
                        string += "<div style='display: none' class='HistoryCard mdl-card mdl-shadow--4dp mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet mdl-cell--4-col'><div class='mdl-card__title mdl-color--yellow-300'><h2 class='mdl-card__title-text'>$"
                            + runningTotalPaid.toLocaleString(undefined, { minimumFractionDigits: 2 })
                            + " to "
                            + data[history]['Name']
                            + "</h2></div><h5 "
                            + (data[history]['isReceived'] == 0 ? "style='display: none'" : '')
                            + " class='stamp' id='recd_stamp_"
                            + data[history]['Purchase_ID']
                            + "'><i class='material-icons'>done</i> RECEIVED</h5><div class='mdl-card__supporting-text'><h5>Items Purchased</h5><ul class='mdl-list' style=''>"
                            + itemList
                            + "<hr><li class='mdl-list__item mdl-list__item--two-line'><span class='mdl-list__item-primary-content'><span>Total Paid</span><span class='mdl-list__item-sub-title'>Excludes taxes and shipping</span></span><span class='mdl-list__item-secondary-content'>$"
                            + runningTotalPaid.toLocaleString(undefined, { minimumFractionDigits: 2 })
                            + "</span></li></ul><small class='mdl-card__subtitle-text'>Ordered on: "
                            + data[history]['DateOrdered']
                            + (data[history]['isReceived'] == 0 ? '' : "<br>Received on: "+data[history]['DateReceived'])
                            + "</small><br><small>Purchased by: "
                            + data[history]['InitiatedBy']
                            + "</small></div><div class='mdl-card__actions mdl-card--border'><label><a "
                            + (data[history]['invoice_attached'] == 0 ? "" : "href='filemanager.php?path=Order_Confirmations/"+data[history]['Purchase_ID']+"_confirm.pdf'")
                            + " class='mdl-button mdl-js-button mdl-js-ripple-effect'><i class='material-icons'>"
                            + (data[history]['invoice_attached'] == 0 ? 'attach_file' : 'picture_as_pdf')
                            + "</i>"
                            + (data[history]['invoice_attached'] == 0 ? ' Attach Confirmation' : ' View Confirmation')
                            + "</a>"
                            + (data[history]['invoice_attached'] !== 0 ? "<input type='file' name='confirmation_" + data[history]['Purchase_ID'] + "' class='confirmationUpload' style='display: none'>" : "")
                            + "</label></div><div "
                            + (data[history]['isReceived'] == 0 ? '' : "style='display: none'")
                            + " class='mdl-card__menu'><button onclick=\"receive('"
                            + data[history]['Purchase_ID']
                            + "')\" id='menu_"
                            + data[history]['Purchase_ID']
                            + "' class='mdl-button mdl-js-button mdl-button--icon'><i class='material-icons'>beenhere</i></button><div class='mdl-tooltip' for='menu_"
                            + data[history]['Purchase_ID']
                            + "'>Stamp Done</div></div></div>";
                        runningTotalPaid = 0;
                    }
                }
            } else {
                if (!curHistoryPos > 0){
                    string = "<div style='text-align: center; width: 100%;' class='HistoryCard'><h5>From here, it looks like you haven't made any purchases...</h5></div>"
                }
            }
            $("#fillMeWithPurchaseHistory").append(string);
            $('.confirmationUpload').on('change', function(event){
                var imgUpload = event.target.files;
                var formData = new FormData();
                $.each(imgUpload, function(key, value){
                    formData.append(key, value);
                });
                formData.append('ID', event.target.name);
                console.log(event);
                console.log(formData);
                $.ajax({
                    type: 'post',
                    url: 'API/receive_confirmations.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function () {
                        $(event.target).parent().parent().fadeOut();
                    }
                });

            });
            curHistoryPos += 20;
            $('.mdl-card').fadeIn();
        });
    }

    function generateItemList(jsonItems) {
        var string = "";
        for (var itemOrdered in jsonItems) {
                string += "<li class='mdl-list__item mdl-list__item--two-line'><span class='mdl-list__item-primary-content'><span><i style='margin-right:10px' class='material-icons mdl-list__item-icon'>chevron_right</i>"
                    + jsonItems[itemOrdered]['ItemDesc']
                    + "</span><span class='mdl-list__item-sub-title'>("
                    + jsonItems[itemOrdered]['QuantityOrdered']
                    + " @ $"
                    + jsonItems[itemOrdered]['PricePerUnit']
                    + " each)</span></span><span class='mdl-list__item-secondary-content'>"
                    + "$"
                    + (jsonItems[itemOrdered]['QuantityOrdered']*jsonItems[itemOrdered]['PricePerUnit']).toLocaleString(undefined, { minimumFractionDigits: 2 })
                    + "</span></li>";
                runningTotalPaid += (jsonItems[itemOrdered]['QuantityOrdered']*jsonItems[itemOrdered]['PricePerUnit']);
        }
        return string;
    }

    function receive(itemID)
    {
        $.get('API/setReceived.php?q='+itemID, function(data) {
           $('#menu_'+itemID).parent().hide();
            $('#recd_stamp_'+itemID).show();
            var notification = document.querySelector('.mdl-js-snackbar');
            notification.MaterialSnackbar.showSnackbar(
                {
                    message: data+' items added to inventory.',
                    timeout: 3000,
                    actionText: 'Undo',
                    actionHandler: function() {
                        $.get('API/setReceived.php?undo='+itemID, function() {
                            $('#recd_stamp_'+itemID).fadeOut('slow');
                            $('#menu_'+itemID).parent().show();
                            notification.MaterialSnackbar.showSnackbar(
                                {
                                    message: 'Items removed from inventory for now.',
                                    timeout: 3700
                                });
                        });
                    }
                }
            );
        });
    }

    function checkShoppingCart() {
        if(sessionStorage.length > 0){
            $('#shopping_cart_tag').show();
        } else {
            $('#shopping_cart_tag').fadeOut();
        }
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