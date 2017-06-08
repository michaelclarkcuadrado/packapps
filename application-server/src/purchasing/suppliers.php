<?php
include '../config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT allowedPurchasing, `Real Name` as RealName, Role FROM packapps_master_users JOIN purchasing_UserData ON packapps_master_users.username=purchasing_UserData.Username WHERE packapps_master_users.username = '$SecuredUserName'"));
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
    <meta name="msapplication-TileImage" content="favicons/mstile-144x144.png">

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
            <span class="mdl-layout-title">Suppliers</span>
            <div class="mdl-layout-spacer"></div>
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
                <label class="mdl-button mdl-js-button mdl-button--icon" for="search">
                    <i class="material-icons">search</i>
                </label>
                <div class="mdl-textfield__expandable-holder">
                    <input class="mdl-textfield__input" type="text" id="search" placeholder="Search Suppliers">
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
    <main class="mdl-layout__content mdl-color--grey-400">
        <div class="widthfixer mdl-grid demo-cards" id='fillMeWithSuppliers'>
            <div id="newSupplierForm" style="display: none" class="mdl-cell mdl-cell--12-col mdl-card mdl-shadow--4dp">
                <div class="mdl-card__title mdl-color--deep-purple-300">
                    <h2 style="color:white" class="mdl-card__title-text">Add new Supplier</h2><div class="mdl-layout-spacer"></div><i style='float: right; cursor: pointer; color: white' onclick="$('#newSupplierForm').slideUp(), $('#addButton').fadeIn()" id="close_newsupplier" class="material-icons">close</i>
                </div>
                <div style="width: initial" class="mdl-card__supporting-text">
                    <form id="newSupplierSubmitter" class='mdl-grid'>
                        <div
                            class="mdl-cell mdl-cell--4-col mdl-textfield--floating-label mdl-textfield mdl-js-textfield">
                            <input required class="mdl-textfield__input" type="text" name="newCompanyName"
                                   id="newCompanyName">
                            <label class="mdl-textfield__label" for="newCompanyName">Company Name</label>
                        </div>
                        <div
                            class="mdl-cell mdl-cell--4-col mdl-textfield--floating-label mdl-textfield mdl-js-textfield">
                            <input required class="mdl-textfield__input" type="text" name="newContactName"
                                   id="newContactName">
                            <label class="mdl-textfield__label" for="newContactName">Contact Name</label>
                        </div>
                        <div
                            class="mdl-cell mdl-cell--4-col mdl-textfield--floating-label mdl-textfield mdl-js-textfield">
                            <input required class="mdl-textfield__input" type="text" name="newContactPhone"
                                   id="newContactPhone">
                            <label class="mdl-textfield__label" for="newContactPhone">Contact Phone Number</label>
                        </div>
                        <div
                            class="mdl-cell mdl-cell--4-col mdl-textfield--floating-label mdl-textfield mdl-js-textfield">
                            <input required class="mdl-textfield__input" type="text" name="newContactEmail"
                                   id="newContactEmail">
                            <label class="mdl-textfield__label" for="newContactEmail">Contact Email</label>
                        </div>
                        <div
                            class="mdl-cell mdl-cell--4-col mdl-textfield--floating-label mdl-textfield mdl-js-textfield">
                            <input required class="mdl-textfield__input" type="text" name="newInternalContact"
                                   id="newInternalContact">
                            <label class="mdl-textfield__label" for="newInternalContact">Internal Contact</label>
                        </div>
                        <button class="mdl-cell mdl-cell--4-col mdl-button mdl-js-button mdl-button--raised">
                            Create Listing
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <button id="addButton" onclick="showNewForm()"
            style="position: fixed; right: 24px; bottom: 24px; padding-top: 24px; margin-bottom: 0; z-index: 998; color: white"
            class="mdl-button mdl-shadow--8dp mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored mdl-color--deep-purple-300">
        <i class="material-icons">add</i>
    </button>
</div>
<script src="scripts/material.min.js"></script>
<script src="scripts/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        checkShoppingCart();
        loadSuppliers();

        //init search listener
        $('#search').keyup(function (e) {
            clearTimeout($.data(this, 'timer'));
            if (e.keyCode == 13)
                search();
            else
                $(this).data('timer', setTimeout(search, 150));
        });

        function search() {
            var existingString = $("#search").val();
            $.getJSON('API/supplierSearch.php?q=' + existingString, function (data) {
                $('.addedSupplierCards').remove();
                generateSupplierCards(data, false);
            });
        }

        $('#newSupplierSubmitter').submit(function (e) {
            $.post('API/newSupplierSubmit.php', $('#newSupplierSubmitter').serialize(), function () {
                $('#newSupplierSubmitter')[0].reset();
                $('.addedSupplierCards').remove();
                loadSuppliers();
            });
            e.preventDefault();
        });
    });

    function loadSuppliers() {
        $.ajax({
            type: 'GET',
            url: "API/getSuppliers.php",
            dataType: 'json',
            cache: false,
            success: function (data) {
                generateSupplierCards(data, true);
            }
        });
    }

    function generateSupplierCards(data, anim) {
        var string = "";
        if (data.length > 0) {
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    string += "<div style='display: none' class='mdl-card mdl-shadow--4dp mdl-cell mdl-cell--6-col mdl-cell--4-col-tablet mdl-cell--4-col-phone addedSupplierCards'><div style='color: white' class='mdl-card__title mdl-color--deep-purple-300'><h2 class='mdl-card__title-text'>"
                        + data[key]['Name']
                        + (data[key]['hasFoodSafetyDocuments'] > 0 ? "<div id='hasDocs" + key + "' class='material-icons'>done</div><span for='hasDocs" + key + "' style='z-index: 99' class='mdl-tooltip'>Food Safety Documents on file</span>" : '')
                        + "</h2></div><div class='mdl-card__supporting-text'><ul class='mdl-list'><li class='mdl-list__item'><span class='mdl-list__item-primary-content'><i id='contacticon" + key + "' class='material-icons mdl-list__item-icon'>perm_contact_calendar</i><span for='contacticon" + key + "' style='z-index: 99' class='mdl-tooltip'>Contact Name</span>"
                        + data[key]['ContactName']
                        + "</span></li><li class='mdl-list__item'><span class='mdl-list__item-primary-content'><a style='text-decoration: none' href='tel:"
                        + data[key]['ContactPhone']
                        + "'><i  id='contactphone" + key + "' class='material-icons mdl-list__item-icon'>contact_phone</i><span for='contactphone" + key + "' style='z-index: 99' class='mdl-tooltip'>Contact Phone #</span>"
                        + data[key]['ContactPhone']
                        + "</a></span></li><li class='mdl-list__item'><span class='mdl-list__item-primary-content'><a style='text-decoration: none' href='mailto:"
                        + data [key]['ContactEmail']
                        + "'><i  id='contactemail" + key + "' class='material-icons mdl-list__item-icon'>contact_mail</i><span for='contactemail" + key + "' style='z-index: 99' class='mdl-tooltip'>Contact Email</span>"
                        + data [key]['ContactEmail']
                        + "</a></span></li><li class='mdl-list__item'><span class='mdl-list__item-primary-content'><i  id='managedby" + key + "' class='material-icons mdl-list__item-icon'>person</i><span for='managedby" + key + "' style='z-index: 99' class='mdl-tooltip'>Internal Contact</span>"
                        + data[key]['InternalContact']
                        + "</span></li></ul><small>Last activity: "
                        + data[key]['lastInteracted']
                        + "</small></div><div class='mdl-card__actions mdl-card--border'><a href='inventory.php?autoSearch="
                        + data[key]['SupplierID']
                        + "&supplierName="
                        + data[key]['Name']
                        + "' class='mdl-button mdl-js-button mdl-js-ripple-effect'>Browse Products</a><a href='purchasehistory.php?suppID="
                        + data[key]['SupplierID']
                        + "&suppName="
                        + data[key]['Name']
                        + "' class='mdl-button mdl-js-button mdl-js-ripple-effect'>View past purchases</a></div><div class='mdl-card__menu'><button style='color: white' id='demo-menu-lower-right" + key + "' class='mdl-button mdl-js-button mdl-button--icon'><i class='material-icons'>more_vert</i></button><ul class='mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect' for='demo-menu-lower-right" + key + "'>"
                        + "<li onclick='openEditBox(" + data[key]['SupplierID'] + ");' class='mdl-menu__item'>Edit Supplier</li><li onclick=\"location.href='filemanager.php#Food_Safety_Docs/"
                        + data[key]['SupplierID']
                        + "'\" class='mdl-menu__item'>Food Safety Documents</li></ul></div></div>";
                }
            }
            $('#fillMeWithSuppliers').append(string);
            componentHandler.upgradeDom();
            if (anim) {
                $('.addedSupplierCards').fadeIn('fast');
            } else {
                $('.addedSupplierCards').show();
            }
        } else {
            $('#fillMeWithSuppliers').append("<div style='text-align: center; width: 100%;' class='addedSupplierCards'><h5>Can't find anything over here...</h5></div>")
        }
    }

    function openEditBox(ID) {
        var newwindow = window.open("editSupplier.php?supplier=" + ID, 'Edit Supplier', 'scrollbars=0,height=307,width=1050');
        newwindow.onbeforeunload = function () {
            $('.addedSupplierCards').remove();
            loadSuppliers();
        };
        if (window.focus) {
            newwindow.focus()
        }
        return false;
    }

    function showNewForm() {
        $("main").animate({scrollTop: 0}, "fast", "swing", function () {
            $('#newSupplierForm').slideDown('slow')
            $('#addButton').fadeOut();
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