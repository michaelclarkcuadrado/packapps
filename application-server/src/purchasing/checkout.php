<?php
require '../config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT allowedPurchasing, `Real Name` as RealName, Role FROM packapps_master_users JOIN purchasing_UserData ON packapps_master_users.username=purchasing_UserData.Username WHERE packapps_master_users.username = '$SecuredUserName'"));
    if($checkAllowed['Role'] <= 1) {
        die ("<script>window.location.replace('/purchasing')</script>");
    } else {
        $RealName = $checkAllowed;
    }
}
// end authentication?>
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
    <link rel="stylesheet" href="../styles-common/materialIcons/material-icons.css">
    <link rel="stylesheet" href="../styles-common/material.min.css">
    <link rel="stylesheet" href="../styles-common/stepper.css">
    <link rel="stylesheet" href="../styles-common/styles.css">
</head>
<body>
<div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
    <header class="demo-header mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
        <div class="mdl-layout__header-row">
            <span class="mdl-layout-title">Checkout orders</span>
            <div class="mdl-layout-spacer"></div>
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
                    <li onclick='logout()' class="mdl-menu__item"><i class="material-icons">power_settings_new</i>Sign
                        out
                    </li>
                </ul>
            </div>
        </header>
        <nav class="demo-navigation mdl-navigation mdl-color--blue-grey-800">
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
    <main style='100%' class='mdl-layout__content mdl-color--grey-400'>
        <div class="mdl-grid widthfixer demo-cards">
            <ul class="mdl-cell mdl-cell--12-col mdl-shadow--6dp mdl-stepper mdl-stepper--feedback mdl-stepper--linear mdl-stepper--horizontal"
                id="checkout_stepper">

                <!--Step 1: picking supplier -->
                <li class="mdl-step" data-step-transient-message="Pulling up your order...">
                <span class="mdl-step__label">
                    <span class="mdl-step__title">
                        <span class="mdl-step__title-text">Suppliers</span>
                        <span class="mdl-step__title-message">Choose P.O.</span>
                    </span>
                </span>
                    <div id="step1" class="mdl-step__content">
                        <h4>Choose a P.O. to complete</h4>
                        <div style="max-width:360px; margin: auto" class="mdl-list">
                            <!-- supplier list injected here -->
                        </div>
                    </div>
                    <div class="mdl-step__actions">
                        <button style="display: none"
                                class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--colored mdl-button--raised"
                                data-stepper-next>
                            Continue
                        </button>
                        <button onclick="location.href= 'inventory.php'"
                                class="mdl-button mdl-js-button mdl-js-ripple-effect" data-stepper-cancel>
                            Cancel
                        </button>
                    </div>
                </li>

                <!--Step 2: item quantities -->
                <li class="mdl-step" data-step-transient-message="Fetching supplier data...">
            <span class="mdl-step__label">
            <span class="mdl-step__title">
                <span class="mdl-step__title-text">Review Items</span>
                <span class="mdl-step__title-message">Item List</span>
            </span>
            </span>
                    <div id="step2" class="mdl-step__content">
                        <h4 style="margin-bottom: 0">How many do you need?</h4>
                        <p>You can adjust these later if they change during the order process.</p>
                        <ul id='step2ItemList' class="mdl-list">
                            <li class="mdl-list__item">
                            <span class="mdl-list__item-primary-content">
                                <span style="margin-left: 32px">Item</span>
                            </span>
                                <span class="mdl-list__item-secondary-content">
                                Estimated Cost
                            </span>
                            </li>
                            <hr>
                            <div id="itemsInjectedHere">

                            </div>
                            <hr>
                            <div>
                                <li class='mdl-list__item'>
                                <span class='mdl-list__item-primary-content'>
                                <span style='margin-left: 32px'>Subtotal</span>
                                </span>
                                    <span class='mdl-list__item-secondary-content'>
                                        <mark id="subtotal_marked">$0.00</mark>
                                </span>
                                </li>
                                <li class='mdl-list__item'>
                                    <!--Needs to be here to align confirmation box-->
                                    <span class='mdl-list__item-primary-content'>
                                <span style='margin-left: 32px'></span>
                                </span>
                                    <span class='mdl-list__item-secondary-content'>
                                    <label style="display: none"
                                           class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect"
                                           for="step2checkbox">
                                    <input type="checkbox"
                                           id="step2checkbox" class="mdl-checkbox__input">
                                    <span class="mdl-checkbox__label">Confirm subtotal</span>
                                </label>
                                </span>
                                </li>
                            </div>
                        </ul>

                    </div>
                    <div class="mdl-step__actions">
                        <button style="display: none"
                                class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--colored mdl-button--raised"
                                data-stepper-next>
                            Continue
                        </button>
                        <button onclick="" class="mdl-button mdl-js-button mdl-js-ripple-effect" data-stepper-cancel>
                            Cancel
                        </button>
                    </div>
                </li>

                <!-- Step 3 -->
                <li class="mdl-step" data-step-transient-message="Looks good...">
    <span class="mdl-step__label">
          <span class="mdl-step__title">
            <span class="mdl-step__title-text">Contact Vendor</span>
            <span class="mdl-step__title-message">Place Order</span>
    </span>
    </span>
                    <div class="mdl-step__content">
                        <h4 style="margin-bottom: 0">Contact the supplier to place your order.</h4>
                        <p>For your reference, the order details are displayed. Use the prices and quantities worksheet
                            to input the data as you order.</p>
                        <div class='mdl-grid'>
                            <ul id="step3businesscard" style="float:left; display: table"
                                class="mdl-list mdl-cell mdl-cell--4-col mdl-shadow--6dp">
                                <li class="mdl-list__item">
                                 <span class="mdl-list__item-primary-content">
                                    <span style='text-align: center; font-size: 40px'><i
                                            style="vertical-align: middle; font-size: 40px"
                                            class="material-icons">store</i><span id="suppNameCard"></span></span>
                                 </span>
                                </li>
                                <hr style='margin:auto; width:75%' class="mdl-color--light-blue-300">
                                <li class="mdl-list__item">
                                 <span class="mdl-list__item-primary-content">
                                    <span><i style="vertical-align: middle"
                                             class="material-icons">perm_contact_calendar</i><span
                                            id="suppContactNameCard"></span></span>
                                 </span>
                                </li>
                                <li class="mdl-list__item">
                                 <span class="mdl-list__item-primary-content">
                                    <span><i style="vertical-align: middle" class="material-icons">contact_phone</i><a
                                            style="text-decoration: none" id="suppPhoneCard"></a></span>
                                 </span>
                                </li>
                                <li class="mdl-list__item">
                                 <span class="mdl-list__item-primary-content">
                                    <span><i style="vertical-align: middle" class="material-icons">contact_mail</i><a
                                            style="text-decoration: none" id="suppEmailCard"></a></span>
                                 </span>
                                </li>
                                <li class="mdl-list__item">
                                 <span class="mdl-list__item-primary-content">
                                    <span><i style="vertical-align: middle" class="material-icons">history</i> Last activity:<span
                                            id="suppDateCard"></span></span>
                                 </span>
                                </li>
                            </ul>
                            <div id='itemDetailPanel'
                                 class='mdl-shadow--6dp mdl-cell mdl-cell--8-col-desktop mdl-cell--8-col-tablet mdl-cell--4-col-phone'
                                 style="float:left"></div>
                        </div>
                    </div>
                    <div class="mdl-step__actions">
                        <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--colored mdl-button--raised"
                                data-stepper-next>
                            Submit order
                        </button>
                        <button onclick="location.reload()" class="mdl-button mdl-js-button mdl-js-ripple-effect"
                                data-stepper-cancel>
                            Cancel
                        </button>
                    </div>
                </li>
            </ul>
        </div>

    </main>
</div>
<div id='snack' style='z-index: 100' class="mdl-js-snackbar mdl-snackbar">
    <div class="mdl-snackbar__text"></div>
    <button class="mdl-snackbar__action" type="button"></button>
</div>
<script src="../scripts-common/material.min.js"></script>
<script src="../scripts-common/stepper.js"></script>
<script src="../scripts-common/jquery.min.js"></script>
<script>
    var activeVendor = '';
    var curStep = 1;
    var itemDataObj = {};
    var suppDataObj = {};
    //jquery's ready() doesn't cut it for some reason
    window.onload = function () {
        genSupplierOrders();

        //event listener for 'confirm subtotal' button
        $("#step2checkbox").on("change", function (event) {
            if ($("#subtotal_marked").text() != "$0.00" && $("#subtotal_marked").text() != '$NaN') {
                allowStepping();
                if(curStep == 2){
                    $('.amount').prop('disabled', true);
                }
                $("#step2checkbox").parent().fadeOut();
            } else {
                var notification = document.querySelector('.mdl-js-snackbar');
                notification.MaterialSnackbar.showSnackbar(
                    {
                        message: 'You must have at least one item.',
                        timeout: 3000
                    });
            }
        });
        //begin stepper logic
        (function () {
            var selector = '.mdl-stepper#checkout_stepper';
            // Select stepper container element
            var stepperElement = document.querySelector(selector);
            var Stepper;
            var steps;
            var inputTransientMessage /** @type {HTMLElement} */;
            if (!stepperElement) return;

            // Get the MaterialStepper instance of element to control it.
            Stepper = stepperElement.MaterialStepper;

            if (!Stepper) {
                console.error('MaterialStepper instance is not available for selector: ' + selector + '.');
                return;
            }
            steps = stepperElement.querySelectorAll('.mdl-step');
            for (var i = 0; i < steps.length; i++) {

                // When user clicks on [data-stepper-next] button of step.
                steps[i].addEventListener('onstepnext', (function (step) {
                    return function () {
                        disallowStepping();
                        // {element}.MaterialStepper.next() change the state of current step to "completed"
                        // and move one step forward.
                        inputTransientMessage = step.querySelector('#stepper-transient-message');

                        if (inputTransientMessage && inputTransientMessage.value.length) {
                            step.setAttribute('data-step-transient-message', inputTransientMessage.value);
                        }

                        prepareNextStep(stepperElement);
                        setTimeout(function () {
                            Stepper.next();
                        }, 1500);
                    }
                })(steps[i]));
            }
            // When all steps are completed this event is dispatched.
            stepperElement.addEventListener('onsteppercomplete', function (e) {
                location.href ='purchasehistory.php';
            });
        })();
        //end stepper logic

    };

    function prepareNextStep(Stepper) {
        if (curStep == 1) {
            genOrderItems();
        } else if (curStep == 2) {
            //record step2 data into itemDataObj
            $('.amount').each(function () {
                var itemID = $(this).attr('id').substring(7);
                itemDataObj[itemID]['quantityWanted'] = $(this).val();
            });
            getVendorInfo();
        } else if (curStep == 3) {
            submitFinalData();
        }
        curStep++;
    }

    //allows user movement when step input completed
    function allowStepping() {
        $("#checkout_stepper > li.mdl-step.is-active > div.mdl-step__actions > button.mdl-button.mdl-js-button.mdl-js-ripple-effect.mdl-button--colored.mdl-button--raised").show();
    }

    function disallowStepping(){
        $("#checkout_stepper > li.mdl-step.is-active > div.mdl-step__actions > button.mdl-button.mdl-js-button.mdl-js-ripple-effect.mdl-button--colored.mdl-button--raised").hide();
    }

    //run on page load, prepares first step page
    function genSupplierOrders() {
        var string = "";
        for (var key in sessionStorage) {
            if (sessionStorage.hasOwnProperty(key)) {
                string += "<div onclick='setActiveVendor("
                    + key
                    + ")' class='mdl-list__item mdl-list__item--two-line mdl-button--raised'><span class='mdl-list__item-primary-content'><i id='"
                    + key
                    + "_icon' class='material-icons mdl-list__item-avatar'>person</i> <span>"
                    + JSON.parse(sessionStorage.getItem(key))['suppName']
                    + "</span> <span class='mdl-list__item-sub-title'>"
                    + Object.keys(JSON.parse(sessionStorage.getItem(key))['items']).length
                    + " items</span> </span> <a class='mdl-list__item-secondary-action'><i class='material-icons'>chevron_right</i></a></div>";
            }
        }
        $("#step1 > div").append(string);
    }

    //set by step1
    function setActiveVendor(vendorID) {
        $('#' + activeVendor + "_icon").text('person');
        $('#' + vendorID + "_icon").text('done');
        activeVendor = vendorID;
        allowStepping();
    }

    function genOrderItems() {
        var rawobj = JSON.parse(sessionStorage.getItem(activeVendor));
        rawobj['suppID'] = activeVendor;
        //post to server, get prices
        $.ajax({
            type: 'POST',
            url: "API/getItemPrices.php",
            data: rawobj,
            dataType: 'json',
            cache: false,
            success: function (data) {
                itemDataObj = data;
                var string = "";
                for (var key in data) {
                    if (data.hasOwnProperty(key)) {
                        string += "<li style='height: 100px' class='mdl-list__item mdl-list__item--two-line'><span class='mdl-list__item-primary-content'><span><i style='margin-right:10px' class='material-icons mdl-list__item-icon'>chevron_right</i>"
                            + data[key]['Name']
                            + "</span> <span class='mdl-list__item-sub-title'><input required class='amount' name='amount_"
                            + data[key]['ID']
                            + "' id='amount_"
                            + data[key]['ID']
                            + "' placeholder='None' style='width: 4em' type='number' value='0' max='9999' min='0'><span> @ $"
                            + data[key]['quotedPricePerUnit'].toLocaleString()
                            + " (quoted)</span></span><label class='mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect' for'quoteCheckbox_"
                            + data[key]['ID']
                            + "' style='margin-left: 32px;margin-top: 5px; display: none'><input id='quoteCheckbox_"
                            + data[key]['ID']
                            + "' class='mdl-checkbox__input' value='1' type='checkbox'><span class='mdl-checkbox__label'>Save price as new quote?</span></label></span><span id='lineItemSubtotal_"
                            + data[key]['ID']
                            + "' class='mdl-list__item-secondary-content'>$0.00</span></li>"
                    }
                }
                $('#itemsInjectedHere').append(string);
                componentHandler.upgradeDom();
                $(".amount").on("change", function () {
                    var runningSubtotal = 0;
                    //calculate totals in step 2
                    for (var key in itemDataObj) {
                        var lineTotal = (parseInt($('#amount_' + key).val()) * parseFloat(itemDataObj[key]['quotedPricePerUnit']));
                        runningSubtotal += lineTotal;
                        $("#lineItemSubtotal_" + key).text("$" + lineTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
                        $("#subtotal_marked").text("$" + runningSubtotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
                        $("#step2checkbox").attr('checked', false).parent().show();
                    }
                });
            }
        });
    }

    function getVendorInfo() {
        //fill out business card
        $.getJSON('API/getSuppliers.php?supplier=' + activeVendor, function (data) {
            suppDataObj = data;
            $("#suppNameCard").text(" " + data['Name']);
            $("#suppContactNameCard").text(" " + data['ContactName']);
            $("#suppPhoneCard").text(" " + data['ContactPhone']).attr("href", "tel:" + data['ContactPhone']);
            $("#suppEmailCard").text(" " + data['ContactEmail']).attr("href", "mailto:" + data['ContactEmail']);
            $("#suppDateCard").text(" " + data['lastInteracted']);
            $("#itemDetailPanel").append($("#step2ItemList"));
            //attach event handlers to step 3 worksheet
            $(".amount").off("change").each(function () {
                $(this).attr("disabled", false).parent().children("span").remove();
                $(this).parent().append("<span> @ $</span><input required class='priceInput' type='number' id='priceInput_" + $(this).attr('id').substring(7) + "' step='any' style='width: 5em' value='" + itemDataObj[$(this).attr('id').substring(7)]['quotedPricePerUnit'] + "' placeholder='price'>").on("change", function (event) {
                    var runningSubtotal = 0;
                    //this mess here shows the save quote box if the price changes
                    if($(event.target).attr('id').indexOf('priceInput') == 0 && $(event.target).val() != itemDataObj[$(event.target).attr('id').substring(11)]['quotedPricePerUnit']){
                        $('#quoteCheckbox_'+$(event.target).attr('id').substring(11)).parent().fadeIn();
                    } else {
                        $('#quoteCheckbox_'+$(event.target).attr('id').substring(11)).attr('checked', false).parent().removeClass('is-checked').fadeOut();
                    }
                    //calculate the totals in step 3
                    for (var key in itemDataObj) {
                        var lineTotal = (parseInt($('#amount_' + key).val()) * parseFloat($('#priceInput_'+key).val()));
                        runningSubtotal += lineTotal;
                        $("#lineItemSubtotal_" + key).text("$" + lineTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
                        $("#subtotal_marked").text("$" + runningSubtotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    }
                });
            });
        });
    }

    function submitFinalData(){
        for(var item in itemDataObj){
            if(itemDataObj.hasOwnProperty(item)){
                itemDataObj[item]['pricePerUnit'] = $("#priceInput_"+item).val();
                itemDataObj[item]['changeQuote'] = $("#quoteCheckbox_"+item).is(':checked');
                itemDataObj[item]['quantityWanted'] = $("#amount_"+item).val();
                if (itemDataObj[item]['quantityWanted'] == 0 || itemDataObj[item]['quantityWanted'] == '' || isNaN(itemDataObj[item]['quantityWanted'])){
                    delete itemDataObj[item];
                }
            }
        }
        itemDataObj['SuppID'] = activeVendor;
        //delete from shopping cart
        sessionStorage.removeItem(activeVendor);
        $.post('API/orderSubmit.php', itemDataObj);
    }
</script>
</body>
</html>
