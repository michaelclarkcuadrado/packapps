<!DOCTYPE HTML>
<html>
<head>
    <?php
    include '../../config.php';
    $userinfo = packapps_authenticate_grower();
    $preharvestdata = mysqli_query($mysqli, "
    SELECT
      PK,
      DATE_FORMAT(grower_Preharvest_tests.Date, '%W %M %e %Y') as DateTested,
          farmName,
          BlockDesc,
          commodity_name,
          VarietyName,
          strainName,
          FORMAT(AVG(Starch),2) AS Starch,
          FORMAT(AVG(Brix),2) AS Brix,
          FORMAT(AVG(DAAverage),2) AS DA,
          FORMAT(MIN(DAAverage),2) AS LDA,
          FORMAT(MAX(DAAverage),2) AS HDA,
          FORMAT(AVG((Pressure1+grower_Preharvest_Samples.Pressure2)/2),2) AS Pressure,
          FORMAT(AVG(Weight),2) as Weight,
          Retain,
          concat(left(`Real Name`, (instr(`Real Name`,' ')+1)), '.') AS Inspector,
          IFNULL(NULLIF(Notes, ''), 'No Comment.') AS Notes
        FROM `grower_crop-estimates`
        JOIN grower_farms ON `grower_crop-estimates`.farmID = grower_farms.farmID
        JOIN grower_strains ON `grower_crop-estimates`.strainID = grower_strains.strain_ID
        JOIN grower_varieties ON grower_strains.variety_ID = grower_varieties.VarietyID
        JOIN grower_commodities ON grower_varieties.commodityID = grower_commodities.commodity_ID
        JOIN grower_Preharvest_tests ON `grower_crop-estimates`.PK = grower_Preharvest_tests.block_PK
        JOIN grower_Preharvest_Samples ON grower_Preharvest_tests.test_id = grower_Preharvest_Samples.test_id
          JOIN grower_GrowerLogins ON grower_farms.growerID = grower_GrowerLogins.GrowerID
          JOIN quality_UserData ON Inspector = UserName
          JOIN packapps_master_users ON grower_Preharvest_tests.Inspector = packapps_master_users.username
          WHERE GrowerCode='" . $userinfo['GrowerCode'] . "'
           AND YEAR(grower_Preharvest_tests.Date) = '".((isset($_GET['YearTested'])) ? $_GET['YearTested'] : date('Y'))."'
           AND isStarchInspected > 0
        GROUP BY grower_Preharvest_tests.test_id
    ");
    echo "<title>Pre-Harvest Info: " . $userinfo['GrowerName'] . "</title>";
    ?>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="description" content="<?echo $companyName?> Grower Control Panel"/>
    <meta name="keywords" content=""/>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.scrolly.min.js"></script>
    <script src="js/init.js"></script>
</head>
<body>
<!-- Header -->
<div id="header" class="skel-layers-fixed">

    <div class="top">

        <!-- Logo -->
        <div id="logo">
<!--            <span class="image"><img src="images/avatar.png" alt=""/></span>-->
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
                    $preharvestAWSURL = '//'.$availableBuckets['quality'].$amazonAWSURL.$companyShortName."-quality-preharvest-ID".$preharvarray['PK']."-starch.jpg";
                    echo "<span class='icon fa-eyedropper'><b> Block ID#: </b>" . $preharvarray['PK'] . "<b> -- Tested On: </b>" . $preharvarray['DateTested'] . "</span>";
                    echo "<table border='1px'>";
                    echo "<tr><td><b>Farm</td><td><b>Block</td><td><b>Variety</td><td><b>Strain</td></tr>";
                    echo "<tr><td>" . $preharvarray['farmName'] . "</td><td>" . $preharvarray['BlockDesc'] . "</td><td><img src='images/" . $preharvarray['commodity_name'] . ".png'> " . $preharvarray['VarietyName'] . "</td><td>" . $preharvarray['strainName'] . "</td></tr>";
                    echo "<tr><td><b>Avg. Starch</td><td><b>Avg. Brix</td><td><b>Avg. DA</td><td><b>Avg. Pressure (lb)</td></tr>";
                    echo "<tr><td>" . $preharvarray['Starch'] . "</td><td>" . $preharvarray['Brix'] . "</td><td>" . $preharvarray['DA'] . "<br>(range: " . $preharvarray['LDA'] . " to " . $preharvarray['HDA'] . ")</td><td>" . $preharvarray['Pressure'] . "</td></tr>";
                    echo "<tr><td><b>Avg. Weight (lb)</td><td><b>Treated w/ Retain</td><td colspan='2'><b>Inspector Name</b></td></tr>";
                    echo "<tr><td>" . $preharvarray['Weight'] . "</td><td>" . (($preharvarray['Retain'] == 'No') ? "<span class='icon fa-times'> No</span> " : "<span class='icon fa-check'> Yes</span>") . "</td><td colspan='2'>" . $preharvarray['Inspector'] . "</td></tr>";
                    echo "<tr><td colspan='2'><a href='". $preharvestAWSURL ."'><b><span class='icon fa-camera'> View Starch Testing Photo</span></b></a></td><td colspan='2'><a href='preharvestcsv.php?ID=" . $preharvarray['PK'] . "&Date=" . $preharvarray['Date'] . $userinfo['GrowerCode'] . "'><b><span class='icon fa-cloud-download'> Download Complete Pre-harvest Report</span></b></a></td>";
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
