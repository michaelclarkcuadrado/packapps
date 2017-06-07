<?php
include '../config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT allowedPurchasing, `Real Name` as RealName, isAuthorizedForPurchases FROM packapps_master_users JOIN purchasing_UserData ON packapps_master_users.username=purchasing_UserData.Username WHERE packapps_master_users.username = '$SecuredUserName'"));
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
    <meta name="apple-mobile-web-app-capable" content="yes">
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

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#3372DF">


    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="styles/materialIcons/material-icons.css">
    <link rel="stylesheet" href="styles/material.min.css">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
<div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
    <header class="demo-header mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
        <div class="mdl-layout__header-row">
            <span class="mdl-layout-title">Shared Purchasing Files</span>
            <div class="mdl-layout-spacer"></div>
            <?if(!isset($_GET['path'])) {echo "<button class=\"mdl-button mdl-js-button mdl-button--raised\" onclick='window.location = window.location.href.split(\"#\")[0]' ><i class='material-icons'>replay</i> Return to main folder</button>";}?>
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
    <iframe id="managerFrame" src='assets/<?if(isset($_GET['path'])){echo $_GET['path'];}else{echo 'index.php';}?>' class="mdl-layout__content iframe_files">

    </iframe>
</div>
<script src="scripts/material.min.js"></script>
<script src="scripts/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        if(window.location.hash != ''){
            $('#managerFrame').attr('src', 'assets/index.php' + window.location.hash);
        }
       checkShoppingCart();
    });

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