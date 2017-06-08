<?
include '../config.php';
//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `Real Name` as UserRealName, Role, allowedProduction FROM packapps_master_users JOIN production_UserData ON packapps_master_users.username=production_UserData.UserName WHERE packapps_master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedProduction'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
    }
}
if ($RealName['Role'] != 'Production') {
    echo "<script>window.close()</script>";
    die();
}
// end authentication
?>
<!doctype html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production</title>

    <!-- Disable tap highlight on IE -->
    <meta name="msapplication-tap-highlight" content="no">

    <!-- Web Application Manifest -->
    <link rel="manifest" href="manifest.json">

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Production">
    <link rel="icon" sizes="192x192" href="images/touch/chrome-touch-icon-192x192.png">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="PackApps">
    <link rel="apple-touch-icon" href="images/touch/apple-touch-icon.png">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#2F3BA2">

    <!-- Color the status bar on mobile devices -->
    <meta name="theme-color" content="#2F3BA2">

    <link rel="stylesheet" href="styles/material.min.css">

    <!-- Material Design icons -->
    <link rel="stylesheet" href="styles/materialIcons/material-icons.css">

    <!-- Your styles -->
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
<form method="post" action="editedRunSubmit.php">
    <input type="hidden" name="RunID" id="RunID" value="">
    <input type="hidden" name="isQA" id="isQA" value="">
    <input type="hidden" name="isPreInspected" id="isPreInspected" value="">
    <input type="hidden" name="Line" id="Line" value="">
    <h4 class="mdl-dialog__title"><? echo(isset($_GET['duplicate']) ? 'Create New Duplicate Run' : 'Edit Run') ?></h4>
    <div class="mdl-dialog__content">
        <table cellspacing="0" width="100%">
            <tr>
                <td colspan="3">
                    <div style='width:6em' class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" name="runNum"
                               id="runNum">
                        <label class="mdl-textfield__label" for="runNum">Run Number</label>
                        <span class="mdl-textfield__error">Numbers, please.</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3"><h4>What should we dump on this run?</h4></td>
            </tr>
            <tr>
                <td colspan="3">
                    <div>
                        <table class="mdl-data-table mdl-js-data-table mdl-shadow--4dp">
                            <thead>
                            <tr>
                                <th class="mdl-data-table__cell--non-numeric">NOT</th>
                                <th class="mdl-data-table__cell--non-numeric">Grower</th>
                                <th class="mdl-data-table__cell--non-numeric">Variety</th>
                                <th class="mdl-data-table__cell--non-numeric">Grade or Farm</th>
                                <th class="mdl-data-table__cell--non-numeric">Size or Block</th>
                                <th class="mdl-data-table__cell--non-numeric">Lot</th>
                                <th class="mdl-data-table__cell--non-numeric">Location</th>
                                <th class="mdl-data-table__cell--non-numeric">Amount</th>
                            </tr>
                            </thead>
                            <tbody id="dumpedTbody">
                            <tr id="dumpedRow1">
                                <td id="tdNot1">
                                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="Not1">
                                        <input
                                            onClick="(this.checked ? $('#tdNot1').css('background-color', '#FF9990') : $('#tdNot1').css('background-color', '#FFFFFF'))"
                                            type="checkbox" value="1" name="Not1" id="Not1" class="mdl-checkbox__input">
                                    </label>
                                </td>
                                <td class="mdl-data-table__cell--non-numeric">
                                    <div style='width:7.5em'
                                         class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input size='2' maxlength="2" class="mdl-textfield__input"
                                               type="text"
                                               name="growerCode1"
                                               id="growerCode1">
                                        <label class="mdl-textfield__label" for="growerCode1">Grower</label>
                                    </div>
                                </td>
                                <td class="mdl-data-table__cell--non-numeric">
                                    <div style='width:6.5em'
                                         class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input size='10' maxlength="255" class="mdl-textfield__input"
                                               type="text" name="Variety1" id="Variety1">
                                        <label class="mdl-textfield__label" for="Variety1">Variety</label>
                                    </div>
                                </td>
                                <td class="mdl-data-table__cell--non-numeric">
                                    <div style='width:4.5em'
                                         class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input size='10' maxlength="255" class="mdl-textfield__input"
                                               type="text" name="Quality1" id="Quality1">
                                        <label class="mdl-textfield__label" for="Quality1">Grade</label>
                                    </div>
                                </td>
                                <td class="mdl-data-table__cell--non-numeric">
                                    <div style='width:4.5em'
                                         class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input size='10' maxlength="255" class="mdl-textfield__input"
                                               type="text" name="Size1" id="Size1">
                                        <label class="mdl-textfield__label" for="Size1">Size</label>
                                    </div>
                                </td>
                                <td class="mdl-data-table__cell--non-numeric">
                                    <div style='width:3.5em'
                                         class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input size='10' maxlength="255" class="mdl-textfield__input"
                                               type="text" name="Lot1" id="Lot1">
                                        <label class="mdl-textfield__label" for="Lot1">Lot</label>
                                    </div>
                                </td>
                                <td class="mdl-data-table__cell--non-numeric">
                                    <div style='width:7em'
                                         class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input size='10' maxlength="255" class="mdl-textfield__input"
                                               type="text" name="Location1" id="Location1">
                                        <label class="mdl-textfield__label" for="Location1">Location</label>
                                    </div>
                                </td>
                                <td>
                                    <div style='width:5em'
                                         class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input" type="text"
                                               pattern="-?[0-9]*(\.[0-9]+)?" name="Amount1" id="Amount1">
                                        <label class="mdl-textfield__label" for="Amount1">Amount</label>
                                        <span class="mdl-textfield__error">Numbers, please.</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div align="left">
                        <br>
                        <button
                            type="button"
                            class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
                            onclick="addDumpedRow();">
                            New Row
                        </button>
                        <button
                            type="button"
                            class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
                            onclick="removeDumpedRow();">
                            Remove Row
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3"><h4>What are we making?</h4></td>
            </tr>
            <tr>
                <td colspan="3">
                    <div>
                        <table class="mdl-data-table mdl-js-data-table mdl-shadow--4dp">
                            <thead>
                            <tr>
                                <th class="mdl-data-table__cell--non-numeric">Product</th>
                                <th class="mdl-data-table__cell--non-numeric">Pack Size</th>
                                <th class="mdl-data-table__cell--non-numeric">Amount</th>
                                <th class="mdl-data-table__cell--non-numeric">Boxes</th>
                            </tr>
                            </thead>
                            <tbody id="madeTbody">
                            <tr id="madeRow1">
                                <td class="mdl-data-table__cell--non-numeric">
                                    <div style='width:10em'
                                         class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input" type="text" name="productMade1"
                                               id="productMade1">
                                        <label class="mdl-textfield__label" for="productMade1">Product</label>
                                    </div>
                                </td>
                                <td class="mdl-data-table__cell">
                                    <div style='width:5em'
                                         class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input autocomplete="off" size='10' maxlength="255"
                                               class="mdl-textfield__input" type="text" name="packSize1"
                                               id="packSize1">
                                        <label class="mdl-textfield__label" for="packSize1">Pack Size</label>
                                    </div>
                                </td>
                                <td class="mdl-data-table__cell">
                                    <div style='width:5em'
                                         class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input size='10' maxlength="255" class="mdl-textfield__input" type="text"
                                               pattern="-?[0-9]*(\.[0-9]+)?" name="madeAmount1" id="madeAmount1">
                                        <label class="mdl-textfield__label" for="madeAmount1">Amount</label>
                                        <span class="mdl-textfield__error">Numbers, please.</span>
                                    </div>
                                </td>
                                <td>
                                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="amountType1">
                                        <input type="checkbox" value="1" name="amountType1" id="amountType1"
                                               class="mdl-checkbox__input">
                                    </label>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div align="left">
                        <br>
                        <button
                            type="button"
                            class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
                            onclick="addMadeRow();">
                            New Row
                        </button>
                        <button
                            type="button"
                            class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
                            onclick="removeMadeRow();">
                            Remove Row
                        </button>
                    </div>
                </td>
            </tr>
        </table>
        <div class="mdl-dialog__actions">
            <button class="mdl-button mdl-button--raised">Add To Schedule</button>
            <button type="button" class="mdl-button mdl-button--raised close" onclick="window.close()">Cancel</button>
        </div>
</form>
</body>
<script src="scripts/material.min.js"></script>
<script src="scripts/main.js"></script>
<script src="scripts/jquery.min.js"></script>
<script language="JavaScript">
    var amountMadeRows = 1;
    var amountDumpedRows = 1;

    loadRunInfo();

    function loadRunInfo() {
        $.ajax({
            type: 'GET',
            url: "API/singleRun.php?Run=<?echo $_GET['run'] . (isset($_GET['duplicate']) ? '&duplicate=1' : '')?>",
            dataType: 'json',
            cache: false,
            success: function (data) {
                $("#Line").val(data[1]);
                if (data[6] != '') {
                    $("#runNum").val(data[6]).parent().addClass('is-dirty');
                }
                $('#isQA').val(data[7]);
                $('#isPreInspected').val(data[8]);
                $('#RunID').val(data[0]);
                for (var i = 1; i <= data[2]; i++) {
                    if (i > 1) {
                        addDumpedRow();
                    }
                    if (data[4][i - 1][7] == 1) {
                        $("#Not" + i).attr('checked', true).parent().addClass('is-checked').parent().css('background-color', 'rgb(255, 153, 144)');

                    }
                    $("#growerCode" + i).val(data[4][i - 1][0]).parent().addClass('is-dirty');
                    $("#Variety" + i).val(data[4][i - 1][1]).parent().addClass('is-dirty');
                    $("#Quality" + i).val(data[4][i - 1][2]).parent().addClass('is-dirty');
                    $("#Size" + i).val(data[4][i - 1][3]).parent().addClass('is-dirty');
                    $("#Lot" + i).val(data[4][i - 1][4]).parent().addClass('is-dirty');
                    $("#Location" + i).val(data[4][i - 1][5]).parent().addClass('is-dirty');
                    $("#Amount" + i).val(data[4][i - 1][6]).parent().addClass('is-dirty');
                }

                for (i = 1; i <= data[3]; i++) {
                    if (i > 1) {
                        addMadeRow();
                    }
                    $("#productMade" + i).val(data[5][i - 1][0]).parent().addClass('is-dirty');
                    $("#packSize" + i).val(data[5][i - 1][2]).parent().addClass('is-dirty');
                    $("#madeAmount" + i).val(data[5][i - 1][1]).parent().addClass('is-dirty');
                    if (data[5][i - 1][3] == 1) {
                        $("#amountType" + i).attr('checked', true).parent().addClass('is-checked')
                    }
                }
            }
        })
    }

    function addDumpedRow() {
        amountDumpedRows++;
        $("<tr id=\"dumpedRow" + amountDumpedRows + "\"><td id=\"tdNot" + amountDumpedRows + "\"><label class=\"mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect\" for=\"Not" + amountDumpedRows + "\"><input onClick=\"(this.checked ? $('#tdNot" + amountDumpedRows + "').css('background-color', '#FF9990') : $('#tdNot" + amountDumpedRows + "').css('background-color', '#FFFFFF'))\" type=\"checkbox\" value=\"1\" name=\"Not" + amountDumpedRows + "\" id=\"Not" + amountDumpedRows + "\" class=\"mdl-checkbox__input\"></label></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:7.5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='2' maxlength=\"2\" class=\"mdl-textfield__input\" type=\"text\" name=\"growerCode" + amountDumpedRows + "\" id=\"growerCode" + amountDumpedRows + "\"><label class=\"mdl-textfield__label\" for=\"growerCode" + amountDumpedRows + "\">Grower</label></div></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:6.5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='10' maxlength=\"255\" class=\"mdl-textfield__input\" type=\"text\" name=\"Variety" + amountDumpedRows + "\" id=\"Variety" + amountDumpedRows + "\"><label class=\"mdl-textfield__label\" for=\"Variety" + amountDumpedRows + "\">Variety</label></div></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:4.5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='10' maxlength=\"255\" class=\"mdl-textfield__input\" type=\"text\" name=\"Quality" + amountDumpedRows + "\" id=\"Quality" + amountDumpedRows + "\"><label class=\"mdl-textfield__label\" for=\"Quality" + amountDumpedRows + "\">Grade</label></div></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:4.5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='10' maxlength=\"255\" class=\"mdl-textfield__input\" type=\"text\" name=\"Size" + amountDumpedRows + "\" id=\"Size" + amountDumpedRows + "\"><label class=\"mdl-textfield__label\" for=\"Size" + amountDumpedRows + "\">Size</label></div></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:3.5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='10' maxlength=\"255\" class=\"mdl-textfield__input\" type=\"text\" name=\"Lot" + amountDumpedRows + "\" id=\"Lot" + amountDumpedRows + "\"><label class=\"mdl-textfield__label\" for=\"Lot" + amountDumpedRows + "\">Lot</label></div></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:7em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='10' maxlength=\"255\" class=\"mdl-textfield__input\" type=\"text\" name=\"Location" + amountDumpedRows + "\" id=\"Location" + amountDumpedRows + "\"><label class=\"mdl-textfield__label\" for=\"Location" + amountDumpedRows + "\">Location</label></div></td><td><div style='width :5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input class=\"mdl-textfield__input\" type=\"text\" pattern=\"-?[0-9]*(\\.[0-9]+)?\" name=\"Amount" + amountDumpedRows + "\" id=\"Amount" + amountDumpedRows + "\"><label class=\"mdl-textfield__label\" for=\"Amount" + amountDumpedRows + "\">Amount</label><span class=\"mdl-textfield__error\">Numbers, please.</span></div></td></tr>").appendTo("#dumpedTbody");
        componentHandler.upgradeDom();
    }

    function removeDumpedRow() {
        if (amountDumpedRows > 1) {
            $("#dumpedRow" + amountDumpedRows).remove();
            amountDumpedRows--;
        }
    }

    function addMadeRow() {
        amountMadeRows++;
        $("<tr id=\"madeRow" + amountMadeRows + "\"><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:10em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input class=\"mdl-textfield__input\" type=\"text\" name=\"productMade" + amountMadeRows + "\" id=\"productMade" + amountMadeRows + "\"><label class=\"mdl-textfield__label\" for=\"productMade" + amountMadeRows + "\">Product</label></div></td>    <td class=\"mdl-data-table__cell\"> <div style='width:5em'class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"> <input autocomplete=\"off\" size='10' maxlength=\"255\"class=\"mdl-textfield__input\" type=\"text\" name=\"packSize" + amountMadeRows + "\"id=\"packSize" + amountMadeRows + "\"> <label class=\"mdl-textfield__label\" for=\"packSize" + amountMadeRows + "\">Pack Size</label> </div></td> <td class=\"mdl-data-table__cell\"><div style='width:5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='10' maxlength=\"255\" class=\"mdl-textfield__input\" type=\"text\" pattern=\"-?[0-9]*(\\.[0-9]+)?\" name=\"madeAmount" + amountMadeRows + "\" id=\"madeAmount" + amountMadeRows + "\"><label class=\"mdl-textfield__label\" for=\"madeAmount" + amountMadeRows + "\">Amount</label><span class=\"mdl-textfield__error\">Numbers, please.</span></div></td><td><label class=\"mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect\" for=\"amountType" + amountMadeRows + "\"><input type=\"checkbox\" value='1' name=\"amountType" + amountMadeRows + "\" id=\"amountType" + amountMadeRows + "\" class=\"mdl-checkbox__input\"></label></td></tr>").appendTo("#madeTbody");
        componentHandler.upgradeDom();
    }

    function removeMadeRow() {
        if (amountMadeRows > 1) {
            $("#madeRow" + amountMadeRows).remove();
            amountMadeRows--;
        }
    }

</script>