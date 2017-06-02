<!DOCTYPE HTML>
<?php
include '../config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Real Name`, Role, isSectionManager as isAdmin, allowedQuality FROM master_users JOIN quality_UserData ON master_users.username=quality_UserData.UserName WHERE master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedQuality'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
        $Role = $checkAllowed['Role'];
    }
}
// end authentication

//QA Lab only
if ($Role !== "QA") {
    die("Unauthorized. This page is for the QA lab team.");
}
$rts = mysqli_query($mysqli, "SELECT InspectedRTs.RTNum AS `RT#`, ifnull(BULKOHCSV.Grower,'?') AS Grower, ifnull(BULKOHCSV.VarDesc,'?') AS VarDesc, ifnull(BULKOHCSV.Date, date(InspectedRTs.DateInspected)) AS Date FROM InspectedRTs LEFT JOIN BULKOHCSV ON InspectedRTs.RTNum=BULKOHCSV.`RT#` WHERE InspectedRTs.isFinalInspected = '0' ORDER BY InspectedRTs.DateInspected ASC ");
$runs = mysqli_query($mysqli, "SELECT RunID, RunNumber, Line FROM `production_runs` WHERE isQA != 1 and lastEdited >= NOW() - INTERVAL 7 DAY ORDER BY RunID DESC Limit 6;");
$count_total = mysqli_query($mysqli, "SELECT COUNT(*) AS countRT, (SELECT count(*) FROM AppleSamples) AS countSamp, ifnull(round((SELECT sum(Weight) FROM AppleSamples),2), 0) AS Weight FROM InspectedRTs");
$total_count = mysqli_fetch_assoc($count_total);
$count_total_runs = mysqli_query($mysqli, "SELECT COUNT(DISTINCT RunID) AS countRuns, Count(*) as countSamp, ifnull(round(sum(Weight),2), 0) AS Weight FROM run_inspections");
$total_count_runs = mysqli_fetch_assoc($count_total_runs);
?>
<html>
<head>
    <title>Quality Panel</title>
    <meta charset="utf-8"/>
    <!--[if lte IE 8]>
    <script src="assets/js/ie/html5shiv.js"></script><![endif]-->
    <link rel="stylesheet" href="assets/css/main.css"/>
    <noscript>
        <link rel="stylesheet" href="assets/css/noscript.css"/>
    </noscript>
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="assets/css/ie8.css"/><![endif]-->
    <link rel="icon" sizes="196x196" href="apple-touch-icon.png">
    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/skel.min.js"></script>
    <script src="assets/js/skel-viewport.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>
    <!--[if lte IE 8]>
    <script src="assets/js/ie/respond.min.js"></script><![endif]-->
</head>
<body>

<!-- Wrapper-->
<div id="wrapper">

    <!-- Nav -->
    <nav id="nav">

        <a href="#welcome" class="icon fa-home active"><span>Home</span></a>
        <a href="preharvest/" class='icon fa-stethoscope'><span>Pre-Harvest</span></a>
        <p style="font-size: 2em; text-align: center; display: inline-block; width: 5px; color: #ffffff; opacity: 0.75"
           class="icon">[</p>
        <a href='#newRT' onclick="$('#InspectorIframe').attr('src', 'Inspector.php'), $('#LoadInspectorIframe').hide()"
           class='icon fa-truck'><span>New RT</span></a>
        <p style="font-size: 1em; text-align: center; display: inline-block; vertical-align: super; color: #ffffff; opacity: 0.75"
           class="icon fa-long-arrow-right"></p>
        <a href='#DA'
           onclick="$('#DAIframe').attr('src', 'DA.php'), $('#starchIframe').attr('src', 'mobilestarch.php'), $('#LoadDAIframe').slideUp()"
           class='icon fa-tachometer'><span>DA & Starch</span></a>
        <p style="font-size: 1em; text-align: center; display: inline-block; vertical-align: super; color: #ffffff; opacity: 0.75"
           class="icon fa-long-arrow-right"></p>
        <a href="#QA" class="icon fa-check"><span>Lab Testing</span></a>
        <p style="font-size: 2em; text-align: center; display: inline-block; width: 5px; color: #ffffff; opacity: 0.75"
           class="icon">]</p>
        <a href="#RTHistory" target="_blank" class="icon
        fa-history"><span>History</span></a>
        <a href="assets/uploadedimages" target="_blank" class="icon
        fa-camera"><span>Img Archive</span></a>
        <p style="font-size: 2em; text-align: center; display: inline-block; width: 5px; color: #ffffff; opacity: 0.75"
           class="icon">[</p>
        <a href="#runQA" class="icon fa-list"><span>Runs</span></a>
        <p style="font-size: 2em; text-align: center; display: inline-block; width: 5px; color: #ffffff; opacity: 0.75"
           class="icon">]</p>
        <a style="font-size: 1em; text-align: center; width: 50px;" href="/" class="icon">Main Menu</a>
    </nav>

    <!-- Main -->
    <div id="main">

        <!-- Welcome Screen -->
        <article id="welcome" class="panel">
            <header>
                <h1><i class="fa fa-leaf"></i> <? echo "Welcome back, " . strtok($RealName[0], " ") . "."; ?></h1>
                <p><i class="icon fa-star"></i> <? echo $total_count['Weight']+$total_count_runs['Weight'] ?> pounds of fruit
                    from <? echo $total_count['countSamp']+$total_count_runs['countSamp'] ?> samples (<? echo $total_count['countRT'] ?> RTs and <?echo $total_count_runs['countRuns']?> runs) analyzed
                    so far!<br>
                    <? if ($RealName[2] == 1) {
                        echo "<a style='font-size: large' href='usermgmt.php'> <SampleNum class='fa fa-cogs'></SampleNum> Control Panel </a><br>";
                    } ?>
                </p>
            </header>
            <a href="#QA" class="jumplink pic">
                <span class="arrow icon fa-chevron-right"></span>
<!--                                <img src="images/Apple.png" width="75">-->
            </a>
        </article>

        <!-- New RT screen -->
        <article id="newRT" class="panel">
            <header>
                <h2>New RT Report Creator</h2>
                <p><?echo $companyName?> Quality Assurance Lab</p>
            </header>
            <div id='DA'>
                <? if (isset($_GET['error'])) {
                    echo "<h2><span class='fa-stack fa-lg'><SampleNum class='fa fa-database fa-stack-1x'></SampleNum><SampleNum style='color: red' class='fa fa-ban fa-stack-2x'></SampleNum></span><b> There was a database error! Try again.</b></h2><br>";
                } ?>
                <button id="LoadInspectorIframe" style='align-self: center'
                        onclick="$('#InspectorIframe').attr('src', 'Inspector.php'), $(this).slideUp()">Load Report
                </button>
                <br>
                <iframe id='InspectorIframe' style='border: solid black 1px' height="900"
                        width="500"></iframe>
            </div>
        </article>

        <!-- DA screen -->
        <article id="DA" class="panel">
            <header>
                <h2>Extended tests</h2>
                <p><?echo $companyName?> Quality Assurance Lab</p>
            </header>
            <div id='DA'>
                <? if (isset($_GET['error'])) {
                    echo "<h2><span class='fa-stack fa-lg'><SampleNum class='fa fa-database fa-stack-1x'></SampleNum><SampleNum style='color: red' class='fa fa-ban fa-stack-2x'></SampleNum></span><b> There was a database error! Try again.</b></h2><br>";
                } ?>
                <button style='text-align: center' id="LoadDAIframe"
                        onclick="$('#DAIframe').attr('src', 'DA.php'), $('#starchIframe').attr('src', 'mobilestarch.php'), $(this).slideUp()">
                    Load Tests
                </button>
                <table>
                    <tr>
                        <td>
                            <iframe id="DAIframe" style='border: solid black 1px' height="750"
                                    width="500"></iframe>
                        </td>
                        <td>
                            <iframe id="starchIframe" style='border: solid black 1px' height="750"
                                    width="500"></iframe>
                        </td>
                    </tr>
                </table>
            </div>
        </article>

        <!-- Final Inspection Screen -->
        <article id="QA" class="panel">
            <header>
                <h2>RT Report Review and Final Inspection</h2>
                <p><?echo $companyName?> Quality Assurance Lab</p>
            </header>
            <? if (isset($_GET['qa'])) {
                echo "<h2><span class='fa fa-check-circle'></span><b> Data for RT# " . $_GET['qa'] . " received.</b></h2><br>";
            } ?>
            <? if (isset($_GET['error'])) {
                echo "<h2><span class='fa-stack fa-lg'><SampleNum class='fa fa-database fa-stack-1x'></SampleNum><SampleNum style='color: red' class='fa fa-ban fa-stack-2x'></SampleNum></span><b> There was a database error! Try again.</b></h2><br>";
            } ?>
            <div id="rts_selector">
                Select an RT for lab testing: <select onchange="RTInsert();" class='selector'>
                    <option value="" disabled
                            selected><? echo(mysqli_num_rows($rts) == 0 ? "No RTs left. &#9787;" : "Select RT"); ?></option>
                    <?php while ($receivedtodo = mysqli_fetch_assoc($rts)) {
                        echo "<option value='" . $receivedtodo['RT#'] . "'>" . $receivedtodo['Date'] . " - RT#" . $receivedtodo['RT#'] . " - " . $receivedtodo['Grower'] . " - " . $receivedtodo['VarDesc'] . "</ option>";
                    } ?>
                </select> <a style='font-size: small' href='#' onclick='RTlistreload();'> <i class='fa fa-refresh'></i></a>
            </div>
            <div id="RTdatainput">

            </div>
        </article>

        <!-- Testing History -->
        <article class='panel' id='RTHistory'>
            <header>
                <h2>Testing History</h2>
                <p><?echo $companyName?> Quality Assurance Lab</p>
            </header>
            <div>
                <span>Last 20 tests processed by packapps (updates every 5 minutes).</span>
                <button onclick="updateHistory()" style='float:right'><span class="fa fa-refresh"> Refresh</span></button>
                <table>
                    <thead>
                        <th>Type</th>
                        <th>ID</th>
                        <th>Samples</th>
                        <th>Last Change</th>
                        <th>DA</th>
                        <th>Starch</th>
                        <th>Final</th>
                        <th>Inspector</th>
                        <th></th>
                    </thead>
                    <tbody id="insertTestsHere">

                    </tbody>
                </table>
            </div>
        </article>

        <!-- About Screen -->
        <article id="about" class="panel">
            <header>
                <h2>About</h2>
                <p>"Quality" system</p>
            </header>
            <hr>
            <p>This system was designed and built by Michael Clark-Cuadrado, in the spring and summer of 2015 for use by
                the QA department of the Rice Fruit company.</p>
            <p>A thousand bugfixes and improvements, and integration into packapps in summer 2016.</p>
        </article>

        <!-- Run info -->
        <article id="runQA" class="panel">
            <header>
                <h2>Run Quality Assessments</h2>
                <p><?echo $companyName?> Quality Assurance Lab</p>
            </header>
            <? if (isset($_GET['run'])) {
                echo "<h2><span class='fa fa-check-circle'></span><b> Data for Run# " . $_GET['run'] . " received.</b></h2><br>";
            } ?>
            <div id="run_selector">
                Select a run to assess: <select
                    onchange="$('#hiddenRunID').val($(this).val()),$('#runsubmitbtn').prop('disabled', false)" required
                    class='selectorRun'>
                    <option value="" disabled
                            selected><? echo(mysqli_num_rows($runs) == 0 ? "No runs available." : 'Select a run:'); ?></option>
                    <?php while ($runsAvailableTodo = mysqli_fetch_assoc($runs)) {
                        echo "<option value='" . $runsAvailableTodo['RunID'] . "'>Run#" . $runsAvailableTodo['RunNumber'] . " - Line: " . $runsAvailableTodo['Line'] . "</ option>";
                    } ?>
                </select>
            </div>
            <form enctype='multipart/form-data' action='runSubmit.php' method='post'>
                <hr>
                <h3>Run Information</h3>
                <input type="hidden" name="RunID" id="hiddenRunID" value="">
                <table>
                    <tr>
                        <td colspan="2" rowspan="7">
                            <div style='width: 40%; text-align: center; margin-left: auto; margin-right: auto'>
                                <label><span class='icon fa-paperclip'><b>Attach FTA data</b></span><input required
                                                                                                           type='file'
                                                                                                           accept='application/vnd.ms-excel'
                                                                                                           name='xlsupload'></label>
                        </td>
                    </tr>
                    <tr>
                        <td><h3 style="text-align: center">Brix</h3></td>
                    </tr>
                    <tr>
                        <td><input tabindex='1' max='30' min='0' type='number' step='any' name='brix1'
                                   placeholder='Brix #1'></td>
                    </tr>
                    <tr>
                        <td><input tabindex='1' max='30' min='0' type='number' step='any' name='brix2'
                                   placeholder='Brix #2'></td>
                    </tr>
                    <tr>
                        <td><input tabindex='1' max='30' min='0' type='number' step='any' name='brix3'
                                   placeholder='Brix #3'></td>
                    </tr>
                    <tr>
                        <td><input tabindex='1' max='30' min='0' type='number' step='any' name='brix4'
                                   placeholder='Brix #4'></td>
                    </tr>
                    <tr>
                        <td><input tabindex='1' max='30' min='0' type='number' step='any' name='brix5'
                                   placeholder='Brix #5'></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"><input type='text' name='notes' value='' placeholder='Additional Notes'
                                               maxlength="255"></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;" colspan="3">
                            Is this a pre-inspection?
                            <label>Yes<input type="radio" name="isPreInspection" value="1"></label>
                            <label>No<input type="radio" name="isPreInspection" value="0" checked></label>
                            <hr>
                        </td>
                    </tr>
                </table>
                <input disabled type="submit" id="runsubmitbtn" value="Send info to Production"><span
                    class="icon fa-check-circle"><strong> Inspected by <? echo $RealName[0] ?></strong></span></form>
        </article>

    </div>
    <div id="footer"><a style='font-size: x-small;text-decoration: none' href="#about">Quality.Lab</a></div>

    <script>
        $(document).ready(function () {
            //keep selected RT in focus
            if ($(".selector").val() != null) {
                RTInsert();
            }
            updateHistory();
            setInterval(updateHistory(), 300000);
        });


        <?if (isset($_GET['FTAsel'])) {
            echo "$(\".selector\").val(\"" . $_GET['FTAsel'] . "\")";
        }?>

        //get around all the spacing problems of the template
        $(window).scroll(function () {
            $(window).resize();
        });

        //make enter act like tab - for numpads
        $('body').on('keydown', 'input, select, textarea', function (e) {
            var self = $(this)
                , form = self.parents('form:eq(0)')
                , focusable
                , next
                ;
            if (e.keyCode == 13) {
                focusable = form.find('input[type=\'number\'],a,select,textarea').filter(':enabled');
                next = focusable.eq(focusable.index(this) + 1);
                if (next.length) {
                    next.focus();
                } else {
                    event.preventDefault();
                    return false;
                }
                return false;
            }
        });

        function RTlistreload() {
            $("#rts_selector").load("API/RTlist.php");
        }

        function updateHistory(){
            $.getJSON('API/getTestingHistory.php', function(data) {
                string = "";
                for(var test in data) {
                    string += "<tr style='text-align: center'><td>"
                        + data[test]['Type']
                        + "</td><td>"
                        + data[test]['ID']
                        + "</td><td>"
                        + data[test]['Samples']
                        + "</td><td>"
                        + data[test]['Last Change']
                        + "</td><td>"
                        + (data[test]['DA'] == 1 ? "<i style='color:green' class='fa fa-lg fa-check-circle-o'></i>" : "<i style='color:red' class='fa fa-lg fa-times-circle-o'></i>")
                        + "</td><td>"
                        + (data[test]['Starch'] == 1 ? "<i style='color:green' class='fa fa-lg fa-check-circle-o'></i>" : "<i style='color:red' class='fa fa-lg fa-times-circle-o'></i>")
                        + "</td><td>"
                        + (data[test]['Final'] == 1 ? "<i style='color:green' class='fa fa-lg fa-check-circle-o'></i>" : "<i style='color:red' class='fa fa-lg fa-times-circle-o'></i>")
                        + "</td><td>"
                        + data[test]['Inspector']
                        + "</td><td><i onclick=\"(confirm('Delete this test?') ? $.get('API/deleteTest.php?testID="
                        + data[test]['ID']
                        + "&testType="
                        + data[test]['Type']
                        + "') : ''), updateHistory()\" class='fa fa-lg fa-trash' style='cursor: pointer'></i></td></tr>"
                }
                $("#insertTestsHere").html(string);
                $(window).resize();
            });
        }

        function RTInsert() {
            $.ajax({
                type: 'GET',
                url: "API/rtinfoqa.php?q=" + $(".selector").val(),
                dataType: 'json',
                cache: false,
                success: function (data) {
                    RT = $(".selector").val();
                    var img = "<img src=images/Apple.png>";
                    //exclude So Hem fruit from icons
                    if (data.CommDesc.indexOf("So Hem") == -1) {
                        img = "<img src=images/" + data.CommDesc + ".png> "
                    }
                    $("#RTdatainput").replaceWith("<div id=RTdatainput><hr><h3>Shipment Information</h3><table class='shipment'><tr><td><b>Grower:</b></td><td>" + data.GrowerName + "</td><td><b>Commodity:</b></td><td>" + img + data.CommDesc + "</td><td><b>Variety:</b></td><td><mark>" + data.VarDesc + "</mark></td><td><b>Strain:</b></td><td>" + data.StrDesc + "</td></tr><tr><td><b>Farm:</b></td><td>" + data.FarmDesc + "</td><td><b>Block:</b></td><td>" + data.BlockDesc + "</td><td><b>Date Received:</b></td><td>" + data.Date + "</td><td><b>Units on Hand:</b></td><td>" + data.Qty + "</td></tr><tr><td></td><td></td><td><b>Bushels on Hand:</b></td><td>" + data.Bu + "</td><td><b>Received as:</b></td><td>" + data.ReceiptType + "</td><td></td><td></td></tr></table><form action='QAsubmit.php' method='get'><input type='hidden' value='" + RT + "' name='del'><input type='submit' value='Void this RT' style='margin-left: auto; margin-right: auto; background-color: red; display: block'></form><a href='assets/uploadedimages/" + RT + ".jpg'><img style='margin-left: auto; margin-right: auto; display: block' width='600px' src='assets/uploadedimages/" + RT + ".jpg'></a><br> <a href='assets/?RT=" + RT + "' style='text-decoration: none'><button style='margin-left: auto; margin-right: auto; display: block'>More from this block</button></a><form enctype='multipart/form-data' action='QAsubmit.php' method='post'>" +
                        "<div id='FTAmodal'></div><h3>Overall Quality Information</h3><table><tr><td style='text-align: center'>RT Number:</td><td><input name='RT' type='text' class='3u' value='" + RT + "' readonly> </td><td style='text-align: center'>Color Quality:</td><td><input type=text class='3u' name='Color' value='" + data.ColorQuality + (data.Blush != 0 ? ' (With Blush)' : '') + "' disabled></td><tr><td style='text-align: center'>Number of Samples:</td><td><input type='text' name='NumSamples' value='" + data.NumSamples + "' readonly></td><td colspan='2' style='text-align: center;'><SampleNum class='fa fa-check-circle'></SampleNum> Inspected by " + data.InspectedBy + " on " + data.DateInspected + "</td></tr><tr><td colspan='4'><input type='text' name='Notes' maxlength='255' placeholder='Notes' value='" + data.Note + "'></td></tr></table>" +
                        "<table style='text-align: center'><tr><td colspan='7' style='text-align: center'><b><u>Defects</b></td></tr><tr><td><b>Bruising</td><td><b>Bitter Pit</td><td><b>Russeting</td><td><b>San Jose Scale</td><td><b>Sunburn</td><td><b>Scab</td><td><b>Stink Bug</td></tr><tr><td>" + (data.Bruise != 'Severe' ? data.Bruise : '<a href=\'assets/uploadedimages/' + RT + 'bruising.jpg\'>Severe</a>' ) + "</td><td>" + (data.BitterPit == 0 ? 'Not Present' : '<a href=\'assets/uploadedimages/' + RT + 'bitterpit.jpg\'>Present</a>' ) + "</td><td>" + data.Russet + "</td><td>" + data.SanJoseScale + "</td><td>" + data.SunBurn + "</td><td>" + data.Scab + "</td><td>" + data.StinkBug + "</td></tr></table>" +
                        "<br><h3>Individual Fruit Samples</h3>" +
                        "<table><tr style='text-align: center'><td>Sample / Test</td><td><b>Pressure A</b></td><td><b>Pressure B</b></td><td><b>Weight</b></td>" + (data.NumSamples > 5 ? "<td><b>Brix</b></td>" : "") + "</tr>" +
                        RTrows(data) +
                        "</table>" + (data.DAFinished != 1 && data.NumSamples > 5 ? "<h3><span class='icon fa-exclamation-circle'>These samples still need DA testing.</h3>" : "") + (data.StarchFinished != 1 && data.NumSamples == 20 && data.CommDesc != 'Peach' && data.CommDesc != 'Nectarine' ? "<h3><span class='icon fa-exclamation-circle'>These samples still need starch testing.</h3>" : "") +
                        "<input type='submit' value='Submit Testing Result'><span class='icon fa-check-circle'><strong> Inspected by <?echo $RealName[0]?></strong></span></form></div>");
                    if (data.FTAup == 0) {
                        $("#FTAmodal").replaceWith("<h3>(Optional) Attach FTA file</h3><p>Autofill pressure and weight values from FTA</p><div style='width: 40%; text-align: center; margin-left: auto; margin-right: auto'><form enctype='multipart/form-data' method='post' action='ftadata.php'><input type='hidden' value='" + RT + "' name='RT'><label><span class='icon fa-paperclip'><b> Attach FTA data for RT#" + RT + "</b></span><input type='file' accept='application/vnd.ms-excel' name='xlsupload'></label><input type='submit' value='Upload FTA'></form></div>")
                    }
                    $(window).resize();
                    return 0;

                },
                error: function () {
                    alert("Error while pulling RT Data");
                    return 1;
                }
            });
        }

        function RTrows(data) {
            var string = String("");
            for (var i = 0; i < data.NumSamples; ++i) {
                string = string + "<tr><td><b>Fruit " + (i + 1) + "</td><td><input class='FTAfill' value='" + data[i]["Pressure1"] + "' max='30' min='0.01' type='number' step='any' name='pressure" + (i + 1) + "-1' placeholder='0' required></td><td><input class='FTAfill' value='" + data[i]["Pressure2"] + "' max='30' min='0.01' type='number' step='any' name='pressure" + (i + 1) + "-2' placeholder='0' required></td><td><input class='FTAfill' value='" + data[i]["Weight"] + "' min='0.01' max='5' type='number' step='any' name='weight" + (i + 1) + "' placeholder='0' required></td>" + (data.NumSamples > 5 ? "<td><input tabindex='1' max='30' min='0' type='number' step='any' name='brix" + (i + 1) + "' required placeholder='0'></td>" : "") + "</tr>";
            }
            return string;
        }

    </script>
</body>
</html>
