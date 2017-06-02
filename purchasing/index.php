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
    if (!$checkAllowed['allowedPurchasing'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
    }
}
// end authentication
$last2yearschartdata = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2015-1-1' AND '2015-1-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS JanLastYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2015-2-1' AND '2015-2-29' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS FebLastYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2015-3-1' AND '2015-3-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS MarLastYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2015-4-1' AND '2015-4-30' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS AprLastYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2015-5-1' AND '2015-5-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS MayLastYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2015-6-1' AND '2015-6-30' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS JunLastYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2015-7-1' AND '2015-7-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS JulLastYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2015-8-1' AND '2015-8-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS AugLastYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2015-9-1' AND '2015-9-30' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS SepLastYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2015-10-1' AND '2015-10-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS OctLastYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2015-11-1' AND '2015-11-30' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS NovLastYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2015-12-1' AND '2015-12-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS DecLastYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2016-1-1' AND '2016-1-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS JanThisYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2016-2-1' AND '2016-2-29' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS FebThisYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2016-3-1' AND '2016-3-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS MarThisYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2016-4-1' AND '2016-4-30' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS AprThisYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2016-5-1' AND '2016-5-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS MayThisYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2016-6-1' AND '2016-6-30' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS JunThisYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2016-7-1' AND '2016-7-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS JulThisYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2016-8-1' AND '2016-8-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS AugThisYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2016-9-1' AND '2016-9-30' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS SepThisYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2016-10-1' AND '2016-10-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS OctThisYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2016-11-1' AND '2016-11-30' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS NovThisYear,IFNULL(SUM(CASE WHEN CAST(DateOrdered AS DATE) BETWEEN '2016-12-1' AND '2016-12-31' THEN ROUND(QuantityOrdered*PricePerUnit, 2) ELSE 0 END), 0.00) AS DecThisYear FROM purchasing_purchase_history JOIN purchasing_purchases2items ON purchasing_purchase_history.Purchase_ID = purchasing_purchases2items.Purchase_ID"));

//subtitle stats
$last30DaysStats = mysqli_fetch_array(mysqli_query($mysqli, "SELECT count(DISTINCT purchasing_purchase_history.Purchase_ID) AS amount, FORMAT(sum(ifnull(PricePerUnit*QuantityOrdered, 0.00)), 2) AS totalPrice FROM purchasing_purchase_history JOIN operationsData.purchasing_purchases2items ON purchasing_purchase_history.Purchase_ID=operationsData.purchasing_purchases2items.Purchase_ID WHERE DateOrdered >= (NOW() - INTERVAL 30 DAY)"));
$yearToDateStats = mysqli_fetch_array(mysqli_query($mysqli, "SELECT count(DISTINCT purchasing_purchase_history.Purchase_ID) AS amount, FORMAT(sum(ifnull(PricePerUnit*QuantityOrdered, 0.00)), 2) AS totalPrice FROM purchasing_purchase_history JOIN operationsData.purchasing_purchases2items ON purchasing_purchase_history.Purchase_ID=operationsData.purchasing_purchases2items.Purchase_ID WHERE DateOrdered BETWEEN " . date('Y') . "-01-01 AND NOW()"));
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
            <span class="mdl-layout-title">Home</span>
            <div class="mdl-layout-spacer"></div>
            <? echo $companyName ?>
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
                   onclick="clearShoppingCart()">(Delete Cart)</p>
            </div>
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
        <div class="widthfixer mdl-grid demo-cards">
            <div style='display: none' class="mdl-card mdl-shadow--4dp mdl-color--white mdl-cell mdl-cell--8-col">
                <div style="color: white" class="mdl-card__title mdl-color--teal-300 ">
                    <h2 class="mdl-card__title-text">Purchasing Overview</h2>
                </div>
                <div style="margin-left: 25px; padding: 0;" class='mdl-card__supporting-text'>
                    <canvas id="purchaseDollarsYear"></canvas>
                    <br>
                    <b>Purchases in last 30 days:
                        $<? echo $last30DaysStats['totalPrice'] . " across " . $last30DaysStats['amount']; ?>
                        orders.</b><br>
                    <b>Year to date purchases:
                        $<? echo $yearToDateStats['totalPrice'] . " across " . $yearToDateStats['amount']; ?>
                        orders.</b><br>
                </div>
                <div class="mdl-card__actions mdl-card--border">
                    <a href="purchasehistory.php" class="mdl-button mdl-js-button mdl-js-ripple-effect">View
                        Purchases</a>
                </div>
            </div>
            <div style='display: none'
                 class="mdl-card mdl-shadow--4dp mdl-cell mdl-cell--4-col">
                <div class="mdl-card__title mdl-color--teal-300">
                    <h2 style="color: white" class="mdl-card__title-text">Recent Spending Categories</h2>
                </div>
                <div class="mdl-card__supporting-text mdl-color-text--grey-600">
                    <canvas id='itemTypePieChart'></canvas>
                    <small>Dollar value in last 30 days</small>
                </div>
                <div class="mdl-card__actions mdl-card--border">
                    <a href="inventory.php" class="mdl-button mdl-js-button mdl-js-ripple-effect">View inventory</a>
                </div>
            </div>

        </div>
    </main>
</div>
<script src="scripts/material.min.js"></script>
<script src="scripts/jquery.min.js"></script>
<script src='scripts/Chart.js'></script>
<script>
    var itemPieChart;
    $(document).ready(function () {
        $('.mdl-card').fadeIn('fast');
        checkShoppingCart();

        var spendingLineChart = new Chart(document.getElementById("purchaseDollarsYear"), {
            type: 'line',
            data: {
                labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                datasets: [{
                    label: "Spending <?echo date('Y') - 1?>",
                    lineTension: 0.3,
                    data: [<?php echo $last2yearschartdata['JanLastYear'] . ", " . $last2yearschartdata['FebLastYear'] . ", " . $last2yearschartdata['MarLastYear'] . ", " . $last2yearschartdata['AprLastYear'] . ", " . $last2yearschartdata['MayLastYear'] . ", " . $last2yearschartdata['JunLastYear'] . ", " . $last2yearschartdata['JulLastYear'] . ", " . $last2yearschartdata['AugLastYear'] . ", " . $last2yearschartdata['SepLastYear'] . ", " . $last2yearschartdata['OctLastYear'] . ", " . $last2yearschartdata['NovLastYear'] . ", " . $last2yearschartdata['DecLastYear']?>]
                }, {
                    label: "Spending <?echo date('Y')?>",
                    lineTension: 0.3,
                    backgroundColor: "rgba(151,187,205,0.2)",
                    borderColor: "rgba(151,187,205,1)",
                    data: [<?php echo $last2yearschartdata['JanThisYear'] . ", " . $last2yearschartdata['FebThisYear'] . ", " . $last2yearschartdata['MarThisYear'] . ", " . $last2yearschartdata['AprThisYear'] . ", " . $last2yearschartdata['MayThisYear'] . ", " . $last2yearschartdata['JunThisYear'] . ", " . $last2yearschartdata['JulThisYear'] . ", " . $last2yearschartdata['AugThisYear'] . ", " . $last2yearschartdata['SepThisYear'] . ", " . $last2yearschartdata['OctThisYear'] . ", " . $last2yearschartdata['NovThisYear'] . ", " . $last2yearschartdata['DecThisYear']?>]
                }]
            },
            options: {
                responsive: true
            }
        });

        //spending per category pie
        $.getJSON('API/getSpendingByType.php', function (data) {
            if ($.isEmptyObject(data)) {
                $('#itemTypePieChart').replaceWith('<h4>No purchases in last 30 days.</h4>');
            } else {
                var dollars = new Array;
                for (var o in data) {
                    if (data.hasOwnProperty(o)) {
                        dollars.push(data[o]);
                    }
                }
                itemPieChart = new Chart(document.getElementById("itemTypePieChart"), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(data),
                        datasets: [{
                            label: "Spending Categories",
                            data: dollars,
                            backgroundColor: getColorArray(Object.keys(data).length),
                        }]
                    },
                    options: {
                        responsive: true
                    }
                });
            }
        });
    });

    function getColorArray(numItems) {
        var array = [];
        for (var i = 0; i < numItems; ++i) {
            array.push(getRandomColor());
        }
        return array;
    }

    function getRandomColor() {
        var letters = '0123456789ABCDEF'.split('');
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    function checkShoppingCart() {
        if (sessionStorage.length > 0) {
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