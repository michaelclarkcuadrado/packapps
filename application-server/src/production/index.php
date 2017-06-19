<?
require '../config.php';

//authentication
if ((!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) && !isset($_GET['displayLine'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey)) && !isset($_GET['displayLine'])) {
    die("<script>window.location.replace('/')</script>");
} else {
    if (isset($_GET['displayLine'])) {
        $RealName = array("UserRealName" => 'Display Board', "Role" => 'Restricted', "allowedProduction" => 1);
        $SecuredUserName = 'Display Board';
    } else {
        $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
        $checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `Real Name` as UserRealName, Role, allowedProduction FROM packapps_master_users JOIN production_UserData ON packapps_master_users.username=production_UserData.UserName WHERE packapps_master_users.username = '$SecuredUserName'"));
        if (!$checkAllowed['allowedProduction'] > 0) {
            die ("<script>window.location.replace('/')</script>");
        } else {
            $RealName = $checkAllowed;
        }
    }
}
// end authentication

require_once('scripts/Mobile_Detect.php');
$detect = new Mobile_Detect();
//stop IE from loading
if ($detect->is('IE')) {
    die("<div style='height: 100%;text-align: center; background-color: white;'><div style='top:20%;position:relative;font-size:25px'>Sorry, Internet Explorer is not supported. Try again with a newer browser, such as firefox or chrome.</div></div>");
}


//set cookies from params, for display boards
if (isset($_GET['displayLine'])) {
    setcookie('blue', "", time() - 3600);
    setcookie('gray', "", time() - 3600);
    setcookie('presizer', "", time() - 3600);
    setcookie($_GET['displayLine'], "true", time() + 788400000);
    setcookie('visited', "true", time() + 788400000);
    setcookie('__display', "true", time() + 788400000);
}
?>
<!--
Protip: putting ?displayLine=blue or ?displayLine=gray at the end of the url will activate a 10-foot view of the current runs, for Wide-area tv display boards
-->
<!doctype html>
<html lang="" <? echo($RealName['Role'] == 'Restricted' ? "style='zoom:140%'" : '') ?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production</title>

    <!-- Color the status bar on mobile devices -->
    <meta name="theme-color" content="#2F3BA2">

    <link rel="stylesheet" href="styles/material.min.css">

    <!-- Material Design icons -->
    <link rel="stylesheet" href="styles/materialIcons/material-icons.css">

    <!-- Your styles -->
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="scripts/themes/default/style.min.css"/>
</head>
<body class="mdl-demo mdl-color--grey-100 mdl-color-text--grey-700 mdl-base">
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
    <header <? echo($RealName['Role'] == 'Restricted' ? "style='display: none'" : '') ?>
        class="mdl-layout__header mdl-layout__header--scroll mdl-color--primary">
        <div class="mdl-layout--large-screen-only mdl-layout__header-row">
            <h3>
                Production Dashboard
                <small style='vertical-align:bottom; font-size: small'><? echo $companyName ?></small>
            </h3>
        </div>
        <div class="mdl-layout__tab-bar mdl-js-ripple-effect mdl-color--primary-dark">
            <a href="#curRunContent" class="mdl-layout__tab is-active ">Current runs</a>
            <a href="#Navigator" class="mdl-layout__tab">Inventory Explorer</a>
            <a href="#runHistory" class="mdl-layout__tab">Run history</a>
            <a href="#Settings" class="mdl-layout__tab">Settings</a>
            <a href="/" class="mdl-layout__tab">Main Menu</a>
        </div>
    </header>
    <?
    if ($RealName['Role'] == 'Production') {
        echo "<button class=\"mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored mdl-shadow--4dp mdl-color--accent\" style='z-index:99; position: fixed' id=\"add\" onclick=\"addNewBlockModal()\"><i class=\"material-icons\" role=\"presentation\">add</i><span class=\"visuallyhidden\">Add</span></button>";
    }
    ?>
    <main  id="scrollingpane" class="mdl-layout__content <? echo($RealName['Role'] == 'Restricted' ? (($_GET['displayLine'] == 'blue') ? "mdl-color--light-blue-300\"" : ($_GET['displayLine'] == 'gray' ? "mdl-color--grey-600\"" : '"')) : '"') ?>>
<? echo($RealName['Role'] == 'Restricted' ? "<div id='updatedFlasher' style='position: fixed; display: none; top: 0; min-height:20%; width: 100%;text-align: center' class='mdl-color--yellow-600 notify-blink'></div>" : '') ?>

        <!-- CURRENT RUN INFORMATION -->
        <div
            class=" mdl-layout__tab-panel
        is-active <? echo($RealName['Role'] == 'Restricted' ? (($_GET['displayLine'] == 'blue') ? "mdl-color--light-blue-300" : ($_GET['displayLine'] == 'gray' ? 'mdl-color--grey-600' : '')) : '') ?>
    "
    id="curRunContent">

    <table cellspacing="20" cellpadding="0" width="100%">
        <tr id="tableForRuns"></tr>
    </table>
</div>

<!-- HISTORY -->
<div <? echo($RealName['Role'] == 'Restricted' ? "style='display: none'" : '') ?> class="mdl-layout__tab-panel"
                                                                             id="runHistory">
    <h1 style="text-align: center">Run History</h1>
    <div style="margin: 50px" id="runCardsHere">
    </div>
    <br>
    <button style="margin: auto; display: block"
            class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
            onclick='loadMoreRunHistory();'>Load 10 Previous Runs
    </button>
    <h3 style="width: 100%; text-align: center">- OR -</h3>
    <div style="margin: auto; text-align: center; border-radius: 10px;" class="mdl-shadow--4dp mdl-cell--4-col">
        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is-dirty">
            <input onkeyup="if (event.keyCode == 13) {retrieveSingleRun()}" class="mdl-textfield__input" type="number" id="search">
            <label class="mdl-textfield__label" style='color:#3f51b5' for="search">Run Number Lookup</label>
        </div>
    </div>
    <br>
</div>

<!-- Inventory Navigator -->
<div <? echo($RealName['Role'] == 'Restricted' ? "style='display: none'" : '') ?> class="mdl-layout__tab-panel"
                                                                             id="Navigator">
    <h1 style="text-align: center">Inventory Explorer</h1>
    <section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--8dp">
        <div class="mdl-card mdl-cell--8-col mdl-cell--4-col-phone mdl-cell">
            <div class="mdl-card__supporting-text">
                <h4>Presized Inventory</h4>
                <div id="PStree"></div>
            </div>
            <? echo($RealName['Role'] == 'Production' ? "<div class=\"mdl-card__actions\"><a onclick=\"makeRun($('#PStree').jstree(true).get_selected(), 'PSOH');\" class=\"mdl-button\">Run selected items</a>|<a style='display:none' id='markAsBadButton' onclick=\"markBadRun($('#PStree').jstree(true).get_selected());\" class=\"mdl-button\">Toggle quality status</a></div>" : '') ?>
        </div>
    </section>
    <section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--16dp">
        <div class="mdl-card mdl-cell--8-col mdl-cell--4-col-phone mdl-cell">
            <div class="mdl-card__supporting-text">
                <h4>Bulk Inventory</h4>
                <div id="Bulktree"></div>
            </div>
            <? echo($RealName['Role'] == 'Production' ? "<div class=\"mdl-card__actions\"><a onclick=\"makeRun($('#Bulktree').jstree(true).get_selected(), 'BULKOH');\" class=\"mdl-button\">Run selected items</a></div>" : '') ?>
        </div>
    </section>
</div>

<!-- SETTINGS-->
<div <? echo($RealName['Role'] == 'Restricted' ? "style='display: none'" : '') ?> class="mdl-layout__tab-panel"
                                                                             id="Settings">
    <section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--16dp">
        <div class="mdl-card mdl-cell mdl-cell--12-col">
            <div class="mdl-card__supporting-text">
                <h4>Settings</h4>
                <h5>Current Run View</h5>
                <table width="100%" cellpadding="0">
                    <tr>
                        <td>
                            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="switch-1">
                                <input
                                    onchange="(this.checked ? (document.cookie = 'blue=blue;max-age=315360000'<? echo($detect->isMobile() ? ", $('#switch-2').attr('checked', false).change().parent().removeClass('is-checked'), $('#switch-3').attr('checked', false).change().parent().removeClass('is-checked'))" : ')') ?> : document.cookie = 'blue=; expires=Thu, 01 Jan 1970 00:00:00 UTC');lastRunData = {};loadRuns()"
                                    type="checkbox" id="switch-1" class="mdl-switch__input" checked>
                                <span class="mdl-switch__label"><? echo $Line1Name ?></span>
                            </label>
                        </td>
                        <td>
                            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="switch-2">
                                <input
                                    onchange="(this.checked ? (document.cookie = 'gray=gray;max-age=315360000'<? echo($detect->isMobile() ? ", $('#switch-1').attr('checked', false).change().parent().removeClass('is-checked'), $('#switch-3').attr('checked', false).change().parent().removeClass('is-checked'))" : ')') ?> : document.cookie = 'gray=; expires=Thu, 01 Jan 1970 00:00:00 UTC');lastRunData = {};loadRuns()"
                                    type="checkbox" id="switch-2"
                                    class="mdl-switch__input">
                                <span class="mdl-switch__label"><? echo $Line2Name ?></span>
                            </label>
                        </td>
                        <td>
                            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="switch-3">
                                <input
                                    onchange="(this.checked ? (document.cookie = 'presizer=presizer;max-age=315360000'<? echo($detect->isMobile() ? ", $('#switch-1').attr('checked', false).change().parent().removeClass('is-checked'), $('#switch-2').attr('checked', false).change().parent().removeClass('is-checked'))" : ')') ?> : document.cookie = 'presizer=; expires=Thu, 01 Jan 1970 00:00:00 UTC');lastRunData = {};loadRuns()"
                                    type="checkbox" id="switch-3" class="mdl-switch__input">
                                <span class="mdl-switch__label"><? echo $Line3Name ?></span>
                            </label></td>
                    </tr>
                </table>

            </div>
        </div>
    </section>
</div>
<div id="snackbar" class="mdl-js-snackbar mdl-snackbar">
    <div class="mdl-snackbar__text"></div>
    <button class="mdl-snackbar__action" type="button"></button>
</div>
</main>
</div>
<script src="scripts/material.min.js"></script>
<script src="scripts/main.js"></script>
<script src="scripts/jquery.min.js"></script>
<script src="scripts/jstree.min.js"></script>
<script language="JavaScript">

    //init page
    var runHistoryOffsetPos = 0;
    //blue = 0, gray = 1, presizer = 2
    var lastChatData = ['', '', ''];
    var blinkLineTitle = [false, false, false];
    var lastRunData = {};
    var updateFlasherTimeout = {};
    var version = 30;
    var scrollerInterval;
    var numMatchingRuns = 0;
    var curActiveCard = 1;
    var nextCollapseTimeout;
    $(document).ready(function () {
        setBlueLineDefault();
        loadRuns();
        createInventoryTrees();

    <?echo($RealName['Role'] != 'Restricted' ? "setInterval(refreshChat, 2000);" : '')?>

        var debug = setInterval(loadRuns, 7000);

        setInterval(createInventoryTrees, 45*60000);

        //request notify permissions
        if (Notification.permission !== 'denied') {
            Notification.requestPermission(function (permission) {
                sendNotification("You are now signed in to chat.", "Production Chat");
            })
        }

    });

    function createInventoryTrees() {
        $("#PStree").jstree('destroy').on("select_node.jstree", function (e, data) {
            if (data.node.a_attr.href != '#') {
                $('#markAsBadButton').show();
            }
            else {
                $('#markAsBadButton').hide();
            }
        }).jstree({
            'core': {
                'data': {
                    'url': 'API/PSOH.php',
                    'type': 'GET',
                    'dataType': 'JSON',
                    'data': function (node) {
                        return {'id': node.id};
                    }
                }
            }
        });
        $("#Bulktree").jstree('destroy').on("select_node.jstree", function (e, data) {
            if (data.node.a_attr.href != '#') {
                window.open(data.node.a_attr.href);
            }
        }).jstree({
            'core': {
                'data': {
                    'url': 'API/BULKOH.php',
                    'type': 'GET',
                    dataType: 'JSON',
                    'data': function (node) {
                        return {'id': node.id};
                    }
                }
            }
        });
    }

    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function ordinal_suffix_of(i) {
        var j = i % 10,
            k = i % 100;
        if (j == 1 && k != 11) {
            return i + "st";
        }
        if (j == 2 && k != 12) {
            return i + "nd";
        }
        if (j == 3 && k != 13) {
            return i + "rd";
        }
        return i + "th";
    }

    function generateRunCards(data, line) {
        var stringToReturn = "";
        numMatchingRuns = 0;
        //asdf is a magic string used to generate run cards for the history page
        if (line == 'asdf') {
            numMatchingRuns = runHistoryOffsetPos;
            runHistoryOffsetPos += 10;
        }
        for (var i = 0; i < data.length; i++) {
            if (line == data[i]['line'] || line == 'asdf' || line == 'singleRun') {
                numMatchingRuns++;
                stringToReturn = stringToReturn + "<section id='cardID" + numMatchingRuns + "' " + ((data[i]['isQA'] > 0 || data[i]['isPreInspected'] > 0) <?echo($RealName['Role'] == 'Restricted' ? '&& false' : '')?> ? "style='margin-top: 125px;'" : "style='margin-bottom: 20px;'") + "class=\"section--center mdl-grid runcard mdl-grid--no-spacing mdl-shadow--16dp\">" +

                    (data[i]['isQA'] > 0 <?echo($detect->isMobile() || $RealName['Role'] == 'Restricted' ? '&& false' : '')?> ? "<a onclick='viewQADetails(" + data[i]['RunID'] + ")'><div style='cursor: pointer;color: black; position: absolute; border-top-right-radius: 40%; border-top-left-radius: 40%; text-align: center; background-color: #8eff96; right:5%; bottom: 100%; z-index:3; padding-top: 20px; padding-left: 10px; padding-right:10px; padding-bottom: 5px' class='mdl-shadow--2dp'>QA Averages: <br> Pres: " + data[i]['QA']['Pressure'] + ", Brix: " + data[i]['QA']['Brix'] + ", Weight (lb): " + data[i]['QA']['Weight'] + "<br>" + data[i]['QA']['Note'] + "</div></a>" : '') +

                    (data[i]['isPreInspected'] > 0 <?echo($detect->isMobile() || $RealName['Role'] == 'Restricted' ? '&& false' : '')?> ? "<a onclick='viewQADetails(" + data[i]['RunID'] + ")'><div style='cursor: pointer;color: white; position: absolute; border-top-right-radius: 40%; border-top-left-radius: 40%; text-align: center; background-color:rgba(255, 64, 129, 0.85); left:5%; bottom: 100%; z-index:2; padding-top: 20px; padding-left: 10px; padding-right:10px; padding-bottom: 5px' class='mdl-shadow--2dp'>Pre-Inspection Averages: <br> Pres: " + data[i]['PreInspection']['Pressure'] + ", Brix: " + data[i]['PreInspection']['Brix'] + ", Weight (lb): " + data[i]['PreInspection']['Weight'] + "<br>" + data[i]['PreInspection']['Note'] + "</div></a>" : '') +

                    (numMatchingRuns == 1 && line != 'asdf' && line != 'singleRun' <?echo($RealName['Role'] == 'Restricted' ? '&& false' : '')?> ? "<header style='display: block' class=\"smdl-cell mdl-cell--3-col-desktop mdl-cell--2-col-tablet mdl-cell--4-col-phone mdl-color--teal-100 mdl-color-text--white\"><h4 style='text-align: center; color: #616161'>Chat</h4><textarea readonly id='" + line + "chat' style='width:98%; opacity:.6; filter: alpha(opacity=60);' rows='20'></textarea><div style='width: 98%;color:black; margin: 2%' class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'><input id='" + line + "message' class='mdl-textfield__input' size='20' type='text'><label class='mdl-textfield__label' for='" + line + "message'>Message</label></div>" : "<header <?echo($RealName['Role'] == 'Restricted' ? "style='display: none;'" : "")?> class=\"smdl-cell mdl-cell--3-col-desktop mdl-cell--2-col-tablet mdl-cell--4-col-phone mdl-color--teal-100 mdl-color-text--white\"><h1 style='text-align:center; color: #616161'>" + ordinal_suffix_of(numMatchingRuns) + " Run</h1></header>") + "</header> <div <?echo($RealName['Role'] == 'Restricted' ? "style='width: 100%; font-family: Overpass'" : '')?> class=\"mdl-card mdl-cell mdl-cell--9-col-desktop mdl-cell--6-col-tablet mdl-cell--4-col-phone\"><div style=\"margin-right: 0\" class=\"mdl-card__supporting-text\">" +
                    "<h4 <?echo($RealName['Role'] == 'Restricted' ? "style='font-size: 300%;line-height:70px'" : '')?>>" + (numMatchingRuns == 1 && line != 'asdf'&& line != 'singleRun' ? "Current Run: " : (numMatchingRuns == 2 && line != 'asdf' && line != 'singleRun' ? "<mark>Next Run:</mark> " : (line != 'asdf' && line != 'singleRun' ?  "<mark>" + ordinal_suffix_of(numMatchingRuns) + " Run:</mark> " : 'Run Number: '))) + data[i]['RunNumber'] + "</h4><B>Dumping</B><ul class=\"mdl-list\">";

                //dumped fruit
                for (var j = 0; j < data[i]['dumpedArray'].length; j++) {
                    stringToReturn += "<li " + (data[i]['dumpedArray'][j][7] == '1' ? "style='background-color: rgb(255, 153, 144); padding:0; <?echo($RealName['Role'] == 'Restricted' ? "line-height: 65px" : '')?>'" : "style='padding: 0; <?echo($RealName['Role'] == 'Restricted' ? "line-height: 65px" : '')?>'") + " class=\"mdl-list__item\"><span <?echo($RealName['Role'] == 'Restricted' ? "style='word-wrap: break-word; width: 100%; margin: 0; font-size: 255%;font-family: Overpass'" : '')?> class=\"mdl-list__item-primary-content\"><i class=\"material-icons mdl-list__item-icon\">" + (data[i]['dumpedArray'][j][7] == '1' ? "close" : "chevron_right") + "</i>" +
                        (data[i]['dumpedArray'][j][0] + data[i]['dumpedArray'][j][1] + data[i]['dumpedArray'][j][2] + data[i]['dumpedArray'][j][3]) + data[i]['dumpedArray'][j][4] + data[i]['dumpedArray'][j][5] +
                        ((data[i]['dumpedArray'][j][6]) > 0 || (data[i]['dumpedArray'][j][6]) == -1 ? "<span style='margin-left:5px;padding:4px;background-color:#ff4081;color:white;font-weight:600;font-size:larger; border-radius:5px'>"+(data[i]['dumpedArray'][j][6] == '-1' ? 'RUN OUT' : data[i]['dumpedArray'][j][6])+"</span>" : '')
                        "</span></li>";
                }
                stringToReturn += "</ul><b>Into</b><ul class=\"mdl-list\">";

                //product made
                for (j = 0; j < data[i]['madeArray'].length; j++) {
                    stringToReturn += " <li style='padding: 0; <?echo($RealName['Role'] == 'Restricted' ? "line-height: 65px" : '')?>' class=\"mdl-list__item\"><span <?echo($RealName['Role'] == 'Restricted' ? "style='margin:0; word-wrap: break-word; font-size: 255%; font-family: Overpass';" : '')?> class=\"mdl-list__item-primary-content\"><i class=\"material-icons mdl-list__item-icon\">chevron_right</i>" +
                        data[i]['madeArray'][j][0] + ' | ' + data[i]['madeArray'][j][2] + (data[i]['madeArray'][j][1] > 0 || data[i]['madeArray'][j][1].charAt(0) == '(' ? "<span style='margin-left:5px;padding:4px;background-color:#ff4081;color:white;font-weight:600;font-size:larger; border-radius:5px'>" + data[i]['madeArray'][j][1] + "</span>" : '') + "</span></li>";
                }
                stringToReturn += "</ul>Updated on " + data[i]['lastEdited'] + "</div>" + (numMatchingRuns == 1 && line != 'asdf' && line != 'singleRun' <?echo($RealName['Role'] != 'Production' ? "&& 1 == 2" : "")?>? "<div class=\"mdl-card__actions\"><a href=\"#\" onclick='$.get(\"manageRun.php\", {finish: " + data[i]['RunID'] + "});snack(\"One moment, please. Finishing run...\", 3000);$(this).parent().parent().parent().slideUp();' class=\"mdl-button\">FINISH AND LOAD NEXT RUN</a></div>" : '') + "</div><button class=\"mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon\" id=\"" + data[i]['RunID'] + "\"><i class=\"material-icons\">more_vert</i></button><ul class=\"mdl-menu mdl-js-menu mdl-menu--bottom-right\" for=\"" + data[i]['RunID'] + "\"><li onclick='window.open(\"editRun.php?run=" + data[i]['RunID'] + "\", \"Edit Run\", \"scrollbars=1,height=700,width=1050\");' class=\"mdl-menu__item\">Edit</li><li onclick='window.open(\"editRun.php?run=" + data[i]['RunID'] + "&duplicate=1\", \"Edit Run\", \"scrollbars=1,height=700,width=1050\");' class=\"mdl-menu__item\">Copy into new run</li><li onclick='$.get(\"manageRun.php\", {delete: " + data[i]['RunID'] + "});snack(\"One moment... Deleting Run.\", 3000);$(this).parent().parent().parent().slideUp();' class=\"mdl-menu__item\">Delete</li>" + (line == 'asdf' || line == 'singleRun' ? "<li onclick='$.get(\"manageRun.php\", {finish: " + data[i]['RunID'] + "});snack(\"One moment, please. Restoring run...\", 3000);$(this).parent().parent().parent().slideUp();' class=\"mdl-menu__item\">Unfinish run</li>" : '') + "</ul></section>";
            }
        }
        if (numMatchingRuns == 0) {
            if (data['refreshpl0x'] > 0) {
                stringToReturn = "<center><h1>Updating Production Coordinator. Please wait...</h1></center>";
            } else {
                stringToReturn = <?echo($RealName['Role'] == 'Restricted' ? "\"<img src='splash.jpg' style='position: fixed; top: 0; bottom: 0; left: 0; right: 0; max-width: 100%; max-height: 100%; margin: auto; overflow: auto'>\"" : "\"<center>No active runs on this line.</center>\"")?>;
            }
        }
        return stringToReturn;
    }

    function loadRuns() {
        $.ajax({
            type: 'GET',
            url: "API/curRuns.php",
            dataType: 'json',
            cache: false,
            success: function (data) {
                //new data, generate cards and everything
                if (JSON.stringify(data) != JSON.stringify(lastRunData)) {
                    //if it isn't the first refresh and there's a change, flash the screen for 5 minutes
                    if (!jQuery.isEmptyObject(lastRunData)) {
                        $('#updatedFlasher').show();
                        clearTimeout(updateFlasherTimeout);
                        updateFlasherTimeout = setTimeout(function () {
                            $('#updatedFlasher').hide();
                        }, 300000);
                    }
                    lastRunData = data;

                    //the self-updater
                    if (data['refreshpl0x'] > version) {
                        location.reload(true);
                    }

                    //save page state
                    clearTimeout(nextCollapseTimeout);
                    if (readCookie('blue') != null) {
                        var blueval = $("#Bluemessage").val();
                    }
                    if (readCookie('gray') != null) {
                        var grayval = $("#Graymessage").val();
                    }
                    if (readCookie('presizer') != null) {
                        var presizerval = $("#Presizermessage").val();
                    }
                    var focused = $(':focus').attr('id');

                    //generate all the run cards
                    $("#tableForRuns").html(
                        (readCookie('blue') != null ? "<td style='vertical-align: top'><h1 id='title0' " + (blinkLineTitle[0] ? "class='notify-blink'" : '') + " style=\"text-align: center; <?echo($RealName['Role'] == 'Restricted' ? "display: none" : '')?>\"><?echo $Line1Name?></h1>" + generateRunCards(data, "Blue") + "<section></section></td>" : '') +
                        (readCookie('gray') != null ? "<td style='vertical-align: top'><h1 id='title1' " + (blinkLineTitle[1] ? "class='notify-blink'" : '') + " style=\"text-align: center; <?echo($RealName['Role'] == 'Restricted' ? "display: none" : '')?>\"><?echo $Line2Name?></h1>" + generateRunCards(data, 'Gray') + "<section></section></td>" : '') +
                        (readCookie('presizer') != null ? "<td style='vertical-align: top'><h1 id='title2' " + (blinkLineTitle[2] ? "class='notify-blink'" : '') + " style=\"text-align: center; <?echo($RealName['Role'] == 'Restricted' ? "display: none" : '')?>\"><?echo $Line3Name?></h1>" + generateRunCards(data, 'Presizer') + "<section></section></td>" : '')
                    );

                    if (readCookie('blue') == null && readCookie('gray') == null && readCookie('presizer') == null) {
                        $("#tableForRuns").html("<h3>Hmm... There's nothing here...</h3>");
                    }

                    //restore page state
                    if (readCookie('blue') != null) {
                        $("#Bluemessage").val(blueval).keypress(function (event) {
                            if (event.keyCode == 13) {
                                sendMessage('Blue', $(this).val());
                            }
                        });
                    }
                    if (readCookie('gray') != null) {
                        $("#Graymessage").val(grayval).keypress(function (event) {
                            if (event.keyCode == 13) {
                                sendMessage('Gray', $(this).val());
                            }
                        });
                    }
                    if (readCookie('presizer') != null) {
                        $("#Presizermessage").val(presizerval).keypress(function (event) {
                            if (event.keyCode == 13) {
                                sendMessage('Presizer', $(this).val());
                            }
                        });
                    }
                    //restart animation
                    curActiveCard = 1;
                    //start auto scroll if display view
                    <?echo($RealName['Role'] == 'Restricted' ? "$(\"#cardID1\").show();animateScroll('down');var nextCollapseTimeout = setTimeout(function() {collapseCards()}, 12000);" : '')?>

                <?echo($RealName['Role'] != 'Restricted' ? "refreshChat();" : '')?>
                    componentHandler.upgradeDom();
                    //don't interrupt chat typing during refresh
                    if (focused != 'undefined') {
                        try {
                            $("#" + focused).focus().setCursorPosition($("#" + focused).val().length);
                        }
                        catch (e) {
                        }
                    }
                }
            },
            error: function () {
                lastRunData = {};
                $("#tableForRuns").html("<td><h3>Could not load runs. Please wait for reconnection to server...</h3></td>");
                snack("Couldn't load live runs.", 2500);
            }
        });
    }

    function loadMoreRunHistory() {
        $.ajax({
            type: 'GET',
            url: "API/runHistory.php?runOffset=" + runHistoryOffsetPos,
            dataType: 'json',
            cache: false,
            success: function (data) {
                if(runHistoryOffsetPos == 0) {
                    $('#runCardsHere').html('');
                }
                $("<div style='display:none'>" + generateRunCards(data, 'asdf') + "<section></section></div>").appendTo('#runCardsHere').fadeIn();
                componentHandler.upgradeDom();
            }
        });
    }

    function retrieveSingleRun() {
        $.getJSON('API/runHistory.php?RunNum=' + $('#search').val(), function(data) {
            runHistoryOffsetPos = 0;
            $('#runCardsHere').html("<div style='display:none'>" + generateRunCards(data, 'singleRun') + "<section></section></div>").children().fadeIn();
            componentHandler.upgradeDom();
        });
    }

    function snack(message, length) {
        var data = {
            message: message,
            timeout: length
        };
        document.querySelector('#snackbar').MaterialSnackbar.showSnackbar(data);
    }

    //rotate through card deck on display screens
    function collapseCards(){
        //ensure consistency
        clearTimeout(nextCollapseTimeout);
        if(lastRunData != {}){
            if(numMatchingRuns == 1){
                return;
            } else {
                //reset scrolling
                clearInterval(scrollerInterval);
                $("#scrollingpane").scrollTop(0);
                //hide card, show next one
                $("#cardID" + curActiveCard).hide(0, function () {
                    var millisecondsToHold;
                    if (curActiveCard >= numMatchingRuns) {
                        curActiveCard = 1;
                        millisecondsToHold = 17000;
                    } else {
                        millisecondsToHold = 10000;
                        if (curActiveCard == 2){
                            millisecondsToHold = 8000;
                        }
                        curActiveCard++;
                    }
                    $("#cardID"+curActiveCard).show(0, function () {
                        animateScroll('down');
                        nextCollapseTimeout = setTimeout(function () {
                            collapseCards();
                        }, millisecondsToHold);
                    });
                });
            }
        }
    }

    function animateScroll(direction){
       var content = $("#scrollingpane");
       clearInterval(scrollerInterval);
       console.log("scroll");
        if(direction == 'down'){
            scrollerInterval = setInterval(function(){
                content.scrollTop(content.scrollTop() + 1);
                if (content[0].scrollHeight - content.scrollTop() <= content.outerHeight()){
                    clearInterval(scrollerInterval);
                    scrollerpendingtimeout = setTimeout(function(){
                        animateScroll('up');
                    }, 2000);
                }
            }, 20);
        } else if(direction == 'up') {
            scrollerInterval = setInterval(function(){
                content.scrollTop(content.scrollTop() - 1);
                if (content.scrollTop() <= 0){
                    clearInterval(scrollerInterval);
                    scrollerpendingtimeout = setTimeout(function(){
                        animateScroll('down');
                    }, 2000);
                }
            }, 20);
        }
    }

    function sendNotification(title, text) {
        if ("Notification" in window) {
            if (Notification.permission === "granted") {
                var notification = new Notification(title, {
                    icon: 'apple-touch-icon.png',
                    body: text
                });
            }
            else if (Notification.permission !== 'denied') {
                Notification.requestPermission(function (permission) {
                    if (permission === "granted") {
                        var notification = new Notification(title, {
                            icon: 'apple-touch-icon.png',
                            body: text
                        });
                    }
                });
            } else {
                snack(text, 10000);
            }
        }
    }

    function sendMessage(line, message) {
        $.post("chat_backend.php", {line: line, message: message}, function () {
            $("#" + line + "message").val("");
        })
    }

    function refreshChat() {
        if (readCookie('blue') != null) {
            $.post("chat_backend.php", {line: "blue"}, function (data) {
                checkIfPinged(data, 0);
                $("#Bluechat").val(data).scrollTop(($('#Bluechat').length ? $('#Bluechat')[0].scrollHeight : 0));
            });
        }
        if (readCookie('gray') != null) {
            $.post("chat_backend.php", {line: "gray"}, function (data) {
                checkIfPinged(data, 1);
                $("#Graychat").val(data).scrollTop(($('#Graychat').length ? $('#Graychat')[0].scrollHeight : 0));
            });
        }
        if (readCookie('presizer') != null) {
            $.post("chat_backend.php", {line: "presizer"}, function (data) {
                checkIfPinged(data, 2);
                $("#Presizerchat").val(data).scrollTop(($('#Presizerchat').length ? $('#Presizerchat')[0].scrollHeight : 0));
            });
        }
    }

    function checkIfPinged(data, linenumber) {
        if (lastChatData[linenumber] != data)
            var username = '<?echo $SecuredUserName?>';
        var pos = data.lastIndexOf('@' + username);
        var pos2 = data.lastIndexOf(username + ':');
        if (pos >= 0 && pos > pos2) {
            if (true <?echo($detect->isMobile() ? '&& false' : '')?>) {
                sendNotification('New Message on Production Chat', data.substr(data.substr(0, pos).lastIndexOf("\n") + 1, data.substr(pos2).lastIndexOf("\n") + pos2 - 1));
            } else {
                navigator.vibrate(500);
            }
            blinkLineTitle[linenumber] = true;
            $('#title' + linenumber).addClass('notify-blink');
        } else if (pos < pos2) {
            blinkLineTitle[linenumber] = false;
            $('#title' + linenumber).removeClass('notify-blink');
        }
        lastChatData[linenumber] = data;
    }

    function setBlueLineDefault() {
        if (readCookie('visited') == null) {
            document.cookie = 'visited=true;max-age=315360000';
            document.cookie = 'blue=blue;max-age=315360000';
        }
        else {
            //set switches to correct states
            if (readCookie('blue') != null) {
                $("#switch-1").attr('checked', true)
            }
            else {
                $("#switch-1").attr('checked', false);
            }

            if (readCookie('gray') != null) {
                $("#switch-2").attr('checked', true);
            }
            else {
                $("#switch-2").attr('checked', false);
            }

            if (readCookie('presizer') != null) {
                $("#switch-3").attr('checked', true);
            }
            else {
                $("#switch-3").attr('checked', false);
            }
        }
    }

    function addNewBlockModal() {
        var newwindow = window.open("newRun.php", 'New Run', 'scrollbars=1,height=700,width=1050');
        if (window.focus) {
            newwindow.focus()
        }
        return false;
    }

    function viewQADetails(runNum) {
        var newwindow = window.open("runQADrillDown.php?q=" + runNum, 'View QA Details', 'scrollbars=1,height=800,width=425');
        if (window.focus) {
            newwindow.focus()
        }
        return false;
    }

    $.fn.setCursorPosition = function (pos) {
        this.each(function (index, elem) {
            if (elem.setSelectionRange) {
                elem.setSelectionRange(pos, pos);
            } else if (elem.createTextRange) {
                var range = elem.createTextRange();
                range.collapse(true);
                range.moveEnd('character', pos);
                range.moveStart('character', pos);
                range.select();
            }
        });
        return this;
    };

    function makeRun(selected, type) {
        $.ajax({
            type: 'POST',
            url: 'API/runFromJson' + type + '.php',
            data: {array: JSON.stringify(selected)},
            dataType: 'json',
            cache: false,
            success: function (data) {
                var newwindow = window.open("newRun.php?autofill=" + data[0], 'New Run', 'scrollbars=1,height=700,width=900');
                if (window.focus) {
                    newwindow.focus()
                }
                return false;
            },
            error: function (a, b, c) {
                snack("Could not send info. The error was: " + c, 3000);
            }
        });
    }

    function markBadRun(selected) {
        //make sure they don't sneak in anything but runs
        var isAllRuns = true;
        for (var i = 0; i < selected.length; i++) {
            var selectedArr = selected[i].split(':');
            if (selectedArr[selectedArr.length - 2] != 'Run') {
                isAllRuns = false;
                break;
            }
        }

        if (isAllRuns) {
            for (var j = 0; j < selected.length; j++) {
                selectedArr = selected[j].split(':');
                $.ajax({
                    type: 'POST',
                    url: 'API/markRunAsBad.php?',
                    data: {Run: selectedArr[(selectedArr.length) - 1]},
                    cache: false,
                    success: function (data) {

                    },
                    error: function (a, b, c) {
                        snack("Could not mark. The error was: " + c, 3000);
                    }
                });
            }
            $("#PStree").jstree("refresh");
        } else {
            snack("Sorry, only runs can be marked as bad.", 4500);
        }
    }

</script>

</body>
</html>
