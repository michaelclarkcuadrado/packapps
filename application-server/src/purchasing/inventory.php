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
    <title>Purchasing Dashboard</title>

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="../styles-common/materialIcons/material-icons.css">
    <link rel="stylesheet" href="../styles-common/material.min.css">
    <link rel="stylesheet" href="../styles-common/styles.css">
    <style>
        a {
            color: #ff8a65
        }

        .mdl-button--primary.mdl-button--primary {
            color: #ff8a65
        }
    </style>
</head>
<body>
<div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
    <header class="demo-header mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
        <div class="mdl-layout__header-row">
            <span class="mdl-layout-title">Inventory</span>
            <div class="mdl-layout-spacer"></div>
            <label class="mdl-icon-toggle mdl-js-icon-toggle mdl-js-ripple-effect" style="margin-right: 15px"
                   for="icon-toggle-1">
                <input onchange="isListViewEnabled = !isListViewEnabled, rerenderCurrentCards()" type="checkbox"
                       id="icon-toggle-1"
                       class="mdl-icon-toggle__input">
                <i class="mdl-icon-toggle__label material-icons">assignment</i>
            </label>
            <div id='tag'></div>
            <button id='categoriesButton' style="display: none; color: white; padding-right: 8px; margin-right: 20px"
                    class="mdl-button  mdl-js-button mdl-button--raised mdl-color--deep-orange-300"
                    onclick="$('main').animate({scrollTop: 0}, 'fast', 'swing'),$(this).fadeOut(),$('#typeCard').slideDown()">
                Categories <i class="material-icons">expand_more</i></button>
            <div id="expandable_search" class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
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
                <span style='text-align: center;width:100%'><? echo $RealName['RealName'] ?></span>
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
            <a class="mdl-navigation__link" style="padding-left:70px" onClick="$('.mdl-card').fadeOut('fast');"
               href="ItemRecycle.php"><i
                    class="mdl-color-text--deep-orange-400 material-icons"
                    role="presentation">delete</i>Recycle Bin</a>
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="purchasehistory.php"><i
                    class="mdl-color-text--yellow-400 material-icons" role="presentation">history</i>Purchases</a>
            <a class="mdl-navigation__link" onClick="$('.mdl-card').fadeOut('fast');" href="suppliers.php"><i
                    class="mdl-color-text--deep-purple-400 material-icons"
                    role="presentation">contacts</i>Suppliers</a>
        </nav>
    </div>
    <main class="mdl-layout__content mdl-color--grey-400">
        <div id="fillMeWithItems" class="mdl-grid demo-cards widthfixer">
            <div style='display: none' id="newItem_Card"
                 class="mdl-card mdl-shadow--4dp mdl-cell mdl-cell--12-col mdl-cell--8-col-tablet mdl-cell--4-col-phone">
                <div style="color: white" class="mdl-card__title mdl-color--deep-orange-300">
                    <h2 class="mdl-card__title-text">New Inventory Item</h2>
                    <div class="mdl-layout-spacer"><i style="cursor: pointer; float: right"
                                                      onclick="$('#newItem_Card').slideUp(), $('#addButton').fadeIn()"
                                                      class="material-icons">close</i></div>
                </div>
                <div style='width: initial' class="mdl-card__supporting-text">
                    <form id="newItemSubmitter" class="mdl-grid">
                        <div id="Spacer" style='position: absolute; text-align: center'
                             class='mdl-cell--12-col-desktop mdl-cell--8-col-tablet mdl-cell--4-col-phone'>
                            <!-- radios injected here -->
                        </div>
                        <div
                            class='mdl-cell mdl-cell--12-col-desktop mdl-cell--8-col-tablet mdl-cell--4-col-phone mdl-grid'
                            style='margin-top: 35px; width: 100%'>
                            <div
                                class="mdl-cell mdl-cell--6-col mdl-cell--4-col-phone mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="text" id="newItemDesc" name="newItemDesc"
                                       required>
                                <label class="mdl-textfield__label" for="newItemDesc">Item Name</label>
                            </div>
                            <div
                                class="mdl-cell mdl-cell--3-col mdl-cell--4-col-phone mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="number" id="newQuantity" name="newQuantity"
                                       required>
                                <label class="mdl-textfield__label" for="newQuantity">Quantity per Unit</label>
                            </div>
                            <button
                                class='mdl-cell mdl-cell--3-col mdl-cell--4-col-phone mdl-button mdl-js-button mdl-button--raised'>
                                Add
                                item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div id='typeCard' style='display: none'
                 class="mdl-card mdl-shadow--4dp mdl-cell mdl-cell--12-col mdl-cell--8-col-tablet mdl-cell--4-col-phone">
                <div style="color: white" class="mdl-card__title mdl-color--deep-orange-300">
                    <h2 class="mdl-card__title-text">Select a Category</h2>
                </div>
                <div id="fillMeWithTypes" class="mdl-grid mdl-card__supporting-text">
                </div>
            </div>
        </div>
    </main>
    <button id="addButton" onclick="showNewForm()"
            style="position: fixed; right: 24px; bottom: 24px; padding-top: 24px; margin-bottom: 0; z-index: 90; color: white"
            class="mdl-button mdl-shadow--8dp mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored mdl-color--deep-orange-300">
        <i class="material-icons">add</i>
    </button>
</div>
<div id='snack' style='z-index: 100' class="mdl-js-snackbar mdl-snackbar">
    <div class="mdl-snackbar__text"></div>
    <button class="mdl-snackbar__action" type="button"></button>
</div>
<script src="../scripts-common/material.min.js"></script>
<script src="../scripts-common/jquery.min.js"></script>
<script src="../scripts-common/moment.js"></script>
<script src="../scripts-common/Chart.js"></script>
<script>
    var currentTypeID = -1;
    var inventoryHTML = {};
    var curTypeName = "";
    var isSupplierView = <?if (isset($_GET['autoSearch']) && isset($_GET['supplierName'])) {
        echo 'true';
    } else {
        echo 'false';
    }?>;
    var isListViewEnabled = false;
    var currentOnScreenCards = {};
    var chartObjectStorage = {};
    <?php
    if (isset($_GET['autoSearch']) && isset($_GET['supplierName'])) {
        echo "var supplierViewID='" . $_GET['autoSearch'] . "'; var supplierViewName='" . $_GET['supplierName'] . "';";
    }
    ?>

    $(document).ready(function () {
        $('.mdl-card').fadeIn('fast');
        $('#newItem_Card').hide();
        checkShoppingCart();
        loadTypes();

        <?
        if (isset($_GET['autoSearch']) && isset($_GET['supplierName'])) {
            echo "supplierMatch('" . $_GET['autoSearch'] . "', '" . $_GET['supplierName'] . "')";
        }
        ?>

        //init search listener
        $('#search').keyup(function (e) {
            clearTimeout($.data(this, 'timer'));
            if (e.keyCode == 13)
                search();
            else
                $(this).data('timer', setTimeout(search, 150));
        });

        $('#newItemSubmitter').submit(function (e) {
            $.post('API/newItemSubmit.php', $('#newItemSubmitter').serialize(), function () {
                $('.ItemCard').remove();
                loadProductsForType(currentTypeID, false);
                $('#newItemSubmitter')[0].reset();
                //uncheck type in style too
                $('.is-checked').removeClass('is-checked');
            });
            e.preventDefault();
        });
    });

    function showNewForm() {
        $("main").animate({scrollTop: 0}, "fast", "swing", function () {
            $('#newItem_Card').slideDown();
            $('#addButton').fadeOut();
        });
    }

    function search() {
        var existingString = $("#search").val();
        $.getJSON('API/inventorySearch.php?q=' + existingString + '&TypeID=' + currentTypeID, function (data) {
            $('.ItemCard').remove();
            generateInventoryCards(data, false);
        });
    }

    function supplierMatch(supplier, supplierName) {
        $.getJSON('API/getItemsBySupplier.php?q=' + supplier, function (data) {
            $('#typeCard').remove();
            $('.ItemCard').remove();
            $('#categoriesButton').remove();
            $('#expandable_search').remove();
            generateTag();
            generateInventoryCards(data, false);
        });
    }

    function loadTypes() {
        $.getJSON('API/getItemTypes.php', function (data) {
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    $('#fillMeWithTypes').append("<button onclick=\"curTypeName='" + data[key]['Type_Description'] + "',loadProductsForType(" + data[key]['Type_ID'] + ", '" + data[key]['Type_Description'] + "' ,true)\" class=\"mdl-button mdl-js-button mdl-button--primary mdl-js-ripple-effect mdl-shadow--6dp mdl-cell mdl-cell--2-col\" style=\"height: 125px; float: left; margin: 6px; border-radius: 12px; word-wrap: break-word; text-align: center; font-size: x-large; vertical-align: middle\">" + data[key]['Type_Description'] + "<p style='font-size: small;position: absolute; width: 100%; left: 0; color: #757575'>(" + data[key]['ItemCount'] + " items)</p></button></div>");
                    $('#Spacer').append("<label style='margin-right: 15px' class='mdl-radio mdl-js-radio mdl-js-ripple-effect' for='type-" + data[key]['Type_ID'] + "'><input type='radio' id='type-" + data[key]['Type_ID'] + "' class='mdl-radio__button' name='type' value='" + data[key]['Type_ID'] + "' required><span class='mdl-radio__label'>" + data[key]['Type_Description'] + "</span></label>");
                }
            }
            componentHandler.upgradeDom();
        });
    }

    function addToShoppingCart(suppID, suppName, itemID, itemName) {
        if (sessionStorage.getItem(suppID) === null) {
            var obj = {};
            obj['suppName'] = suppName;
            obj['items'] = {};
            obj['items'][itemID] = itemName;
            sessionStorage.setItem(suppID, JSON.stringify(obj));
        } else {
            var existingObj = JSON.parse(sessionStorage.getItem(suppID));
            if (!existingObj['items'].hasOwnProperty(itemID)) {
                existingObj['items'][itemID] = itemName;
            }
            sessionStorage.setItem(suppID, JSON.stringify(existingObj));
        }
        checkShoppingCart();
        var notification = document.querySelector('.mdl-js-snackbar');
        notification.MaterialSnackbar.showSnackbar(
            {
                message: itemName + ' added to cart.',
                timeout: 3000,
                actionText: 'Undo',
                actionHandler: function () {
                    removeFromShoppingCart(suppID, itemID);
                }
            });
    }

    function removeFromShoppingCart(suppID, itemID) {
        var obj = JSON.parse(sessionStorage.getItem(suppID));
        delete obj['items'][itemID];
        if (jQuery.isEmptyObject(obj['items'])) {
            sessionStorage.removeItem(suppID);
        } else {
            sessionStorage.setItem(suppID, JSON.stringify(obj));
        }
        checkShoppingCart();
        var notification = document.querySelector('.mdl-js-snackbar');
        notification.MaterialSnackbar.showSnackbar(
            {
                message: 'Removed from cart.',
                timeout: 3000
            });
    }

    function generateTag() {
        if (typeof supplierViewName == "undefined") {
            $('#tag').append("<h2 style='display: initial; vertical-align: text-top; font-size: 14px; padding: 5px; margin-right: 15px; border-radius: 15px; color: white' class='ItemCard mdl-cell--hide-phone mdl-color--deep-orange-300'>Showing: " + (curTypeName == '' ? 'Search Results': curTypeName) + "</h2>");
        } else {
            $('#tag').append("<h2 style='display: initial; vertical-align: text-top; font-size: 14px; padding: 5px; margin-right: 15px; border-radius: 15px; color: white' class='ItemCard mdl-color--deep-purple-300'><i onclick=\"location.replace('inventory.php')\" style='cursor: pointer; vertical-align: middle' class='material-icons'>close</i> Supplier: " + supplierViewName + "</h2>");
        }
    }


    function loadProductsForType(typeID, typeName, anim) {
        currentTypeID = typeID;
        $('.ItemCard').fadeOut().remove();
        $('#typeCard').slideUp();
        generateTag();
        $('#categoriesButton').fadeIn();
        $.getJSON('API/getInventoryItems.php?type=' + typeID, function (data) {
            generateInventoryCards(data, anim);
            $('#search').val('');
        });
    }

    function rerenderCurrentCards() {
        $('.ItemCard').remove();
        generateTag();
        generateInventoryCards(currentOnScreenCards, false);
    }

    function generateInventoryCards(data, anim) {
        currentOnScreenCards = data;
        if (!isListViewEnabled) {
            var string = "";
        } else {
            var string = "<table style='margin: 8px' class='mdl-data-table mdl-cell--12-col mdl-cell--4-col-phone mdl-js-data-table ItemCard mdl-shadow--2dp'><thead><tr><th class='mdl-data-table__cell--non-numeric'>Item Name</th><th>Quantity</th></tr></thead><tbody>";
        }
        if (!jQuery.isEmptyObject(data)) {
            for (var item in data) {
                if (data.hasOwnProperty(item)) {
                    if (!isListViewEnabled) {
                        string += "<div style='display: none' class='ItemCard mdl-card mdl-shadow--4dp mdl-cell mdl-cell--4-col mdl-cell--4-col-tablet mdl-cell--4-col-phone'><div style='color: white' class='mdl-card__title mdl-color--deep-orange-300'><h2 style='margin-right: 30px' class='mdl-card__title-text'>"
                            + data[item]['ItemDesc']
                            + "</h2></div><div class='mdl-card__supporting-text'><form style='display: none' id='newNameSubmissionForm_"
                            + item
                            + "'><input type='hidden' name='renameID' value='"
                            + item
                            + "'><label>New name: <input type='text' maxlength='255' name='newName'></label><button>Send</button></form><ul class='mdl-list'><li style='cursor: pointer' class='mdl-list__item mdl-list__item--two-line'><span onclick='editInventory(\""
                            + item
                            + "\")' class='mdl-list__item-primary-content'><i class='material-icons mdl-list__item-icon'>assignment</i><span id='" + +item
                            + "'>"
                            + data[item]['AmountInStock']
                            + " "
                            + data[item]['UnitOfMeasure']
                            + " in Inventory</span><span class='mdl-list__item-sub-title'>Tap to correct</span></span></li><li class='mdl-list__item mdl-list__item--two-line'><span class='mdl-list__item-primary-content'><i class='material-icons mdl-list__item-icon'>show_chart</i><span>View Usage Data</span><span class='mdl-list__item-sub-title'>Inventory Levels</span></span><span class='mdl-list__item-secondary-content'><a><i onclick='showUsageGraph("
                            + item
                            + ", $(this))' class='material-icons mdl-list__item-secondary-action'>expand_more</i></a></span></li><li id='usage_graph_" + item
                            + "' style='display: none'></li><li class='mdl-list__item mdl-list__item--two-line'><span class='mdl-list__item-primary-content'><i class='material-icons mdl-list__item-icon'>contacts</i><span>View Quotes</span><span class='mdl-list__item-sub-title'>"
                            + (typeof data[item]['Suppliers'] != 'undefined' ? Object.keys(data[item]['Suppliers']).length : '0')
                            + " Suppliers</span></span><span class='mdl-list__item-secondary-content'><a><i id='icon_supplier_list_"
                            + item
                            + "' onclick=\"toggleSublist('"
                            + item
                            + "', \'supplier_list\')\" class='material-icons mdl-list__item-secondary-action'>expand_more</i></a></span></li><div style='display: none' id='supplier_list_"
                            + item
                            + "' class='sublist_supplier'>"
                            + createSupplierList(data[item])
                            + "</ul><div style='display: none' id='associatedSuppliersTable_"
                            + item
                            + "'><hr><span class='mdl-list__item'>Edit Suppliers and Quotes<div class='mdl-layout-spacer'></div><i onclick='$(this).parent().parent().slideUp()' style='cursor: pointer' class='material-icons'>clear</i></span><form id='form_supplier_prices_" + +item
                            + "'><input type='hidden' name='item_ID' value=\""
                            + item
                            + "\"><table style='margin: auto' class='mdl-data-table mdl-js-data-table mdl-shadow--2dp'><thead><tr><th class='mdl-data-table__cell--non-numeric mdl-data'>Supplier</th><th class='mdl-data-table__cell--non-numeric'>Quoted Price per unit</th></tr></thead><tbody id='fillMeWithPotentialSuppliers_"
                            + item
                            + "'></tbody></table></form><br><button onClick='sendNewSuppliers(\""
                            + item
                            + "\")' style='float: right' class='mdl-button--raised mdl-button'>Publish quotes</button></div><small class='mdl-card__subtitle-text'>Sets of "
                            + data[item]['QtyPerUnit']
                            + "</small></div><div class='mdl-card__menu'><button class='mdl-button mdl-js-button mdl-button--icon'  id='dropDownMenu_"
                            + item
                            + "'><i style='color: white' class='material-icons'>more_vert</i></button><ul class='mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect' for='dropDownMenu_"
                            + item
                            + "'><li onclick='renameItem(\""
                            + item
                            + "\")' class='mdl-menu__item'>Rename Item</li><li onclick=\"associateSupplier("
                            + item
                            + ", '"
                            + data[item]['ItemDesc']
                            + "')\" class='mdl-menu__item'>Edit Suppliers and Quotes</li><li class='mdl-menu__item' onclick='disableItem("
                            + item
                            + ")'>Retire Item</li></ul></div></div>";
                    } else {
                        string += "<tr><td style='font-size: 25px; white-space: normal; line-height: initial' class='mdl-data-table__cell--non-numeric'>"
                            + data[item]['ItemDesc']
                            + "</td><td><span style='display: none' id='receivedPopup_"
                            + item
                            + "'><i class='material-icons'>done</i> Received!</span><input class='listViewInventorySubmitter' name='"
                            + item
                            + "' type='number' value='"
                            + data[item]['AmountInStock']
                            + "'></td></tr>";
                    }
                }
            }
            if (isListViewEnabled) {
                string += "</tbody></table>";
            }
            $("#fillMeWithItems").append(string);
            componentHandler.upgradeDom();
            if (isListViewEnabled) {
                $(".listViewInventorySubmitter").off("change").on("change", function () {
                    $.post("API/editInventoryItem.php", $(this).serialize(), function () {
                        var notification = document.querySelector('.mdl-js-snackbar');
                        notification.MaterialSnackbar.showSnackbar(
                            {
                                message: 'Inventory Received.',
                                timeout: 1000
                            });
                    });
                });
            }
            if (anim) {
                $('.ItemCard').fadeIn();
            } else {
                $('.ItemCard').show();
            }
        } else {
            $('#fillMeWithItems').append("<div style='text-align: center; width: 100%;' class='ItemCard'><h5>Looks a little empty. Add something?</h5></div>")
        }
    }

    //populates the chart for usage data
    function showUsageGraph(ItemID, caller) {
        var graphClosing = caller.hasClass('rotate');
        caller.toggleClass('rotate');
        if (graphClosing) {
            $("#usage_graph_" + ItemID).slideToggle();
            chartObjectStorage[ItemID].destroy();
        } else {
            $.getJSON('API/getItemInventoryHistory.php?itemID=' + ItemID, function (data) {
                if (jQuery.isEmptyObject(data) || Object.keys(data).length == 1) {
                    $("#usage_graph_" + ItemID).css('text-align', 'center').html("<span>No Historical Data Available</span>").slideDown();
                } else {
                    $("#usage_graph_" + ItemID).html("<canvas id='canvas_usage_graph_" + ItemID + "' style='width: 100%;'></canvas>").slideToggle();
                    var canvas = $("#canvas_usage_graph_" + ItemID);
                    var chart = new Chart(canvas, {
                        type: 'line',
                        data: {
                            labels: createTimeSeriesLabel(data),
                            datasets: [{
                                label: 'Inventory @ time',
                                data: createDataPoints(data),
                                borderDash: [5, 5],
                                lineTension: .15,
                                backgroundColor: '#ff8a65'
                            }]
                        },
                        options: {
                            responsive: true,
                            scaleShowLabels: false,
                            legend: {
                                display: false,
                            },
                            title: {
                                display: false,
                                text: "Inventory"
                            },
                            scales: {
                                xAxes: [{
                                    type: "time",
                                    time: {
                                        format: 'MM/DD/YYYY',
                                        // round: 'day'
                                        tooltipFormat: 'll HH:mm'
                                    },
                                    scaleLabel: {
                                        display: false,
                                        labelString: 'Date'
                                    }
                                },],
                                yAxes: [{
                                    scaleLabel: {
                                        display: false,
                                        labelString: 'value'
                                    }
                                }]
                            },
                        }
                    });
                    chartObjectStorage[ItemID] = chart;
                }
            });
        }
    }

    function createTimeSeriesLabel(data) {
        //returns only one label, the oldest date in dataset
        var array = [];
        var curMoment = moment(data[0]['TimeReceived']);
        array.push(curMoment.clone());
//        while (curMoment.clone().add(2, 'w').isBefore(moment())) {
//            array.push(curMoment.add(2, 'w').clone());
//        }
        return array;
    }

    function createDataPoints(data) {
        var final = [];
        for (var point in data) {
            if (data.hasOwnProperty(point)) {
                var temp = {};
                temp.x = moment(data[point]['TimeReceived']);
                temp.y = data[point]['Quantity'];
                final.push(temp);
            }
        }
        return final;
    }

    //creates the list div that slides down with inputs
    function associateSupplier(itemID, itemName) {
        $.getJSON('API/loadSuppliersForItem.php?Item_ID=' + itemID, function (data) {
            var string = "";
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    string += "<tr><td class='mdl-data-table__cell--non-numeric'>" + data[key]['Name'] + "</td>";
                    string += "<td>$<input value='" + data[key]['quotedPricePerUnit'] + "' name='" + data[key]['SupplierID'] + "' placeholder='No offer' style='width: 80px' maxlength='6' type='number'></td></tr>";
                }
            }
            $('#fillMeWithPotentialSuppliers_' + itemID).empty().append(string);
            componentHandler.upgradeDom();
            $('#associatedSuppliersTable_' + itemID).slideDown();
        });
    }

    function disableItem(itemID) {
        $.get('API/editInventoryItem.php?disableItem=' + itemID, function () {
            if (isSupplierView) {
                supplierMatch(supplierViewID, supplierViewName);
            } else {
                loadProductsForType(currentTypeID, curTypeName, false);
            }
            var notification = document.querySelector('.mdl-js-snackbar');
            notification.MaterialSnackbar.showSnackbar(
                {
                    message: 'Item removed.',
                    timeout: 4000
                });
        });
    }

    //sends the new prices
    function sendNewSuppliers(itemID) {
        $.post('API/submitSupplierAssociations.php', $("#form_supplier_prices_" + itemID).serialize(), function () {
            $('#associatedSuppliersTable_' + itemID).slideUp();
            if (isSupplierView) {
                supplierMatch(supplierViewID, supplierViewName);
            } else {
                loadProductsForType(currentTypeID, curTypeName, false);
            }
        });
    }

    function editInventory(itemID) {
        inventoryHTML[itemID] = $('#' + itemID).html();
        $('#' + itemID).replaceWith("<label id='" + itemID + "'><input class='inventorySubmitter' type='number' name='" + itemID + "' placeholder='Amount in Inventory'></label>");
        $('.inventorySubmitter').focus().on('change', function () {
            $.post("API/editInventoryItem.php", $(this).serialize(), function () {
                var num = $('#' + itemID + " > input").val();
                if (num != '') {
                    $('#' + itemID).html(inventoryHTML[itemID]).text(num + ' units in inventory');
                }
            });
        });
    }

    function renameItem(itemID) {
        $("#newNameSubmissionForm_" + itemID).slideDown().submit(function (e) {
            $.post('API/renameItem.php', $('#newNameSubmissionForm_' + itemID).serialize(), function () {
                $('#newNameSubmissionForm_' + itemID).slideUp();
                if (isSupplierView) {
                    supplierMatch(supplierViewID, supplierViewName);
                } else {
                    loadProductsForType(currentTypeID, curTypeName, false);
                }
            });
            e.preventDefault();
        });
    }

    function createSupplierList(item) {
        var supplier_sublist = "";
        for (var supplier in item['Suppliers']) {
            if (item['Suppliers'].hasOwnProperty(supplier)) {
                supplier_sublist += "<li class='mdl-list__item mdl-list__item--two-line'><span class='mdl-list__item-primary-content'><i class='material-icons mdl-list__item-icon'>person</i><span>"
                    + item['Suppliers'][supplier]['Name']
                    + "</span><span class='mdl-list__item-sub-title'>$"
                    + item['Suppliers'][supplier]['quotedPricePerUnit']
                    + "/unit</span></span><span class='mdl-list__item-secondary-content'><a class='mdl-list__item-secondary-action' onclick=\"addToShoppingCart("
                    + supplier
                    + ", '"
                    + item['Suppliers'][supplier]['Name']
                    + "' ,"
                    + item['Item_ID']
                    + ", '"
                    + item['ItemDesc']
                    + "')\"><i id='shoppingtooltip_"
                    + item['Item_ID']
                    + "_"
                    + item['Suppliers'][supplier]['SupplierID']
                    + "' class='material-icons'>add_shopping_cart</i></a><div class='mdl-tooltip' for='shoppingtooltip_"
                    + item['Item_ID']
                    + "_"
                    + item['Suppliers'][supplier]['SupplierID']
                    + "'>Add To Cart</div></span></li>";
            }
        }
        supplier_sublist += "</div>";
        return supplier_sublist;
    }

    function checkShoppingCart() {
        if (sessionStorage.length > 0) {
            $('#shopping_cart_tag').fadeIn();
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

    function toggleSublist(itemID, sublistType) {
        $("#" + sublistType + "_" + itemID).slideToggle();
        $("#icon_" + sublistType + "_" + itemID).toggleClass('rotate');
    }
</script>
</body>
</html>