<!DOCTYPE HTML>
<html>
<head>
    <?php
    include '../../config.php';
    $userinfo = packapps_authenticate_grower();
    require_once '../../scripts-common/Mobile_Detect.php';
    require_once 'incrementYearInDB.php';
    $detect = new Mobile_Detect;
    $year = new Year();
    if (!$year->isCurrent($mysqli)) {
        error_log("First access since new year, incrementing year.");
        $year->increment($mysqli);
    }
    $numPreHarvest = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) AS count FROM (SELECT PK FROM `grower_Preharvest_Samples` WHERE Grower= '" . $userinfo['GrowerCode'] . "' AND `Date` >= (NOW() - INTERVAL 7 DAY) GROUP BY `Date`, `PK`) t1"))['count'];

    echo "<title>" . $companyName . " Portal: " . $userinfo['GrowerName'] . "</title>";
    ?>

    <link rel="stylesheet" href="css/select2.min.css">
    <link rel="stylesheet" href="css/grid.css">
    <!--[if lte IE 8]>
    <script src="css/ie/html5shiv.js"></script>
    <![endif]-->
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
            <p><? echo $companyName ?><br></p>
        </div>
        <!-- Nav -->
        <nav id="nav">
            <ul>
                <li><a href="#top" id="top-link" class="skel-layers-ignoreHref"><span
                                class="icon fa-home">Home</span></a></li>
                <li><a href="#receiving" id="receiving-link" class="skel-layers-ignoreHref"><span class="icon fa-truck">Received Shipments</span></a>
                </li>
                <li><a href="#blockManagement" id="blockManagement-link" class="skel-layers-ignoreHref"><span
                                class="icon fa-sliders">Blocks & Estimates</span></a></li>
                <li>
                    <hr style='width: 75%; margin-top: 0; margin-bottom:0; border-top: solid 1px rgba(255,255,255,0.5)'>
                </li>
                <li <?php echo($detect->isMobile() ? "style='display: none'" : '') ?>><a href="growerCalendar.php" id="top-link" class="skel-layers-ignoreHref"><span class="icon fa-calendar">Picking Calendar</span></a>
                </li>
                <li><a href="QAview.php"><span class="icon fa-area-chart">Block-by-Block QA</span></a>
                </li>
                <li><a href="preharvest.php"><span
                                class="icon fa-stethoscope <? if ($numPreHarvest > 0) {
                                    echo 'phbadge';
                                } ?>" data-badge="<? echo $numPreHarvest ?>">Pre-Harvest Checkup</span></a></li>
                <li><a href="growerfileshare/"><span
                                class="icon fa-cloud-download">Shared Files</span></a></li>
                <? if ($detect->isMobile()) { //Hide Downloads and toggle bar on mobile
                    echo " <div id='options' style='background: rgba(0,0,0,0.15);'>
                                <li><a href='changepw.php'><span  class=\"icon fa-key\">Change Password</span></a></li>
                                <li><a href=\"#\" onclick=\"logout();\" id=\"logout-link\"><span class=\"icon fa-sign-out\">Log Out</span></a></li>
                                </div>";
                } else {
                    echo "<li id='optiontoggle'><a href='#' ><span id='optionstab' class=\"icon fa-chevron-down\">Options</span></a></li>
                                <div id='options' style='display: none; background: rgba(0,0,0,0.15);'>
                                <li><a href='changepw.php'><span  class=\"icon fa-key\">Change Password</span></a></li>
                                <li><a href='csvExport.php' id='export1' class='skel-layers-ignoreHref'><span class='icon fa-download'>Download My Blocks</a></span></li>
                                <li><a href=# onclick=\"logout();\" id=\"logout-link\"><span class=\"icon fa-sign-out\">Logout / Switch User</span></a></li>
                                </div>";
                }
                ?>
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
                <h2 class="alt"><strong><?php echo $userinfo['GrowerName'] ?></strong> Grower Control Panel<br/>
                    at <strong><? echo $companyName ?></strong></h2>
                <p>Access grower tools and information, review your shipments, and track your estimates.</p>
            </header>
            <footer>
                <a href="#blockManagement" class="button scrolly"><span
                            class="icon fa-pencil"> Let's do my Estimates</a>
            </footer>

        </div>
    </section>
    <!--Received Shipments -->
    <section id="receiving" class="receiving three">
        <div id="deliveriesvue" class="container">
            <header>
                <h2 class="alt">Your Shipments to <? echo $companyName ?></h2>
                <h2><span class="icon fa-truck"></span></h2>
            </header>
            <p v-if="deliveries.length >= 75">The last 75 trucks you sent to us.</p>
            <p v-else>All {{deliveries.length}} deliveries you sent to us this year.</p>
            <p>
                <button id="hider"
                        class="button"><span
                            class="icon fa-eye-slash"> View/Hide Receiving Data ({{deliveries.length}} receipts)</span>
                </button>
            </p>
            <div id="longtab" style="display:none">
                <table id="truckDeliveries" border='1px'>
                    <thead>
                    <tr>
                        <th>QA</th>
                        <th><b>Date Received</th>
                        <th><b>Farm</th>
                        <th><b>Block</th>
                        <th><b>Variety</th>
                        <th><b>Strain</th>
                        <th><b>Bins</th>
                        <th><b>Bushels</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(delivery, index) in deliveries">
                        <td>
                            <abbr v-if="delivery.isQATested > 0" v-bind:title="getMouseOverQAPhotoString(delivery.delivery_ID)">
                                <a :href="'<?php echo "//" . $availableBuckets['quality'] . $amazonAWSURL . $companyShortName . "-quality-rtnum-" ?>' + delivery.delivery_ID + '.jpg'"
                                   class="icon fa-camera">
                                </a>
                            </abbr>
                            <abbr v-else title="This one hasn't been tested yet.">
                                <span class="fa-stack">
                                        <i class="fa fa-camera fa-stack-1x"></i>
                                        <i class="fa fa-ban fa-stack-2x" style="color: #d9534f"></i>
                                </span>
                            </abbr>
                        </td>
                        <td>
                            {{delivery.date}}
                        </td>
                        <td>
                            {{delivery.farmName}}
                        </td>
                        <td>
                            <abbr v-if="delivery.BlockIsDeleted > 0" title="This block has been marked as deleted.">
                                <i class="fa fa-exclamation-triangle"></i>
                            </abbr>
                            {{delivery.BlockDesc}}
                        </td>
                        <td>
                            <abbr :title="delivery.commodity_name">
                                <img :src="'images/' + delivery.commodity_name + '.png'" height="25px" width="25px"/>
                            </abbr>
                            {{delivery.VarietyName}}
                        </td>
                        <td>
                            {{delivery.strainName}}
                        </td>
                        <td>
                            {{delivery.bins_quantity}}
                        </td>
                        <td>
                            {{delivery.bushelsTotal}}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <!-- Crop Estimates -->
    <section id="blockManagement" class="two">
        <div style="vertical-align: middle;">
            <header>
                <h2>Deliveries and Estimates: Season <? echo date('Y') ?></h2>
            </header>
            <h3>
                <span v-if="curSelectionMode > 0" title="Back" class="icon fa-level-up" style="cursor: pointer;" v-on:click="goBackSelectionView()"></span>
                <span v-else class="icon fa-th"></span>
                {{selectionPanelTitle}}
            </h3>
            <hr width="85%">
            <div id="farm_comm_var_block_management_panel">
                <!--Farms-->
                <div v-if="curSelectionMode === 0" class="mdl-grid" id="farmSelectionView">
                    <div v-for="(farm, farm_id) in blockManagementTree['farms']" v-on:click="selectFarm(farm_id)" class="farm_comm_var_selector mdl-cell mdl-cell--4-col mdl-shadow--4dp">
                        <div style="display: inline-block">
                            <h3 style="display: inline-block">{{ farm.name }} </h3>
                            <i class="fa fa-edit" v-on:click="renameFarm(farm.ID, farm.name)"></i>
                        </div>
                        <hr style="width: 85%">
                        <div class="fcv_selector_info_wrapper">
                            <div class="delivery_progress">
                                {{Number(farm.bushelsReceived).toLocaleString()}} bushels delivered
                                <div class="noload">
                                    <div :style="{width: getDeliveryCompletionPercentage(farm.bushelsReceived, farm.bushelsAnticipated) + '%'}" class="load"></div>
                                </div>
                            </div>
                            <div v-if="farm.estimatesNeeded > 0" class="alert_estimates_pending mdl-shadow--2dp">
                                <i class="fa fa-lg fa-exclamation-circle"></i>
                                {{farm.estimatesNeeded}} estimates pending.
                            </div>
                            <div>{{Number(farm.bushelsAnticipated).toLocaleString()}} bushels expected yield
                            </div>
                        </div>
                    </div>
                </div>
                <!--Commodities-->
                <div v-if="curSelectionMode === 1" class="mdl-grid" id="commSelectionView">
                    <div v-for="(comm, comm_id) in blockManagementTree['farms'][curFarmIndex]['commodities']" v-on:click="selectCommodity(comm_id)" class="farm_comm_var_selector mdl-cell mdl-cell--4-col mdl-shadow--4dp">
                        <div style="display: inline-block">
                            <h3 style="display: inline-block">{{ comm.name }} </h3>
                        </div>
                        <hr style="width: 85%">
                        <div class="fcv_selector_info_wrapper">
                            <div class="delivery_progress">
                                {{Number(comm.bushelsReceived).toLocaleString()}} bushels delivered
                                <div class="noload">
                                    <div :style="{width: getDeliveryCompletionPercentage(comm.bushelsReceived, comm.bushelsAnticipated) + '%'}" class="load"></div>
                                </div>
                            </div>
                            <div v-if="comm.estimatesNeeded > 0" class="alert_estimates_pending mdl-shadow--2dp">
                                <i class="fa fa-lg fa-exclamation-circle"></i>
                                {{comm.estimatesNeeded}} estimates pending.
                            </div>
                            <div>{{Number(comm.bushelsAnticipated).toLocaleString()}} bushels expected yield
                            </div>
                        </div>
                    </div>
                </div>
                <!--Varieties-->
                <div v-if="curSelectionMode === 2" class="mdl-grid" id="varietySelectionView">
                    <div v-for="(variety, variety_id) in blockManagementTree['farms'][curFarmIndex]['commodities'][curCommodityIndex]['varieties']" class="farm_comm_var_selector mdl-cell mdl-cell--4-col mdl-shadow--4dp">
                        <div style="display: inline-block">
                            <h3 style="display: inline-block">{{ variety.name }} </h3>
                        </div>
                        <hr style="width: 85%">
                        <div class="fcv_selector_info_wrapper">
                            <div class="delivery_progress">
                                {{Number(variety.bushelsReceived).toLocaleString()}} bushels delivered
                                <div class="noload">
                                    <div :style="{width: getDeliveryCompletionPercentage(variety.bushelsReceived, variety.bushelsAnticipated) + '%'}" class="load"></div>
                                </div>
                            </div>
                            <div v-if="variety.estimatesNeeded > 0" class="alert_estimates_pending mdl-shadow--2dp">
                                <i class="fa fa-lg fa-exclamation-circle"></i>
                                {{variety.estimatesNeeded}} estimates pending.
                            </div>
                            <div>{{Number(variety.bushelsAnticipated).toLocaleString()}} bushels expected yield
                            </div>
                        </div>
                    </div>
                </div>
                <!--Blocks-->
            </div>
            <div id="add_new_item_boxes">
                <button id="hider3" class="button" style="text-align: left"><span
                            class="icon fa-plus"> New Block</span></button>
                <div id="addblockpanel" style="display: none">
                    <form name="newBlock" action="addBlock.php" method="post">
                        <table style="margin:5px; width: calc(100% - 15px);" border='1px'>
                            <thead>
                            <tr>
                                <th><b>Fruit Type</th>
                                <th><b>Variety</th>
                                <th><b>Strain</th>
                            </tr>
                            </thead>
                            <tr>
                                <td style="white-space: nowrap" id="commoditiesRadios">
                                    <!--Radios go here-->
                                </td>
                                <td><select style="width: 100%" name="VarDesc" id="autovar" required></select></td>
                                <td><select style="width: 100%" name="Strain" id="autostr" required></select></td>
                            </tr>
                            <tr>
                                <th><b>Farm</th>
                                <th><b>Block</th>
                                <th><b>'<? echo date('y') ?> Estimate</th>
                            </tr>
                            <tr>
                                <td><input type="text" name="Farm" style='width:150px;'></td>
                                <td><input type="text" name="Block" style='width:150px;'></td>
                                <td><input type="number" name="newEst" style='width:110px;' placeholder="Bushels"
                                           required></td>
                            </tr>
                        </table>
                        <input type=submit value="Submit New Block">
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Footer -->
<div id="footer">
    <!-- Copyright -->
    <ul class="copyright">
        <li>&copy; PackerCloud 2015 - <?= date('Y') ?></li>
    </ul>
</div>

</body>
<script src="js/jquery.min.js"></script>
<script src="js/notify.min.js"></script>
<script src="js/jquery.scrolly.min.js"></script>
<script src="js/jquery.scrollzer.min.js"></script>
<script src="js/skel.min.js"></script>
<script src="js/skel-layers.min.js"></script>
<script src="js/init.js"></script>
<script src="js/select2.min.js"></script>
<script src="../../scripts-common/vue.min.js"></script>
<script>
    //vues
    var deliveriesTableVue = new Vue({
        el: "#deliveriesvue",
        data: {
            deliveries: []
        },
        methods: {
            getMouseOverQAPhotoString: function (number) {
                return "View Photo for delivery #" + number;
            }
        },
        mounted: function () {
            var self = this;
            $.getJSON('API/getShipments.php', function (data) {
                self.deliveries = data;
            });
        }
    });

    var blockManagementVue = new Vue({
        el: "#blockManagement",
        data: {
            blockManagementTree: {},
            curSelectionMode: 0, //0 for farm select, 1 for commodity, 2 for variety, 3 for blocks
            //indexes are from the returned JSON, not their unique IDs
            curFarmIndex: -1,
            curCommodityIndex: -1,
            curVarietyIndex: -1,
            selectionPanelTitle: " Your Farms"
        },
        methods: {
            goBackSelectionView: function(){
                switch (this.curSelectionMode){
                    case 0:
                        break;
                    case 1:
                        this.restoreCleanState();
                        break;
                    case 2:
                        this.selectFarm(this.curFarmIndex);
                        break;
                    case 3:
                        this.selectCommodity(this.curCommodityIndex);
                        break;
                }
            },
            restoreCleanState: function(){
              this.selectionPanelTitle = " Your Farms";
              this.curSelectionMode = 0;
              this.curFarmIndex = -1;
              this.curCommodityIndex = -1;
              this.curVarietyIndex = -1;
            },
            selectFarm: function(farmIndex){ // go to commodity view
                this.selectionPanelTitle = this.blockManagementTree['farms'][farmIndex]['name'] + " Farm's Commodities";
                this.curFarmIndex = farmIndex;
                this.curSelectionMode = 1;
                this.curCommodityIndex = -1;
                this.curVarietyIndex = -1;
            },
            selectCommodity: function(commID){ // go to variety view
                this.selectionPanelTitle = this.blockManagementTree['farms'][this.curFarmIndex]['name'] + " Farm's " + this.blockManagementTree['farms'][this.curFarmIndex]['commodities'][commID]['name'] + " Varieties";
                this.curCommodityIndex = commID;
                this.curVarietyIndex = -1;
                this.curSelectionMode = 2;
            },
            selectVariety: function(varietyID){
                //TODO
            },
            getDeliveryCompletionPercentage: function (delivered, anticipated) {
                if (delivered === 0 || anticipated === 0) {
                    return 0;
                } //Avoid NaNs
                var percentage = (delivered / anticipated) * 100;
                if (percentage > 100) {
                    return 100;
                }
                return percentage;
            },
            renameFarm: function(farmID, curName){
                var newName = prompt("Rename this farm to: ", curName);
                this.renameLand('farm', farmID, newName);
                event.stopPropagation();
            },
            renameBlock: function(blockPK, curName){
                var newName = prompt("Rename this block to: ", curName);
                this.renameLand('block', blockPK, newName);
            },
            renameLand: function (landType, landID, newName) {
                if (landType === 'farm' || landType === 'block') {
                    var self = this;
                    var argsObj = {
                        landType: landType,
                        landID: landID,
                        newName: newName
                    };
                    $.getJSON('API/renameLand.php', argsObj, function (data) {
                        if (landType === 'farm') {
                            $.each(self.blockManagementTree['farms'], function(farmindex, farm){
                                if(farm.ID === landID){
                                    farm.name = newName;
                                    return false; //break loop
                                }
                                return true;
                            });
                        } else if (landType === 'block') {
                            $.each(self.blockManagementTree['farms'][data['farmID']]['commodities'][data['commodityID']]['varieties'][data['variety_ID']]['blocks'], function(block){
                                if(block.ID === data['PK']){
                                    block.BlockDesc = newName;
                                    return false; //break loop
                                }
                                return true;
                            });
                        }
                    }).fail(function () {
                        $.notify("Couldn't rename that " + landType + ".")
                    });
                } else {
                    console.log("Invalid landType")
                }
            },
            toggleFinished: function (PK, isFinished) { //TODO REWRITE
                var self = this;
                $.get('processBlock.php', {Done: PK}, function (data) {
                    if (isFinished > 0) {
//                        console.log(self.percentages[PK]['isFinished']);
                        self.percentages[PK]['isFinished'] = 0;
//                        console.log(self.percentages[PK]['isFinished']);
                        $.notify('Thanks! We\'ll open that back up.', 'success');
                    } else {
//                        console.log(self.percentages[PK]['isFinished']);
                        self.percentages[PK]['isFinished'] = 1;
//                        console.log(self.percentages[PK]['isFinished']);
                        $.notify('Thanks! We won\'t expect any more from that block.', 'success');
                    }
                });
            }
        },
        mounted: function () {
            var self = this;
            $.getJSON('API/getBlocksAndMetadata.php', function (data) {
                self.blockManagementTree = data;
            });
        }
    });

    var CommoditiesTree = {};
    $(document).ready(function () {
        getCommoditiesTree();
        //attach listeners
        $("#hider").click(function () {
            $("#longtab").slideToggle();
        });
        //todo remove
        $("#hider2").click(function () {
            $("#longtab2").slideToggle();
        });
        //todo remove
        $("#hider3").click(function () {
            $("#addblockpanel").slideToggle();
        });
        $("#optiontoggle").click(function () {
            $("#options").slideToggle();
            $("#optionstab").toggleClass("fa-chevron-down fa-minus");
        });
    });
    //attach event listeners to estimates table
    //TODO remove and vue-ify
    $(document).on("change", ".estimatesubmitter, .blocknamer", function () {
        $.post("processEstimates.php", $(this).serialize(), function () {
            $.notify("Information Received", "success");
        });
        return false;
    });

    function getCommoditiesTree() { // for populating/controlling the add block dialog
        $.getJSON('API/getGlobalCommoditySubTypes.php', function (data) {
            CommoditiesTree = data;
            $("#commoditiesRadios").empty();
            $.each(CommoditiesTree, function (key, commodity) {
                $("#commoditiesRadios").append(
                    "<span><input type='radio' class='commodityRadio' name='CommDesc' value='" + commodity['commodity_ID'] + "' required>" + commodity['commodity_name'] + "</span>"
                );
            });
            varSelector = $('#autovar');
            strSelector = $('#autostr');
            varSelector.select2({
                placeholder: 'Varieties',
                disabled: true
            });
            strSelector.select2({
                placeholder: 'Strains',
                disabled: true
            });
            $(".commodityRadio").on('click', function (event) {
                //reset and load varieties and strains for commodity
                if (varSelector.hasClass("select2-hidden-accessible")) {
                    varSelector.select2('destroy');
                    varSelector.empty().append("<option></option>");
                }
                if (strSelector.hasClass("select2-hidden-accessible")) {
                    strSelector.select2('destroy');
                    strSelector.empty().append("<option></option>");
                    strSelector.select2({
                        placeholder: 'Strains',
                        disabled: true
                    });
                }
                var curCommodity = event.target.defaultValue;
                varSelector.off();
                strSelector.off();
                var curVariety = null;
                varSelector.select2({
                    placeholder: "Select Variety",
                    disabled: false,
                    //select2 only takes arrays, no objects allowed
                    data: Object.keys(CommoditiesTree[curCommodity]['Varieties']).map(function (key) {
                        return CommoditiesTree[curCommodity]['Varieties'][key]
                    })
                }).on('select2:select', function (event) {
                    //reset and load Strains for chosen variety
                    curVariety = event.params.data.id;
                    if (strSelector.hasClass("select2-hidden-accessible")) {
                        strSelector.select2('destroy');
                        strSelector.off();
                        strSelector.empty().append("<option></option>");
                    }
                    strSelector.select2({
                        placeholder: 'Select Strain',
                        disabled: false,
                        data: Object.keys(CommoditiesTree[curCommodity]['Varieties'][curVariety]['Strains']).map(function (key) {
                            return CommoditiesTree[curCommodity]['Varieties'][curVariety]['Strains'][key]
                        })
                    });
                });
            });
        });
    }

    function logout() {
        document.cookie = "auth=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
        document.cookie = "username=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
        document.cookie = "grower=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
        window.location.replace('/');
    }
</script>
</html>
