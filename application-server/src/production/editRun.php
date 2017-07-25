<?
require '../config.php';
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
                                <th class="mdl-data-table__cell--non-numeric"></th>
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
                                <td id="tdreorder1">
                                    <div>
                                        <button onclick='moveDumpedUp(1)' type='button' class='mdl-button mdl-js-button mdl-button--icon'>
                                            <i class="material-icons">keyboard_arrow_up</i>
                                        </button><br>
                                        <button onclick='moveDumpedDown(1)' type='button' class='mdl-button mdl-js-button mdl-button--icon'>
                                            <i class='material-icons'>keyboard_arrow_down</i>
                                        </button>
                                    </div>
                                </td>
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
                                <th class="mdl-data-table__cell--non-numeric"></th>
                                <th class="mdl-data-table__cell--non-numeric">Product</th>
                                <th class="mdl-data-table__cell--non-numeric">Pack Size</th>
                                <th class="mdl-data-table__cell--non-numeric">Amount</th>
                                <th class="mdl-data-table__cell--non-numeric">Boxes</th>
                            </tr>
                            </thead>
                            <tbody id="madeTbody">
                            <tr id="madeRow1">
                                <td id='tdmadereorder" + rowNum + "'>
                                    <div>
                                        <button onclick='moveMadeUp(1)' type='button' class='mdl-button mdl-js-button mdl-button--icon'>
                                            <i class='material-icons'>keyboard_arrow_up</i>
                                        </button><br>
                                        <button onclick='moveMadeDown(1)' type='button' class='mdl-button mdl-js-button mdl-button--icon'>
                                            <i class='material-icons'>keyboard_arrow_down</i>
                                        </button>
                                    </div>
                                </td>
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
            <button class="mdl-button mdl-button--raised">Apply To Schedule</button>
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
        $(getDumpedRowHTML(amountDumpedRows)).appendTo("#dumpedTbody");
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
        $(getMadeRowHTML(amountMadeRows)).appendTo("#madeTbody");
        componentHandler.upgradeDom();
    }

    function removeMadeRow() {
        if (amountMadeRows > 1) {
            $("#madeRow" + amountMadeRows).remove();
            amountMadeRows--;
        }
    }

    function getDumpedRowHTML(rowNum){
        return "<tr id=\"dumpedRow" + rowNum + "\"><td id='tdreorder" + rowNum + "'><div><button onclick='moveDumpedUp(" + rowNum + ")' type='button' class='mdl-button mdl-js-button mdl-button--icon'><i class='material-icons'>keyboard_arrow_up</i></button><br><button onclick='moveDumpedDown(" + rowNum + ")' type='button' class='mdl-button mdl-js-button mdl-button--icon'><i class='material-icons'>keyboard_arrow_down</i></button></div></td><td id=\"tdNot" + rowNum + "\"><label class=\"mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect\" for=\"Not" + rowNum + "\"><input onClick=\"(this.checked ? $('#tdNot" + rowNum + "').css('background-color', '#FF9990') : $('#tdNot" + rowNum + "').css('background-color', '#FFFFFF'))\" type=\"checkbox\" value=\"1\" name=\"Not" + rowNum + "\" id=\"Not" + rowNum + "\" class=\"mdl-checkbox__input\"></label></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:7.5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='2' maxlength=\"2\" class=\"mdl-textfield__input\" type=\"text\" name=\"growerCode" + rowNum + "\" id=\"growerCode" + rowNum + "\"><label class=\"mdl-textfield__label\" for=\"growerCode" + rowNum + "\">Grower</label></div></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:6.5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='10' maxlength=\"255\" class=\"mdl-textfield__input\" type=\"text\" name=\"Variety" + rowNum + "\" id=\"Variety" + rowNum + "\"><label class=\"mdl-textfield__label\" for=\"Variety" + rowNum + "\">Variety</label></div></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:4.5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='10' maxlength=\"255\" class=\"mdl-textfield__input\" type=\"text\" name=\"Quality" + rowNum + "\" id=\"Quality" + rowNum + "\"><label class=\"mdl-textfield__label\" for=\"Quality" + rowNum + "\">Grade</label></div></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:4.5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='10' maxlength=\"255\" class=\"mdl-textfield__input\" type=\"text\" name=\"Size" + rowNum + "\" id=\"Size" + rowNum + "\"><label class=\"mdl-textfield__label\" for=\"Size" + rowNum + "\">Size</label></div></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:3.5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='10' maxlength=\"255\" class=\"mdl-textfield__input\" type=\"text\" name=\"Lot" + rowNum + "\" id=\"Lot" + rowNum + "\"><label class=\"mdl-textfield__label\" for=\"Lot" + rowNum + "\">Lot</label></div></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:7em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='10' maxlength=\"255\" class=\"mdl-textfield__input\" type=\"text\" name=\"Location" + rowNum + "\" id=\"Location" + rowNum + "\"><label class=\"mdl-textfield__label\" for=\"Location" + rowNum + "\">Location</label></div></td><td><div style='width :5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input class=\"mdl-textfield__input\" type=\"text\" pattern=\"-?[0-9]*(\\.[0-9]+)?\" name=\"Amount" + rowNum + "\" id=\"Amount" + rowNum + "\"><label class=\"mdl-textfield__label\" for=\"Amount" + rowNum + "\">Amount</label><span class=\"mdl-textfield__error\">Numbers, please.</span></div></td></tr>";
    }

    function getMadeRowHTML(rowNum){
        return "<tr id=\"madeRow" + rowNum + "\"><td id='tdmadereorder" + rowNum + "'><div><button onclick='moveMadeUp(" + rowNum + ")' type='button' class='mdl-button mdl-js-button mdl-button--icon'><i class='material-icons'>keyboard_arrow_up</i></button><br><button onclick='moveMadeDown(" + rowNum + ")' type='button' class='mdl-button mdl-js-button mdl-button--icon'><i class='material-icons'>keyboard_arrow_down</i></button></div></td><td class=\"mdl-data-table__cell--non-numeric\"><div style='width:10em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input class=\"mdl-textfield__input\" type=\"text\" name=\"productMade" + rowNum + "\" id=\"productMade" + rowNum + "\"><label class=\"mdl-textfield__label\" for=\"productMade" + rowNum + "\">Product</label></div></td>    <td class=\"mdl-data-table__cell\"> <div style='width:5em'class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"> <input autocomplete=\"off\" size='10' maxlength=\"255\"class=\"mdl-textfield__input\" type=\"text\" name=\"packSize" + rowNum + "\"id=\"packSize" + rowNum + "\"> <label class=\"mdl-textfield__label\" for=\"packSize" + rowNum + "\">Pack Size</label> </div></td> <td class=\"mdl-data-table__cell\"><div style='width:5em' class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\"><input size='10' maxlength=\"255\" class=\"mdl-textfield__input\" type=\"text\" pattern=\"-?[0-9]*(\\.[0-9]+)?\" name=\"madeAmount" + rowNum + "\" id=\"madeAmount" + rowNum + "\"><label class=\"mdl-textfield__label\" for=\"madeAmount" + rowNum + "\">Amount</label><span class=\"mdl-textfield__error\">Numbers, please.</span></div></td><td><label class=\"mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect\" for=\"amountType" + rowNum + "\"><input type=\"checkbox\" value='1' name=\"amountType" + rowNum + "\" id=\"amountType" + rowNum + "\" class=\"mdl-checkbox__input\"></label></td></tr>";
    }

    /* Moves a dumped row up on the form, decreasing its ID */
    function moveDumpedUp(rowNum){
        if(rowNum != 1){
            //get vals
            var rowOnTop = {};
            rowOnTop.Not = $("#Not" + (rowNum -1)).is(':checked');
            rowOnTop.growerCode = $("#growerCode" + (rowNum -1)).val();
            rowOnTop.Variety = $("#Variety" + (rowNum -1)).val();
            rowOnTop.Quality = $("#Quality" + (rowNum -1)).val();
            rowOnTop.Size = $("#Size" + (rowNum -1)).val();
            rowOnTop.Lot = $("#Lot" + (rowNum -1)).val();
            rowOnTop.Location = $("#Location" + (rowNum -1)).val();
            rowOnTop.Amount = $("#Amount" + (rowNum -1)).val();

            var rowOnBottom ={};
            rowOnBottom.Not = $("#Not" + rowNum).is(':checked');
            rowOnBottom.growerCode = $("#growerCode" + rowNum).val();
            rowOnBottom.Variety = $("#Variety" + rowNum).val();
            rowOnBottom.Quality = $("#Quality" + rowNum).val();
            rowOnBottom.Size = $("#Size" + rowNum).val();
            rowOnBottom.Lot = $("#Lot" + rowNum).val();
            rowOnBottom.Location = $("#Location" + rowNum).val();
            rowOnBottom.Amount = $("#Amount" + rowNum).val();

            //push vals into rows
            var replacerObj = rowOnBottom;
            var rowToReplace = rowNum - 1;
            for(var i = 0; i < 2; i++){
                if (replacerObj.Not) {
                    $("#Not" + rowToReplace).attr('checked', true).parent().addClass('is-checked').parent().css('background-color', 'rgb(255, 153, 144)');
                } else {
                    $("#Not" + rowToReplace).attr('checked', false).parent().removeClass('is-checked').parent().css('background-color', 'initial');
                }
                $("#growerCode" + rowToReplace).val(replacerObj.growerCode).parent().addClass('is-dirty');
                $("#Variety" + rowToReplace).val(replacerObj.Variety).parent().addClass('is-dirty');
                $("#Quality" + rowToReplace).val(replacerObj.Quality).parent().addClass('is-dirty');
                $("#Size" + rowToReplace).val(replacerObj.Size).parent().addClass('is-dirty');
                $("#Lot" + rowToReplace).val(replacerObj.Lot).parent().addClass('is-dirty');
                $("#Location" + rowToReplace).val(replacerObj.Location).parent().addClass('is-dirty');
                $("#Amount" + rowToReplace).val(replacerObj.Amount).parent().addClass('is-dirty');
                replacerObj = rowOnTop;
                rowToReplace = rowNum;
            }
        }
    }

    function moveDumpedDown(rowNum){
        if(rowNum != amountDumpedRows){
            moveDumpedUp(rowNum + 1);
        }
    }

    /* Moves a Made row up on the form, decreasing its ID */
    function moveMadeUp(rowNum){
        console.log('test');
        if(rowNum != 1){
            //get vals
            var rowOnTop = {};
            rowOnTop.productMade = $("#productMade" + (rowNum -1)).val();
            rowOnTop.packSize = $("#packSize" + (rowNum -1)).val();
            rowOnTop.madeAmount = $("#madeAmount" + (rowNum -1)).val();
            rowOnTop.amountType = $("#amountType" + (rowNum -1)).is(':checked');

            //get vals
            var rowOnBottom = {};
            rowOnBottom.productMade = $("#productMade" + rowNum).val();
            rowOnBottom.packSize = $("#packSize" + rowNum).val();
            rowOnBottom.madeAmount = $("#madeAmount" + rowNum).val();
            rowOnBottom.amountType = $("#amountType" + rowNum).is(':checked');

            //push vals into rows
            var replacerObj = rowOnBottom;
            var rowToReplace = rowNum - 1;
            for(var i = 0; i < 2; i++){
                if (replacerObj.amountType) {
                   $("#amountType" + rowToReplace).attr('checked', true).parent().addClass('is-checked');
                } else {
                    $("#amountType" + rowToReplace).attr('checked', false).parent().removeClass('is-checked');
                }
                $("#productMade" + rowToReplace).val(replacerObj.productMade).parent().addClass('is-dirty');
                $("#packSize" + rowToReplace).val(replacerObj.packSize).parent().addClass('is-dirty');
                $("#madeAmount" + rowToReplace).val(replacerObj.madeAmount).parent().addClass('is-dirty');
                replacerObj = rowOnTop;
                rowToReplace = rowNum;
            }
        }
    }

    function moveMadeDown(rowNum){
        if(rowNum != amountMadeRows){
            moveMadeUp(rowNum + 1);
        }
    }

</script>