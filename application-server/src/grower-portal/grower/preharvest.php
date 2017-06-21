<!DOCTYPE HTML>
<html>
<head>
    <?php
    include '../config_grower.php';
    $adminauth = mysqli_query($mysqli, "SELECT isAdmin FROM GrowerData WHERE GrowerCode='" . $_SERVER['PHP_AUTH_USER'] . "'");
    $admin = mysqli_fetch_array($adminauth);
    $preharvestdata = mysqli_query($mysqli, "SELECT Preharvest_Samples.PK, CASE WHEN Retain=0 THEN 'No' ELSE 'Yes' END AS Retain, date_format(`Date`, '%e-%b-%Y') AS Date, round(avg(((Pressure1)+(Pressure2))/2),3) AS Pressure, round(avg(Brix),1) AS Brix, round(avg(Weight),3) AS Weight, round(avg(Starch),1) AS Starch, round(avg(DAAverage),2) AS DA, format(greatest(max(DA),max(DA2)),2) AS HDA, format(least(min(DA),min(DA2)),2) AS LDA, concat(left(Preharvest_Samples.Inspector, (instr(Preharvest_Samples.Inspector,' ')+1)), '.') AS Inspector, CASE WHEN Notes='' THEN 'No Comment.' ELSE Notes END AS Notes, `FarmDesc`, BlockDesc, VarDesc, `Comm Desc`, `Str Desc` AS StrDesc FROM Preharvest_Samples JOIN `crop-estimates`ON Preharvest_Samples.PK=`crop-estimates`.PK WHERE Preharvest_Samples.Grower='" . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . "' AND YEAR(DATE)='".((isset($_GET['YearTested'])) ? $_GET['YearTested'] : date('Y'))."' AND isStarchInspected=1 GROUP BY Preharvest_Samples.PK, DATE(`Date`) ORDER BY unix_timestamp(DATE) DESC;");
    $namecnct = mysqli_query($mysqli, "SELECT GrowerName FROM `GrowerData` WHERE GrowerCode='" . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . "' LIMIT 1");
    $growername = mysqli_fetch_array($namecnct);
    echo "<title>Pre-Harvest Info: " . $growername[0] . "</title>";
    ?>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="description" content="<?echo $companyName?> Grower Control Panel"/>
    <meta name="keywords" content=""/>
    <!--[if lte IE 8]>
    <script src="css/ie/html5shiv.js"></script><![endif]-->
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.scrolly.min.js"></script>
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="icon" sizes="196x196" href="apple-touch-icon.png">
    <script src="js/jquery.scrollzer.min.js"></script>
    <script src="js/skel.min.js"></script>
    <script src="js/skel-layers.min.js"></script>
    <script src="js/init.js"></script>
    <noscript>
        <link rel="stylesheet" href="css/skel.css"/>
        <link rel="stylesheet" href="css/style.css"/>
        <link rel="stylesheet" href="css/style-wide.css"/>
    </noscript>
    <!--[if lte IE 9]>
    <link rel="stylesheet" href="css/ie/v9.css"/><![endif]-->
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="css/ie/v8.css"/><![endif]-->
</head>
<body>

<!-- Piwik -->
<script type="text/javascript">
    var _paq = _paq || [];
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function () {
        var u = "//grower.ricefruit.com/analytics/";
        _paq.push(['setTrackerUrl', u + 'piwik.php']);
        _paq.push(['setSiteId', 1]);
        _paq.push(['setUserId', '<?echo ($admin[0] == 1 && $_GET['pretend']) ? "Admin: " . $_SERVER['PHP_AUTH_USER'] . " logged in as " . addcslashes($growername[0], "'") : addcslashes($growername[0], "'")?>']);
        var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
        g.type = 'text/javascript';
        g.async = true;
        g.defer = true;
        g.src = u + 'piwik.js';
        s.parentNode.insertBefore(g, s);
    })();
</script>

<noscript><p><img src="//grower.ricefruit.com/analytics/piwik.php?idsite=1" style="border:0;" alt=""/></p></noscript>
<!-- End Piwik Code -->
<!-- Header -->
<div id="header" class="skel-layers-fixed">

    <div class="top">

        <!-- Logo -->
        <div id="logo">
            <span class="image"><img src="images/avatar.png" alt=""/></span>
            <h1 id="title"><? echo $growername[0] ?></h1>
            <p><?echo $companyName?> Grower</p>
        </div>

        <!-- Nav -->
        <nav id="nav">
            <ul>
                <li><a href="#top" id="top-link" class="skel-layers-ignoreHref"><span class="icon fa-stethoscope">Pre-Harvest Checkup</span></a>
                </li>
                <li><a href="index.php<? if ($admin[0] == 1 && ($_GET['pretend'])) {
                        echo '?pretend=' . $_GET['pretend'];
                    } else if (isset($_GET['alt_acc'])){
                        echo '?alt_acc=' . $_GET['alt_acc'];
                    }?>" id="top-link"><span class="icon fa-arrow-left">Back</span></a></li>
            </ul>
        </nav>

    </div>
</div>

<!-- Main -->
<div id="main">

    <!-- Intro -->
    <section id="top" class="one dark cover">
        <div class="container">

            <header>
                <h2 class="alt"><strong>Pre-Harvest Checkup</strong><br/></h2>
                <p>Samples taken from orchards pre-harvest will have their quality information displayed here. If you have fruit you would like us to test, give us a call or drop it off in our lobby. Please ensure all samples are clearly marked with block information.</p>
            </header>

            <footer>
                <?echo "<select onchange=\"window.location.search='".(($admin[0] == 1 && ($_GET['pretend'])) ? "?pretend=".$_GET['pretend']."&YearTested='+this.value;\">" : (isset($_GET['alt_acc']) ? "?alt_acc=".$_GET['alt_acc']."&YearTested='+this.value;\">" : "?YearTested='+this.value;\">"));
                        for($i = date('Y'); $i >= 2015; $i--)
                        {
                            echo "<option value='$i' ".(($i == $_GET['YearTested']) ? 'Selected' : '').">$i</option>";
                        }
                    ?>

                </select>
            </footer>

        </div>
    </section>

    <section id="receiving" class="receiving three">
        <div class="container">
            <h2>Test Results</h2>
            <p>All tests are performed at <?echo $companyName?>'s on-site Fruit Lab.</p>
            <br>
            <?
            if (mysqli_num_rows($preharvestdata) == 0) {
                echo "<h2>No tests have been performed this year.</h2><p>If you are waiting for a test, check again later or call in.</p>";
            } else {
                while ($preharvarray = mysqli_fetch_assoc($preharvestdata)) {
                    echo "<span class='icon fa-eyedropper'><b> Block ID#: </b>" . $preharvarray['PK'] . "<b> -- Tested On: </b>" . $preharvarray['Date'] . "</span>";
                    echo "<table border='1px'>";
                    echo "<tr><td><b>Farm</td><td><b>Block</td><td><b>Variety</td><td><b>Strain</td></tr>";
                    echo "<tr><td>" . $preharvarray['FarmDesc'] . "</td><td>" . $preharvarray['BlockDesc'] . "</td><td><img src='images/" . $preharvarray['Comm Desc'] . ".png'> " . $preharvarray['VarDesc'] . "</td><td>" . $preharvarray['StrDesc'] . "</td></tr>";
                    echo "<tr><td><b>Avg. Starch</td><td><b>Avg. Brix</td><td><b>Avg. DA</td><td><b>Avg. Pressure (lb)</td></tr>";
                    echo "<tr><td>" . $preharvarray['Starch'] . "</td><td>" . $preharvarray['Brix'] . "</td><td>" . $preharvarray['DA'] . "<br>(range: " . $preharvarray['LDA'] . " to " . $preharvarray['HDA'] . ")</td><td>" . $preharvarray['Pressure'] . "</td></tr>";
                    echo "<tr><td><b>Avg. Weight (lb)</td><td><b>Treated w/ Retain</td><td colspan='2'><b>Inspector Name</b></td></tr>";
                    echo "<tr><td>" . $preharvarray['Weight'] . "</td><td>" . (($preharvarray['Retain'] == 'No') ? "<span class='icon fa-times'> No</span> " : "<span class='icon fa-check'> Yes</span>") . "</td><td colspan='2'>" . $preharvarray['Inspector'] . "</td></tr>";
                    echo "<tr><td colspan='2'><a href='img.php?q=ID" . $preharvarray['PK'] . "--" . $preharvarray['Date'] . "--starch.jpg'><b><span onclick=\"_paq.push(['trackEvent', 'Pre-Harvest', 'View Starch Photo']);\" class='icon fa-camera'> View Starch Testing Photo</span></b></a></td><td colspan='2'><a href='preharvestcsv.php?ID=" . $preharvarray['PK'] . "&Date=" . $preharvarray['Date'] . ($admin[0] == 1 && ($_GET['pretend']) ? "&pretend=" . $_GET['pretend'] : (isset($_GET['alt_acc']) ? '&alt_acc=' . $_GET['alt_acc'] : $_SERVER['PHP_AUTH_USER'])) . "'><b><span onclick=\"_paq.push(['trackEvent', 'Pre-Harvest', 'Download Excel Report']);\" class='icon fa-cloud-download'> Download Complete Pre-harvest Report</span></b></a></td>";
                    echo "<tr><td colspan='4'><b>Notes: </b>" . $preharvarray['Notes'] . "</td></tr>";
                    echo "</table><br>";
                }
            }
            ?>
        </div>
    </section>


</div>
<!-- Footer -->
<div id="footer">

    <!-- Copyright -->
    <ul class="copyright">
        <li>&copy; MCC</li>
    </ul>

</div>

</body>
</html>
