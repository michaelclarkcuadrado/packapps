<!DOCTYPE HTML>
<?php
include '../../config.php';
$userData = packapps_authenticate_user('quality');

?>
<html>
<head>
    <title>Quality Panel: Preharvest</title>
    <meta charset="utf-8" />
    <!--[if lte IE 8]><script src="../assets/js/ie/html5shiv.js"></script><![endif]-->
    <link rel="stylesheet" href="../assets/css/main.css" />
    <noscript><link rel="stylesheet" href="../assets/css/noscript.css" /></noscript>
    <!--[if lte IE 8]><link rel="stylesheet" href="../assets/css/ie8.css" /><![endif]-->
    <link rel="apple-touch-icon" href="../apple-touch-icon.png">
    <link rel="icon" sizes="196x196" href="../apple-touch-icon.png">
    <!-- Scripts -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/skel.min.js"></script>
    <script src="../assets/js/skel-viewport.min.js"></script>
    <script src="../assets/js/util.js"></script>
    <script src="../assets/js/main.js"></script>
    <!--[if lte IE 8]><script src="../assets/js/ie/respond.min.js"></script><![endif]-->
</head>
<body>
<!-- Wrapper-->
<div id="wrapper">
    <!-- Nav -->
    <nav id="nav">
        <a href="../" class='icon fa-arrow-left'><span>Quality</span></a>
        <p style="font-size: 2em; text-align: center; display: inline-block" class="icon" >(</p>
        <a href='#preharvest' class='icon fa-edit'><span>Manual Entry</span></a>
        <p style="font-size: 1.2em; text-align: center; display: inline-block" class="icon" >- OR -</p>
        <a href='#FTApreharvest' class='icon fa-upload'><span>FTA Upload</span></a>
        <p style="font-size: 2em; text-align: center; display: inline-block" class="icon" >) </p>
        <p style="font-size: 1em; text-align: center; display: inline-block; vertical-align: super; padding: 10px" class="icon fa-long-arrow-right" ></p>
                <a href='#starch' class='icon fa-eyedropper'><span>Starch</span></a>
                <a style="font-size: 1em; text-align: center; width: 50px;" href="/" class="icon">Main Menu</a>
    </nav>

    <!-- Main -->
    <div id="main">

        <!-- Welcome Screen -->
        <article id="welcome" class="panel" style="margin-bottom: 0">
            <header style="margin-bottom: 0">
                <h1><i class="fa fa-stethoscope"></i> Pre-harvest Check-ups</h1>
	            <p>Logged in as <i class="icon fa-leaf"></i><?echo $userData['Real Name']?></p>
            </header>
            <a href="#preharvest" class="jumplink pic">
                <span class="arrow icon fa-chevron-right"></span>
        <!-- <img src="images/bins.jpg" width="75"> -->
            </a>
        </article>

        <!-- Pre-Harvest Block Evaluation -->
        <article id="preharvest" class="panel">
            <header>
                <h2>Pre-Harvest Sample Evaluation</h2>
                <p><?echo $companyName?> Quality Assurance Lab</p>
            </header>
            <? if(isset($_GET['ph-block'])){echo "<h2><span class='fa fa-check-circle'></span><b> Data for Block ID-".$_GET['ph-block']." received.</b></h2><br>";}?>
            <hr>
            <form enctype='multipart/form-data' action='Preharvestsubmit.php' method='post'>
                <h3>Block ID</h3><table><tr>
                        <td colspan="1"><input id="BlockIDinput" name='Block' type='number' autocomplete='off' class='3u' value='' placeholder="Block ID" required></td>
                        <td colspan="1"> <a onclick="window.open('blockidfinder.php', 'newwindow', 'width=800, height=600'); return false;" href="blockidfinder.php" class="icon fa-search"> Look up Block ID</a> </td><td style="text-align: center; vertical-align: middle"><label>ReTain?<input type="checkbox" value="1" name="Retain"></label></td>
                    <tr id='replacemewithdata'><td colspan="3"><hr></td></tr>
                    <tr><td colspan="3"><input type='text' name='notes' value='' placeholder='Notes on Defects' maxlength="255"></td></tr>
                    <tr><td colspan="3"><hr></td></tr>
                    <tr><td colspan="3"><h3>Individual Apple Samples</h3></td> </tr>
                    <tr><td style="text-align: center" colspan="3"><label>5 Samples<input onclick="$('.ph10, .ph15').hide();$('tr.ph10 input, tr.ph15 input').prop('disabled', true).prop('required', false);$(window).resize();" type='radio' name="NumSamples" value="5" required checked></label><label>10 Samples<input onclick="$('.ph10').show();$('.ph15').hide();$('tr.ph10 input').prop('disabled', false);$('tr.ph15 input').prop('disabled', true);$('tr.ph10 input').prop('required', true);$('tr.ph15 input').prop('required', false);$(window).resize();" type='radio' name="NumSamples" value="10"></label><label>15 Samples<input onclick="$('.ph10, .ph15').show();$('tr.ph10 input,tr.ph15 input').prop('disabled', false);$('tr.ph10 input,tr.ph15 input').prop('required', true);$(window).resize();" type='radio' name="NumSamples" value="15"></label></td></tr>
                </table>
                <table id="appendme"><tr style='text-align: center'><td>Sample / Test</td><td><b>Pressure 1</b></td><td><b>Pressure 2</b></td><td><b>Weight</b></td><td><b>Brix</b></td><td><b>A- Side DA</b></td><td><b>B-Side DA</b></td></tr>
                    <tr><td><b>Fruit 1</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure1-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure1-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight1' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix1' placeholder='0'></td><td><input type='number' name='DA1-1' max="5" step="any" placeholder='0' value='' required></td><td><input type='number' name='DA1-2' max="5" step="any" placeholder='0' value='' required></td></tr>
                    <tr><td><b>Fruit 2</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure2-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure2-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight2' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix2' placeholder='0'></td><td><input type='number' name='DA2-1' max="5" step="any" placeholder='0' value='' required></td><td><input type='number' name='DA2-2' max="5" step="any" placeholder='0' value='' required></td></tr>
                    <tr><td><b>Fruit 3</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure3-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure3-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight3' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix3' placeholder='0'></td><td><input type='number' name='DA3-1' max="5" step="any" placeholder='0' value='' required></td><td><input type='number' name='DA3-2' max="5" step="any" placeholder='0' value='' required></td></tr>
                    <tr><td><b>Fruit 4</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure4-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure4-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight4' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix4' placeholder='0'></td><td><input type='number' name='DA4-1' max="5" step="any" placeholder='0' value='' required></td><td><input type='number' name='DA4-2' max="5" step="any" placeholder='0' value='' required></td></tr>
                    <tr><td><b>Fruit 5</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure5-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure5-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight5' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix5' placeholder='0'></td><td><input type='number' name='DA5-1' max="5" step="any" placeholder='0' value='' required></td><td><input type='number' name='DA5-2' max="5" step="any" placeholder='0' value='' required></td></tr>
                    <tr class="ph10"><td><b>Fruit 6</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure6-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure6-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight6' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix6' placeholder='0'></td><td><input type='number' max="5" step="any" name='DA6-1' placeholder='0' value='' required></td><td><input type='number' max="5" step="any" name='DA6-2' placeholder='0' value='' required></td></tr>
                    <tr class="ph10"><td><b>Fruit 7</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure7-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure7-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight7' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix7' placeholder='0'></td><td><input type='number' max="5" step="any" name='DA7-1' placeholder='0' value='' required></td><td><input type='number' max="5" step="any" name='DA7-2' placeholder='0' value='' required></td></tr>
                    <tr class="ph10"><td><b>Fruit 8</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure8-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure8-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight8' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix8' placeholder='0'></td><td><input type='number' max="5" step="any" name='DA8-1' placeholder='0' value='' required></td><td><input type='number' max="5" step="any" name='DA8-2' placeholder='0' value='' required></td></tr>
                    <tr class="ph10"><td><b>Fruit 9</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure9-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure9-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight9' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix9' placeholder='0'></td><td><input type='number' max="5" step="any" name='DA9-1' placeholder='0' value='' required></td><td><input type='number' max="5" step="any" name='DA9-2' placeholder='0' value='' required></td></tr>
                    <tr class="ph10"><td><b>Fruit 10</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure10-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure10-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight10' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix10' placeholder='0'></td><td><input type='number' max="5" step="any" name='DA10-1' placeholder='0' value='' required></td><td><input type='number' max="5" step="any" name='DA10-2' placeholder='0' value='' required></td></tr>
                    <tr class="ph15"><td><b>Fruit 11</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure11-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure11-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight11' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix11' placeholder='0'></td><td><input type='number' max="5" step="any" name='DA11-1' placeholder='0' value='' required></td><td><input type='number' max="5" step="any" name='DA11-2' placeholder='0' value='' required></td></tr>
                    <tr class="ph15"><td><b>Fruit 12</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure12-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure12-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight12' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix12' placeholder='0'></td><td><input type='number' max="5" step="any" name='DA12-1' placeholder='0' value='' required></td><td><input type='number' max="5" step="any" name='DA12-2' placeholder='0' value='' required></td></tr>
                    <tr class="ph15"><td><b>Fruit 13</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure13-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure13-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight13' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix13' placeholder='0'></td><td><input type='number' max="5" step="any" name='DA13-1' placeholder='0' value='' required></td><td><input type='number' max="5" step="any" name='DA13-2' placeholder='0' value='' required></td></tr>
                    <tr class="ph15"><td><b>Fruit 14</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure14-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure14-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight14' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix14' placeholder='0'></td><td><input type='number' max="5" step="any" name='DA14-1' placeholder='0' value='' required></td><td><input type='number' max="5" step="any" name='DA14-2' placeholder='0' value='' required></td></tr>
                    <tr class="ph15"><td><b>Fruit 15</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure15-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure15-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight15' placeholder='0' required></td><td><input max='30' min='0' type='number' step='any' name='brix15' placeholder='0'></td><td><input type='number' max="5" step="any" name='DA15-1' placeholder='0' value='' required></td><td><input type='number' max="5" step="any" name='DA15-2' placeholder='0' value='' required></td></tr>
                    <tr><td colspan="7"><hr></td></tr>
                </table>
                <input id="phFormSubmit" type='submit' value='Send to Starch'><br><span style='text-align: justify' class="icon fa-check-circle"><strong> Inspected by <?echo $userData['Real Name']?></strong></span></form>
        </article>

        <!-- FTA: Pre-Harvest Block Evaluation -->
        <article id="FTApreharvest" class="panel">
            <header>
                <h2>Pre-Harvest Sample Evaluation</h2>
                <p><?echo $companyName?> Quality Assurance Lab</p>
            </header>
            <? if(isset($_GET['phfta-block'])){echo "<h2><span class='fa fa-check-circle'></span><b> Data for Block ID-".$_GET['ph-block']." received.</b></h2><br>";}?>
            <hr><form enctype='multipart/form-data' id="ftaup" action='ftadata.php' method='post'><table>
                <tr style='text-align: center'><td colspan="2"><h3>FTA File Upload</h3><br><p>Make sure the reference number of the test is the Block ID.</p></td></tr>
                        <tr><td><input id="BlockFTAInput" name='Block' type='file' accept='application/vnd.ms-excel' class='3u' value='' placeholder="Block ID" required></td>
                        <td> <a onclick="window.open('blockidfinder.php', 'newwindow', 'width=800, height=600'); return false;" href="blockidfinder.php" class="icon fa-search"> Look up Block ID</a> </td></tr>
                </table></form>

                <form enctype='multipart/form-data' action='Preharvestsubmit.php' method='post'><div id='ftareplacemewithdata'></div>  <table id="appendme">
                        <tr><td colspan="7"><hr></td></tr>
                <tr><td colspan="7"><input id='FTANote' type='text' name='notes' value='' placeholder='Notes on Defects' maxlength="255"></td><td style="text-align: center; vertical-align: middle"><label>ReTain?<input type="checkbox" value="1" name="Retain"></label></td></tr>
                        <input type="hidden" id="PK" required>
                <tr class="fta5"><td colspan="7"><h3>Individual Apple Samples</h3></td> </tr>
                <tr class="fta5" style='text-align: center'><td>Sample / Test</td><td><b>Pressure 1</b></td><td><b>Pressure 2</b></td><td><b>Weight</b></td><td><b>Brix</b></td><td><b>A- Side DA</b></td><td><b>B-Side DA</b></td></tr>
                <tr class="fta5"><td><b>Fruit 1</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure1-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure1-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight1' placeholder='0' required></td><td><input tabindex="1" max='30' min='0' type='number' step='any' name='brix1' placeholder='0'></td><td><input tabindex="2" type='number' name='DA1-1' max="5" step="any" placeholder='0' value='' required></td><td><input tabindex="3" type='number' name='DA1-2' max="5" step="any" placeholder='0' value='' required></td></tr>
                    <tr class="fta5"><td><b>Fruit 2</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure2-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure2-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight2' placeholder='0' required></td><td><input tabindex="4" max='30' min='0' type='number' step='any' name='brix2' placeholder='0'></td><td><input tabindex="5" type='number' name='DA2-1' max="5" step="any" placeholder='0' value='' required></td><td><input tabindex="6" type='number' name='DA2-2' max="5" step="any" placeholder='0' value='' required></td></tr>
                    <tr class="fta5"><td><b>Fruit 3</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure3-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure3-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight3' placeholder='0' required></td><td><input tabindex="7" max='30' min='0' type='number' step='any' name='brix3' placeholder='0'></td><td><input tabindex="7" type='number' name='DA3-1' max="5" step="any" placeholder='0' value='' required></td><td><input tabindex="8" type='number' name='DA3-2' max="5" step="any" placeholder='0' value='' required></td></tr>
                    <tr class="fta5"><td><b>Fruit 4</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure4-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure4-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight4' placeholder='0' required></td><td><input tabindex="10" max='30' min='0' type='number' step='any' name='brix4' placeholder='0'></td><td><input tabindex="11" type='number' name='DA4-1' max="5" step="any" placeholder='0' value='' required></td><td><input tabindex="12" type='number' name='DA4-2' max="5" step="any" placeholder='0' value='' required></td></tr>
                    <tr class="fta5"><td><b>Fruit 5</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure5-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure5-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight5' placeholder='0' required></td><td><input tabindex="13" max='30' min='0' type='number' step='any' name='brix5' placeholder='0'></td><td><input tabindex="14" type='number' name='DA5-1' max="5" step="any" placeholder='0' value='' required></td><td><input tabindex="15" type='number' name='DA5-2' max="5" step="any" placeholder='0' value='' required></td></tr>
                    <tr class="fta10"><td><b>Fruit 6</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure6-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure6-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight6' placeholder='0' required></td><td><input tabindex="16" max='30' min='0' type='number' step='any' name='brix6' placeholder='0'></td><td><input tabindex="17" type='number' max="5" step="any" name='DA6-1' placeholder='0' value='' required></td><td><input tabindex="18" type='number' max="5" step="any" name='DA6-2' placeholder='0' value='' required></td></tr>
                    <tr class="fta10"><td><b>Fruit 7</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure7-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure7-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight7' placeholder='0' required></td><td><input tabindex="19" max='30' min='0' type='number' step='any' name='brix7' placeholder='0'></td><td><input tabindex="20" type='number' max="5" step="any" name='DA7-1' placeholder='0' value='' required></td><td><input tabindex="21" type='number' max="5" step="any" name='DA7-2' placeholder='0' value='' required></td></tr>
                    <tr class="fta10"><td><b>Fruit 8</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure8-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure8-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight8' placeholder='0' required></td><td><input tabindex="22" max='30' min='0' type='number' step='any' name='brix8' placeholder='0'></td><td><input tabindex="23" type='number' max="5" step="any" name='DA8-1' placeholder='0' value='' required></td><td><input tabindex="24" type='number' max="5" step="any" name='DA8-2' placeholder='0' value='' required></td></tr>
                    <tr class="fta10"><td><b>Fruit 9</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure9-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure9-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight9' placeholder='0' required></td><td><input tabindex="25" max='30' min='0' type='number' step='any' name='brix9' placeholder='0'></td><td><input tabindex="26" type='number' max="5" step="any" name='DA9-1' placeholder='0' value='' required></td><td><input tabindex="27" type='number' max="5" step="any" name='DA9-2' placeholder='0' value='' required></td></tr>
                    <tr class="fta10"><td><b>Fruit 10</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure10-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure10-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight10' placeholder='0' required></td><td><input tabindex="28" max='30' min='0' type='number' step='any' name='brix10' placeholder='0'></td><td><input tabindex="29" type='number' max="5" step="any" name='DA10-1' placeholder='0' value='' required></td><td><input tabindex="30" type='number' max="5" step="any" name='DA10-2' placeholder='0' value='' required></td></tr>
                    <tr class="fta15"><td><b>Fruit 11</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure11-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure11-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight11' placeholder='0' required></td><td><input tabindex="31" max='30' min='0' type='number' step='any' name='brix11' placeholder='0'></td><td><input tabindex="32" type='number' max="5" step="any" name='DA11-1' placeholder='0' value='' required></td><td><input tabindex="33" type='number' max="5" step="any" name='DA11-2' placeholder='0' value='' required></td></tr>
                    <tr class="fta15"><td><b>Fruit 12</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure12-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure12-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight12' placeholder='0' required></td><td><input tabindex="34" max='30' min='0' type='number' step='any' name='brix12' placeholder='0'></td><td><input tabindex="35" type='number' max="5" step="any" name='DA12-1' placeholder='0' value='' required></td><td><input tabindex="36" type='number' max="5" step="any" name='DA12-2' placeholder='0' value='' required></td></tr>
                    <tr class="fta15"><td><b>Fruit 13</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure13-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure13-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight13' placeholder='0' required></td><td><input tabindex="37" max='30' min='0' type='number' step='any' name='brix13' placeholder='0'></td><td><input tabindex="38" type='number' max="5" step="any" name='DA13-1' placeholder='0' value='' required></td><td><input tabindex="39" type='number' max="5" step="any" name='DA13-2' placeholder='0' value='' required></td></tr>
                    <tr class="fta15"><td><b>Fruit 14</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure14-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure14-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight14' placeholder='0' required></td><td><input tabindex="40" max='30' min='0' type='number' step='any' name='brix14' placeholder='0'></td><td><input tabindex="41" type='number' max="5" step="any" name='DA14-1' placeholder='0' value='' required></td><td><input tabindex="42" type='number' max="5" step="any" name='DA14-2' placeholder='0' value='' required></td></tr>
                    <tr class="fta15"><td><b>Fruit 15</td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure15-1' placeholder='0' required></td><td><input value='' max='30' min='0.01' type='number' step='any' name='pressure15-2' placeholder='0' required></td><td><input value='' min='0.01' max='5' type='number' step='any' name='weight15' placeholder='0' required></td><td><input tabindex="43" max='30' min='0' type='number' step='any' name='brix15' placeholder='0'></td><td><input tabindex="44" type='number' max="5" step="any" name='DA15-1' placeholder='0' value='' required></td><td><input tabindex="45" type='number' max="5" step="any" name='DA15-2' placeholder='0' value='' required></td></tr>
                    <tr><td colspan="7"><hr></td></tr>
                </table>
                <input id="ftaFormSubmit" type='submit' value='Send to Starch'><br><span style='text-align: justify' class="icon fa-check-circle"><strong> Inspected by <?echo $userData['Real Name']?></strong></span></form>
        </article>

        <!-- Starch Testing Phase -->
        <article id="starch" class="panel">
            <header>
                <h2>Starch Results Phase</h2>
                <p><?echo $companyName?> Quality Assurance Lab</p>
            </header>
            <iframe style='border: solid black 1px' src="mobilestarch.php" height="570" width="500"></iframe>
        </article>


    <!-- Footer -->
    <div id="footer">
        <ul class="copyright">
            <li>&copy; MCC</li>
        </ul>
    </div>

</div>

<script>

    //FTA decode and autofill
    $('#BlockFTAInput').on('change', prepareupload);
    function prepareupload(event){
        var FTAfile = event.target.files;
        var data = new FormData();
        $.each(FTAfile, function(key, value){
            data.append(key,value);
        });
            $.ajax({
                type: 'post',
                url: 'ftadata.php',
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (data) {
                    var BlockID = data.BlockID;
                    if (data.Error == 'Null'){$(window).reload()}
                    if (data.NumSamples == 5)
                        {
                            //show elements and enable them
                            $('tr.fta5').show();$('tr.fta5 input').prop('disabled', false).prop('required', true);$('tr.fta10, tr.fta15').hide();$('tr.fta10 input, tr.fta15 input').prop('disabled', true).prop('required', false);
                            //autofill FTA vals
                            $("input[name='weight1']").val(data[0][0]);$("input[name='pressure1-1']").val(data[0][1]);$("input[name='pressure1-2']").val(data[0][2]);
                            $("input[name='weight2']").val(data[1][0]);$("input[name='pressure2-1']").val(data[1][1]);$("input[name='pressure2-2']").val(data[1][2]);
                            $("input[name='weight3']").val(data[2][0]);$("input[name='pressure3-1']").val(data[2][1]);$("input[name='pressure3-2']").val(data[2][2]);
                            $("input[name='weight4']").val(data[3][0]);$("input[name='pressure4-1']").val(data[3][1]);$("input[name='pressure4-2']").val(data[3][2]);
                            $("input[name='weight5']").val(data[4][0]);$("input[name='pressure5-1']").val(data[4][1]);$("input[name='pressure5-2']").val(data[4][2]);
                            $.ajax({
                                type: 'GET',
                                url: "../API/blockinfoqa.php?q="+BlockID,
                                dataType: 'json',
                                cache: false,
                                success: function (blockdata) {
                                    if (blockdata.Error != "NULL") {
                                        $("#ftareplacemewithdata").replaceWith("<div id='replacemewithdata' style='text-align: center'><input type='hidden' value=" + blockdata.grower + " name='Grower'><input type='hidden' value=" + data.NumSamples + " name='NumSamples'><input type='hidden' value="+ BlockID + " name='Block'><tr><td colspan='3'><b>Grower:</b> " + blockdata.grower + "  <b>Farm:</b></b> " + blockdata.farm + "  <b>Block:</b> " + blockdata.block + "  <b>Variety:</b> " + blockdata.variety + "  <b>Strain:</b> " + blockdata.strain + "</td></tr></div>");
                                        $("#ftaFormSubmit").prop("disabled", false);
                                    }
                                    else {
                                        $("#ftareplacemewithdata").replaceWith("<div id='replacemewithdata' style='text-align: center'></form><tr><td colspan='3'><span class='icon fa-times-circle'><b> That file's reference number, \""+BlockID +"\" , is not a valid block ID. It cannot be accepted.</b></span></td></tr><tr><td colspan='4'><hr></td></tr></div>");
                                        $("#ftaFormSubmit").prop("disabled", true);
                                    }
                                },
                                error: function () {
                                    alert("Error while getting blockinfo");
                                    return 1;
                                }
                            });
                            $(window).resize();
                        }
                    if (data.NumSamples == 10)
                        {
                            $('tr.fta10, tr.fta5').show();$('tr.fta5 input, tr.fta10 input').prop('disabled', false).prop('required', true);$('tr.fta15').hide();$('tr.fta15 input').prop('disabled', true).prop('required', false);
                            //autofill FTA vals
                            $("input[name='weight1']").val(data[0][0]);$("input[name='pressure1-1']").val(data[0][1]);$("input[name='pressure1-2']").val(data[0][2]);
                            $("input[name='weight2']").val(data[1][0]);$("input[name='pressure2-1']").val(data[1][1]);$("input[name='pressure2-2']").val(data[1][2]);
                            $("input[name='weight3']").val(data[2][0]);$("input[name='pressure3-1']").val(data[2][1]);$("input[name='pressure3-2']").val(data[2][2]);
                            $("input[name='weight4']").val(data[3][0]);$("input[name='pressure4-1']").val(data[3][1]);$("input[name='pressure4-2']").val(data[3][2]);
                            $("input[name='weight5']").val(data[4][0]);$("input[name='pressure5-1']").val(data[4][1]);$("input[name='pressure5-2']").val(data[4][2]);
                            $("input[name='weight6']").val(data[5][0]);$("input[name='pressure6-1']").val(data[5][1]);$("input[name='pressure6-2']").val(data[5][2]);
                            $("input[name='weight7']").val(data[6][0]);$("input[name='pressure7-1']").val(data[6][1]);$("input[name='pressure7-2']").val(data[6][2]);
                            $("input[name='weight8']").val(data[7][0]);$("input[name='pressure8-1']").val(data[7][1]);$("input[name='pressure8-2']").val(data[7][2]);
                            $("input[name='weight9']").val(data[8][0]);$("input[name='pressure9-1']").val(data[8][1]);$("input[name='pressure9-2']").val(data[8][2]);
                            $("input[name='weight10']").val(data[9][0]);$("input[name='pressure10-1']").val(data[9][1]);$("input[name='pressure10-2']").val(data[9][2]);
                            $.ajax({
                                type: 'GET',
                                url: "../API/blockinfoqa.php?q="+BlockID,
                                dataType: 'json',
                                cache: false,
                                success: function (blockdata) {
                                    if (blockdata.Error != "NULL") {
                                        $("#ftareplacemewithdata").replaceWith("<div id='replacemewithdata' style='text-align: center'><input type='hidden' value=" + blockdata.grower + " name='Grower'><input type='hidden' value=" + data.NumSamples + " name='NumSamples'><input type='hidden' value="+ BlockID + " name='Block'><tr><td colspan='3'><b>Grower:</b> " + blockdata.grower + "  <b>Farm:</b></b> " + blockdata.farm + "  <b>Block:</b> " + blockdata.block + "  <b>Variety:</b> " + blockdata.variety + "  <b>Strain:</b> " + blockdata.strain + "</td></tr></div>");
                                        $("#ftaFormSubmit").prop("disabled", false);
                                    }
                                    else {
                                        $("#ftareplacemewithdata").replaceWith("<div id='replacemewithdata' style='text-align: center'></form><tr><td colspan='3'><span class='icon fa-times-circle'><b> That file's reference number, \""+BlockID +"\" , is not a valid block ID. It cannot be accepted.</b></span></td></tr><tr><td colspan='4'><hr></td></tr></div>");
                                        $("#ftaFormSubmit").prop("disabled", true);
                                    }
                                },
                                error: function () {
                                    alert("Error while getting blockinfo");
                                    return 1;
                                }
                            });
                            $(window).resize();
                        }
                    if (data.NumSamples == 15)
                        {
                            $('tr.fta10, tr.fta5, tr.fta15').show();$('tr.fta5 input, tr.fta10 input, tr.fta15 input').prop('disabled', false).prop('required', true)
                            //autofill FTA vals
                            $("input[name='weight1']").val(data[0][0]);$("input[name='pressure1-1']").val(data[0][1]);$("input[name='pressure1-2']").val(data[0][2]);
                            $("input[name='weight2']").val(data[1][0]);$("input[name='pressure2-1']").val(data[1][1]);$("input[name='pressure2-2']").val(data[1][2]);
                            $("input[name='weight3']").val(data[2][0]);$("input[name='pressure3-1']").val(data[2][1]);$("input[name='pressure3-2']").val(data[2][2]);
                            $("input[name='weight4']").val(data[3][0]);$("input[name='pressure4-1']").val(data[3][1]);$("input[name='pressure4-2']").val(data[3][2]);
                            $("input[name='weight5']").val(data[4][0]);$("input[name='pressure5-1']").val(data[4][1]);$("input[name='pressure5-2']").val(data[4][2]);
                            $("input[name='weight6']").val(data[5][0]);$("input[name='pressure6-1']").val(data[5][1]);$("input[name='pressure6-2']").val(data[5][2]);
                            $("input[name='weight7']").val(data[6][0]);$("input[name='pressure7-1']").val(data[6][1]);$("input[name='pressure7-2']").val(data[6][2]);
                            $("input[name='weight8']").val(data[7][0]);$("input[name='pressure8-1']").val(data[7][1]);$("input[name='pressure8-2']").val(data[7][2]);
                            $("input[name='weight9']").val(data[8][0]);$("input[name='pressure9-1']").val(data[8][1]);$("input[name='pressure9-2']").val(data[8][2]);
                            $("input[name='weight10']").val(data[9][0]);$("input[name='pressure10-1']").val(data[9][1]);$("input[name='pressure10-2']").val(data[9][2]);
                            $("input[name='weight11']").val(data[10][0]);$("input[name='pressure11-1']").val(data[10][1]);$("input[name='pressure11-2']").val(data[10][2]);
                            $("input[name='weight12']").val(data[11][0]);$("input[name='pressure12-1']").val(data[11][1]);$("input[name='pressure12-2']").val(data[11][2]);
                            $("input[name='weight13']").val(data[12][0]);$("input[name='pressure13-1']").val(data[12][1]);$("input[name='pressure13-2']").val(data[12][2]);
                            $("input[name='weight14']").val(data[13][0]);$("input[name='pressure14-1']").val(data[13][1]);$("input[name='pressure14-2']").val(data[13][2]);
                            $("input[name='weight15']").val(data[14][0]);$("input[name='pressure15-1']").val(data[14][1]);$("input[name='pressure15-2']").val(data[14][2]);
                            $.ajax({
                                type: 'GET',
                                url: "../API/blockinfoqa.php?q="+BlockID,
                                dataType: 'json',
                                cache: false,
                                success: function (blockdata) {
                                    if (blockdata.Error != "NULL") {
                                        $("#ftareplacemewithdata").replaceWith("<div id='replacemewithdata' style='text-align: center'><input type='hidden' value=" + blockdata.grower + " name='Grower'><input type='hidden' value=" + data.NumSamples + " name='NumSamples'><input type='hidden' value=" + BlockID + " name='Block'><tr><td colspan='3'><b>Grower:</b> " + blockdata.grower + "  <b>Farm:</b></b> " + blockdata.farm + "  <b>Block:</b> " + blockdata.block + "  <b>Variety:</b> " + blockdata.variety + "  <b>Strain:</b> " + blockdata.strain + "</td></tr></div>");
                                        $("#ftaFormSubmit").prop("disabled", false);
                                    }
                                    else {
                                        $("#ftareplacemewithdata").replaceWith("<div id='replacemewithdata' style='text-align: center'></form><tr><td colspan='3'><span class='icon fa-times-circle'><b> That file's reference number, \""+BlockID +"\" , is not a valid block ID. It cannot be accepted.</b></span></td></tr><tr><td colspan='4'><hr></td></tr></div>");
                                        $("#ftaFormSubmit").prop("disabled", true);
                                    }
                                },
                                error: function () {
                                    alert("Error while getting blockinfo");
                                    return 1;
                                }
                            });
                            $(window).resize();
                        }
                    }
                    });
    };

    $( document).ready(function() {

        //prevent hidden elements from submitting form
        $('tr.ph10 input, tr.ph15 input').prop('disabled', true).prop('required', false)
        $('.ph10, .ph15').hide();

        //keep individual sample fields hidden until NumSamples returns
        $('tr.fta5, tr.fta10, tr.fta15').hide();
        $('tr.fta10 input, tr.fta15 input').prop('disabled', true).prop('required', false);



        //get around all the spacing problems of the template
        $(window).scroll(function () {
            $(window).resize();
        });

//make enter act like tab - for numpads
        $('body').on('keydown', 'input[type=\'number\'], select, textarea', function (e) {
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

//get block info for pre-harvest
        $("#BlockIDinput").on("change", function () {
            $.ajax({
                type: 'GET',
                url: "../API/blockinfoqa.php?q=" + $("#BlockIDinput").val(),
                dataType: 'json',
                cache: false,
                success: function (data) {
                    if (data.Error != "NULL") {
                        $("#replacemewithdata").replaceWith("<div id='replacemewithdata' style='text-align: center'><input type='hidden' value=" + data.grower + " name='Grower'><tr><td colspan='3'><b>Grower:</b> " + data.grower + "  <b>Farm:</b></b> " + data.farm + "  <b>Block:</b> " + data.block + "  <b>Variety:</b> " + data.variety + "  <b>Strain:</b> " + data.strain + "</td></tr><tr><td colspan='4'><hr></td></tr></div>");
                        $("#phFormSubmit").prop("disabled", false);
                    }
                    else {
                        $("#replacemewithdata").replaceWith("<div id='replacemewithdata' style='text-align: center'></form><tr><td colspan='3'><span class='icon fa-times-circle'><b> Not a Valid ID! Please Check.</b></span></td></tr><tr><td colspan='4'><hr></td></tr></div>");
                        $("#phFormSubmit").prop("disabled", true);
                    }
                },
                error: function () {
                    alert("Error while getting blockinfo");
                    return 1;
                }
            })
        });
    });
</script>
</body>
</html>