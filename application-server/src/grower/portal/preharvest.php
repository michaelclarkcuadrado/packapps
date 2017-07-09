<!DOCTYPE HTML>
<html>
<head>
    <?php
    include '../../config.php';
    $userinfo = packapps_authenticate_grower();
    $preharvestdata = mysqli_query($mysqli, "SELECT grower_Preharvest_Samples.PK, CASE WHEN Retain=0 THEN 'No' ELSE 'Yes' END AS Retain, date_format(`Date`, '%e-%b-%Y') AS Date, round(avg(((Pressure1)+(Pressure2))/2),3) AS Pressure, round(avg(Brix),1) AS Brix, round(avg(Weight),3) AS Weight, round(avg(Starch),1) AS Starch, round(avg(DAAverage),2) AS DA, format(greatest(max(DA),max(DA2)),2) AS HDA, format(least(min(DA),min(DA2)),2) AS LDA, concat(left(grower_Preharvest_Samples.Inspector, (instr(grower_Preharvest_Samples.Inspector,' ')+1)), '.') AS Inspector, CASE WHEN Notes='' THEN 'No Comment.' ELSE Notes END AS Notes, `FarmDesc`, BlockDesc, VarDesc, `Comm Desc`, `Str Desc` AS StrDesc FROM grower_Preharvest_Samples JOIN `grower_crop-estimates`ON grower_Preharvest_Samples.PK=`grower_crop-estimates`.PK WHERE grower_Preharvest_Samples.Grower='" . $userinfo['GrowerCode'] . "' AND YEAR(DATE)='".((isset($_GET['YearTested'])) ? $_GET['YearTested'] : date('Y'))."' AND isStarchInspected=1 GROUP BY grower_Preharvest_Samples.PK, DATE(`Date`) ORDER BY unix_timestamp(DATE) DESC;");
    echo "<title>Pre-Harvest Info: " . $userinfo['GrowerName'] . "</title>";
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
<!-- Header -->
<div id="header" class="skel-layers-fixed">

    <div class="top">

        <!-- Logo -->
        <div id="logo">
            <span class="image"><img src="images/avatar.png" alt=""/></span>
            <h1 id="title"><? echo $userinfo['GrowerName'] ?></h1>
            <p><?echo $companyName?> Grower</p>
        </div>

        <!-- Nav -->
        <nav id="nav">
            <ul>
                <li><a href="#top" id="top-link" class="skel-layers-ignoreHref"><span class="icon fa-stethoscope">Pre-Harvest Checkup</span></a>
                </li>
                <li><a href="index.php" id="top-link"><span class="icon fa-arrow-left">Back</span></a></li>
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
                <p>Samples taken from orchards pre-harvest will have their quality information displayed here. If you have fruit you would like us to test, give us a call. Please ensure all samples are clearly marked with block information.</p>
            </header>

            <footer>
                <?echo "<select onchange=\"window.location.search='"."?YearTested='+this.value;\">";
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
                    echo "<tr><td colspan='2'><a href='img.php?q=ID" . $preharvarray['PK'] . "--" . $preharvarray['Date'] . "--starch.jpg'><b><span onclick=\"_paq.push(['trackEvent', 'Pre-Harvest', 'View Starch Photo']);\" class='icon fa-camera'> View Starch Testing Photo</span></b></a></td><td colspan='2'><a href='preharvestcsv.php?ID=" . $preharvarray['PK'] . "&Date=" . $preharvarray['Date'] . $userinfo['GrowerCode'] . "'><b><span onclick=\"_paq.push(['trackEvent', 'Pre-Harvest', 'Download Excel Report']);\" class='icon fa-cloud-download'> Download Complete Pre-harvest Report</span></b></a></td>";
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
        <li>&copy;</li>
    </ul>

</div>

</body>
</html>
