<?php
include '../../config.php';
$userinfo = packapps_authenticate_grower();
require_once '../../scripts-common/Mobile_Detect.php';
$detect = new Mobile_Detect;
$numPreHarvest = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) AS count FROM (SELECT PK FROM `grower_Preharvest_Samples` WHERE Grower= '" . $userinfo['GrowerCode'] . "' AND `Date` >= (NOW() - INTERVAL 7 DAY) GROUP BY `Date`, `PK`) t1"))['count'];
?>
<!DOCTYPE HTML>
<html xmlns:v-on="http://www.w3.org/1999/xhtml">
<head>
    <title>Grower Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/grid.css">
    <link rel="stylesheet" href="css/select2.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.scrolly.min.js"></script>
    <script src="js/init.js"></script>
    <script src="../../scripts-common/vue.min.js"></script>
</head>
<body>
<!-- Header -->
<div id="header" class="skel-layers-fixed">
    <div class="top">
        <!-- Logo -->
        <div id="logo">
            <!--            <span class="image"><img src="images/avatar.png" alt=""/></span>-->
            <h1 id="title"><? echo $userinfo['GrowerName'] ?></h1>
            <p>@<? echo $companyName ?><br></p>
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
                <li <?php echo($detect->isMobile() ? "style='display: none'" : '') ?>><a href="growerCalendar.php" class="skel-layers-ignoreHref"><span class="icon fa-calendar">Picking Calendar</span></a>
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
    <div class="bottom">
        <ul class="icons">
            <!--            <li style="color: rgba(255, 255, 255, 0.5);font-size: small;">Powered By <a href="//packercloud.com">PackerCloud</a></li>-->
        </ul>
    </div>
</div>
<!-- Main -->
<div id="main">
    <!-- Intro -->
    <section id="top" class="one dark cover" style="background-image: url('images/banner.jpg')">
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
                            <abbr v-else title="No QA data available yet.">
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
                            <abbr v-if="delivery.BlockIsDeleted > 0" title="This block has been marked as retired.">
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
            <h3 id="scrollToOnUpdateTarget">
                <span class="icon fa-th"></span>
                {{selectionPanelTitle}}
            </h3>
            <hr width="85%">
            <div style="display: flex; justify-content: center;" id="selectorButtons">
                <!-- Add new item buttons -->
                <button v-if="curSelectionMode === 0" class="button" v-on:click="newFarm()" id="add_new_farm">
                    <span class="icon fa-plus"> New Farm</span>
                </button>
                <button v-else class="button" onclick="$('#modalWrapper').addClass('modalOpen');">
                    <span class="icon fa-plus"> New Block</span>
                </button>
                <button v-if="curSelectionMode > 0" class="button" v-on:click="goBackSelectionView()">
                    <span class="icon fa-level-up" style="cursor: pointer;"></span> Go Back
                </button>
            </div>
            <div id="farm_comm_var_block_management_panel">
                <transition name="selectors-slide" mode="out-in">
                    <!--Farms-->
                    <div v-if="curSelectionMode === 0" class="mdl-grid" key="farmSelectionView">
                        <div v-for="(farm, farm_id) in blockManagementTree['farms']" v-on:click="selectFarm(farm_id)" class="farm_comm_var_selector mdl-cell mdl-cell--4-col mdl-shadow--4dp">
                            <h3 style="display: inline-block">{{ farm.name }} </h3>
                            <span class="fa fa-edit" title="Rename Farm" v-on:click="renameFarm(farm.ID, farm.name)"></span>
                            <hr style="width: 85%">
                            <div class="fcv_selector_info_wrapper">
                                <div v-if="farm.estimatesNeeded > 0" class="alert_estimates_pending mdl-shadow--2dp">
                                    <i class="fa fa-lg fa-exclamation-circle"></i>
                                    {{farm.estimatesNeeded}} estimates pending.
                                </div>
                                <div class="delivery_progress">
                                    {{Number(farm.bushelsReceived).toLocaleString()}} bushels delivered <span class="smallFont">({{getDeliveryCompletionPercentage(farm.bushelsReceived, farm.bushelsAnticipated).toFixed(2) + '%'}})</span>
                                    <div class="noload">
                                        <span :style="{width: getDeliveryCompletionPercentage(farm.bushelsReceived, farm.bushelsAnticipated) + '%'}" class="load"></span>
                                    </div>
                                </div>
                                <div>{{Number(farm.bushelsAnticipated).toLocaleString()}} bushels expected yield
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Commodities-->
                    <div v-if="curSelectionMode === 1" class="mdl-grid" key="commSelectionView">
                        <div v-for="(comm, comm_id) in blockManagementTree['farms'][curFarmIndex]['commodities']" v-on:click="selectCommodity(comm_id)"
                             class="farm_comm_var_selector mdl-cell mdl-cell--4-col mdl-shadow--4dp">
                            <div style="display: inline-block">
                                <h3 style="display: inline-block"><img :src="'images/'+comm.name+'.png'"> {{ comm.name }} </h3>
                            </div>
                            <hr style="width: 85%">
                            <div class="fcv_selector_info_wrapper">
                                <div v-if="comm.estimatesNeeded > 0" class="alert_estimates_pending mdl-shadow--2dp">
                                    <i class="fa fa-lg fa-exclamation-circle"></i>
                                    {{comm.estimatesNeeded}} estimates pending.
                                </div>
                                <div class="delivery_progress">
                                    {{Number(comm.bushelsReceived).toLocaleString()}} bushels delivered <span class="smallFont">({{getDeliveryCompletionPercentage(comm.bushelsReceived, comm.bushelsAnticipated).toFixed(2) + '%'}})</span>
                                    <div class="noload">
                                        <span :style="{width: getDeliveryCompletionPercentage(comm.bushelsReceived, comm.bushelsAnticipated) + '%'}" class="load"></span>
                                    </div>
                                </div>
                                <div>{{Number(comm.bushelsAnticipated).toLocaleString()}} bushels expected yield
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Varieties-->
                    <div v-if="curSelectionMode === 2" class="mdl-grid" key="varietySelectionView">
                        <div v-for="(variety, variety_id) in blockManagementTree['farms'][curFarmIndex]['commodities'][curCommodityIndex]['varieties']" v-on:click="selectVariety(variety_id)"
                             class="farm_comm_var_selector mdl-cell mdl-cell--4-col mdl-shadow--4dp">
                            <div style="display: inline-block">
                                <h3 style="display: inline-block">{{ variety.name }} </h3>
                            </div>
                            <hr style="width: 85%">
                            <div class="fcv_selector_info_wrapper">
                                <div v-if="variety.estimatesNeeded > 0" class="alert_estimates_pending mdl-shadow--2dp">
                                    <i class="fa fa-lg fa-exclamation-circle"></i>
                                    {{Number(variety.estimatesNeeded).toLocaleString()}} estimates pending.
                                </div>
                                <div class="delivery_progress">
                                    {{Number(variety.bushelsReceived).toLocaleString()}} bushels delivered <span class="smallFont">({{getDeliveryCompletionPercentage(variety.bushelsReceived, variety.bushelsAnticipated).toFixed(2) + '%'}})</span>
                                    <div class="noload">
                                        <span :style="{width: getDeliveryCompletionPercentage(variety.bushelsReceived, variety.bushelsAnticipated) + '%'}" class="load"></span>
                                    </div>
                                </div>
                                <div>{{Number(variety.bushelsAnticipated).toLocaleString()}} bushels expected yield</div>
                                <div style="font-size: smaller"><span class="fa fa-th"></span> {{Number(variety.blocks.filter(block => block.isDeleted == 0).length).toLocaleString() +
                                    (variety.blocks.filter(block => block.isDeleted == 0).length == 1 ? " Block" : " Blocks")}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Blocks-->
                    <div v-if="curSelectionMode === 3" class="mdl-grid" key="blockDetailView">
                        <div v-for="(block, block_id) in blockManagementTree['farms'][curFarmIndex]['commodities'][curCommodityIndex]['varieties'][curVarietyIndex]['blocks']"
                             :class="[block.isDeleted > 0  ? 'block-deleted-bar' : 'block-detail-bar', 'mdl-shadow--4dp', 'mdl-cell', 'mdl-cell--6-col-desktop', 'mdl-cell--4-col-tablet', 'mdl-cell--4-col-phone']">
                            <h3>
                                {{(block.isDeleted > 0 ? '[RETIRED] ' : '') + block.BlockDesc}}
                                <span v-if="blockTrend(block.bushelHistory) > 0" class="fa fa-sort-up" title="Trends Up" style="color: green; vertical-align: sub"></span>
                                <span v-else-if="blockTrend(block.bushelHistory) == 0 || isNaN(blockTrend(block.bushelHistory))" title="No Trend"
                                      style="vertical-align: text-bottom; font-size: 60px">-</span>
                                <span v-else class="fa fa-sort-down" title="Trends Down" style="color: red; vertical-align: top"></span>
                            </h3>
                            <div>
                                <span class="fa fa-edit actionButtons" title="Rename Block" v-on:click="renameBlock(block.PK, block.BlockDesc)"></span>
                                <span v-if="block.isDeleted == 0" class="fa fa-trash actionButtons" title="Retire Block"
                                      v-on:click="retireBlock(block, curFarmIndex, curCommodityIndex, curVarietyIndex)"></span>
                                <span v-else class="fa fa-repeat actionButtons" title="Open Block" v-on:click="retireBlock(block, curFarmIndex, curCommodityIndex, curVarietyIndex)"></span>
                            </div>
                            <transition name="needsEstimateEase">
                                <div v-if="(block['isDeleted'] > 0 ? false : (block['isSameAsLastYear'] > 0 ? false : (block['bushelHistory'][curYear]['est'] == block['bushelHistory'][curYear - 1]['act'] ? true : false)))"
                                     class="alert_estimates_pending mdl-shadow--2dp">
                                    <i class="fa fa-lg fa-exclamation-circle"></i>
                                    Needs Estimate
                                </div>
                            </transition>
                            <div class="deleted-block-blur-wrapper">
                                <div style="display:flex; justify-content: space-evenly;">
                                    <span>Variety: {{block.VarietyName}}</span>
                                    <span>Strain: {{block.strainName}}</span>
                                </div>
                                <div v-if="block.isDeleted == 0" style="border-top: 1px solid black">
                                    <h5>This Year</h5>
                                    <div style="display:flex; margin-left: 5px; margin-right: 5px; flex-wrap: wrap; justify-content: space-evenly">
                                        <div style="flex-basis: 100%; align-self: center">
                                            {{Number(block.bushelsReceived).toLocaleString() + " Out Of " + Number( (block.isFinished > 0 ? block.bushelsReceived : block.bushelHistory[curYear]['est'])
                                            ).toLocaleString()}} Bushels <span class="smallFont">({{getDeliveryCompletionPercentage(block.bushelsReceived, (block.isFinished > 0 ? block.bushelsReceived : block.bushelHistory[curYear]['est']) ).toFixed(2) + '%'}})</span>
                                        </div>
                                        <div style="flex-basis: 100%">
                                            <span class="icon fa-truck"></span>
                                            {{block.deliveriesReceived + (block.deliveriesReceived == 1 ? ' Delivery' : ' Deliveries')}}
                                        </div>
                                        <div style="align-self: center; min-width: 150px" class="noload">
                                            <span class="load"
                                                  v-bind:style="{width: getDeliveryCompletionPercentage(block.bushelsReceived, (block.isFinished > 0 ? block.bushelsReceived : block['bushelHistory'][curYear]['est'])) + '%'}"></span>
                                        </div>
                                        <div style="flex-shrink: 2; align-self: center; display: grid; margin: 5px">
                                            <span v-on:click="toggleFinished(block, curFarmIndex, curCommodityIndex, curVarietyIndex)" style="cursor: pointer;"
                                                  v-bind:class="['fa', block.isFinished > 0 ? 'fa-lock' : 'fa-unlock']"
                                                  :title="block.isFinished > 0 ? 'Open this block' : 'Finish this block'"></span>
                                            <mark v-if="block.isFinished > 0" style="font-size: small; width: fit-content; line-height:initial; justify-self: center">Done For the Season</mark>
                                        </div>
                                    </div>
                                </div>
                                <div style="border-top: 1px solid black;">
                                    <h5>Estimates in Bushels</h5>
                                    <div style="display: flex; justify-content: space-evenly;" class="estimatesHeaderWrapper">
                                        <div v-for="(valueObj, year) in block.bushelHistory" v-if="year != curYear || block.isDeleted > 0" style="flex-grow: 1">
                                            <div>
                                                {{year}} Delivered
                                            </div>
                                            <div style="border-top: 1px solid gray">
                                                {{ (year == curYear ? Number(block.bushelsReceived).toLocaleString() : Number(valueObj.act).toLocaleString()) }}
                                                <span class="smallEstimatedTag">
                                                    Your Estimate: {{Number(valueObj.est).toLocaleString()}}
                                                </span>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <div>
                                                {{year}} Estimated
                                            </div>
                                            <div style="border-top: 1px solid gray">
                                                <input step="1" type="number" v-model.number="valueObj.est" v-on:change="updateEstimate(block, curFarmIndex, curCommodityIndex, curVarietyIndex)"
                                                       :disabled="block['isSameAsLastYear'] > 0" min="0" style="width: 90px; margin: 5px 0;">
                                                <span v-if="(block['bushelHistory'][curYear]['est'] == block['bushelHistory'][curYear - 1]['act'])" class="smallEstimatedTag">
                                                    <div>
                                                        <input v-model="block.isSameAsLastYear" true-value="1" false-value="0"
                                                               v-on:change="updateEstimate(block, curFarmIndex, curCommodityIndex, curVarietyIndex)" type="checkbox"> Keep this number?
                                                    </div>
                                                </span>
                                                <span v-else class="smallEstimatedTag">
                                                        <button v-on:click="resetBlockEstimate(block, curFarmIndex, curCommodityIndex, curVarietyIndex)">Reset</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </section>
</div>
<div id="footer">
    <ul class="copyright">
        <li>&copy; <a href="//packercloud.com">PackerCloud</a> 2015 - <?= date('Y') ?></li>
        <li>Licensed to <?= $companyName ?></li>
        <li>All Rights Reserved.</li>
    </ul>
</div>
<div class="modalWrapper" id="modalWrapper" aria-hidden="true">
    <div class="modal-dialog" id="addblockpanel">
        <form name="newBlock" action="addBlock.php" method="post">
            <h2>Create New Block</h2>
            <table style="margin:5px; width: calc(100% - 15px);" border='1px'>
                <thead>
                <tr>
                    <th><b>Fruit Type</th>
                    <th><b>Variety</th>
                    <th><b>Strain</th>
                </tr>
                </thead>
                <tr>
                    <td style="display:flex" id="commoditiesRadios">
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
</body>
<script src="js/notify.min.js"></script>
<script src="js/select2.min.js"></script>
<script>
    //vues
    const deliveriesTableVue = new Vue({
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
            const self = this;
            $.getJSON('API/getShipments.php', function (data) {
                self.deliveries = data;
            });
        }
    });

    const blockManagementVue = new Vue({
            el: "#blockManagement",
            data: {
                blockManagementTree: {},
                curSelectionMode: 0, //0 for farm select, 1 for commodity, 2 for variety, 3 for blocks
                //indexes are from the returned JSON, not their unique IDs
                curFarmIndex: -1,
                curCommodityIndex: -1,
                curVarietyIndex: -1,
                selectionPanelTitle: " Your Farms",
                curYear: new Date().getFullYear()
            },
            methods: {
                goBackSelectionView: function () {
                    switch (this.curSelectionMode) {
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
                scrollReset: function () {
                    let $scrollTarget = $('#scrollToOnUpdateTarget');
                    $("html, body").animate({
                            scrollTop: $scrollTarget.offset().top
                        }
                    )
                },
                restoreCleanState: function () {
                    this.selectionPanelTitle = " Your Farms";
                    this.curSelectionMode = 0;
                    this.curFarmIndex = -1;
                    this.curCommodityIndex = -1;
                    this.curVarietyIndex = -1;
                    this.scrollReset();
                },
                selectFarm: function (farmIndex) { // go to commodity view
                    this.selectionPanelTitle = this.blockManagementTree['farms'][farmIndex]['name'] + " Farm's Commodities";
                    this.curFarmIndex = farmIndex;
                    this.curSelectionMode = 1;
                    this.curCommodityIndex = -1;
                    this.curVarietyIndex = -1;
                    this.scrollReset();
                },
                selectCommodity: function (commID) { // go to variety view
                    this.selectionPanelTitle = this.blockManagementTree['farms'][this.curFarmIndex]['name'] + " Farm's " + this.blockManagementTree['farms'][this.curFarmIndex]['commodities'][commID]['name'] + " Varieties";
                    this.curCommodityIndex = commID;
                    this.curVarietyIndex = -1;
                    this.curSelectionMode = 2;
                    this.scrollReset();
                },
                selectVariety: function (varietyID) {
                    this.curSelectionMode = 3;
                    this.curVarietyIndex = varietyID;
                    const farm_name = this.blockManagementTree['farms'][this.curFarmIndex]['name'];
                    const variety_name = this.blockManagementTree['farms'][this.curFarmIndex]['commodities'][this.curCommodityIndex]['varieties'][varietyID]['name'];
                    this.selectionPanelTitle = farm_name + " Farm's " + variety_name + " Blocks";
                    this.scrollReset();
                },
                getDeliveryCompletionPercentage: function (delivered, anticipated) {
                    if (delivered == 0) {
                        return 0;
                    }
                    if (delivered != 0 && anticipated == 0) {
                        return 100;
                    }
                    const percentage = (delivered / anticipated) * 100;
                    if (percentage > 100) {
                        return 100;
                    }
                    return percentage;
                },
                newFarm: function () {
                    const self = this;
                    const newFarmName = prompt("New Farm Name:");
                    $.getJSON('API/addFarm.php', {newFarmName: newFarmName}, function (data) {
                        self.blockManagementTree['farms'].push({
                            name: newFarmName,
                            id: data.ID,
                            commodities: [],
                            bushelsReceived: 0,
                            bushelsAnticipated: 0,
                            estimatesNeeded: 0
                        })
                    }).fail(function () {
                        $.notify("Couldn't create that farm.");
                    });
                },
                renameFarm: function (farmID, curName) {
                    const newName = prompt("Rename this farm to: ", curName);
                    this.renameLand('farm', farmID, newName);
                    event.stopPropagation();
                },
                renameBlock: function (blockPK, curName) {
                    const newName = prompt("Rename this block to: ", curName);
                    this.renameLand('block', blockPK, newName);
                },
                renameLand: function (landType, landID, newName) {
                    if (landType === 'farm' || landType === 'block') {
                        const self = this;
                        const argsObj = {
                            landType: landType,
                            landID: landID,
                            newName: newName
                        };
                        $.getJSON('API/renameLand.php', argsObj, function (data) {
                            if (landType === 'farm') {
                                $.each(self.blockManagementTree['farms'], function (farmindex, farm) {
                                    if (farm.ID === landID) {
                                        farm.name = newName;
                                        return false; //break loop
                                    }
                                    return true;
                                });
                            } else if (landType === 'block') { //have to find the block in the tree, search by returned IDs
                                $.each(self.blockManagementTree['farms'], function (farmIndex, farm) {
                                    if (farm.ID == data['farmID']) {
                                        // console.log('farm match');
                                        $.each(farm.commodities, function (commodityIndex, commodity) {
                                            if (commodity.ID == data['commodityID']) {
                                                // console.log('commodity match');
                                                $.each(commodity['varieties'], function (varietyIndex, variety) {
                                                    if (variety.ID == data['variety_ID']) {
                                                        // console.log('variety match');
                                                        $.each(variety['blocks'], function (blockIndex, block) {
                                                            if (block['PK'] == data.PK) {
                                                                // console.log('block match');
                                                                block.BlockDesc = argsObj.newName;
                                                                return false;
                                                            }
                                                            return true;
                                                        });
                                                        return false;
                                                    }
                                                    return true;
                                                });
                                                return false;
                                            }
                                            return true;
                                        });
                                        return false;
                                    }
                                    return true;
                                });
                            }
                        }).fail(function () {
                            $.notify("Couldn't rename that " + landType + ".")
                        });
                    }
                },
                blockTrend: function (bushelHistoryObj) {
                    const years = [];
                    const bushelVals = [];
                    for (let year in bushelHistoryObj) {
                        if (bushelHistoryObj.hasOwnProperty(year)) {
                            years.push(year);
                            if (year == this.curYear) {
                                bushelVals.push(bushelHistoryObj[year]['est']);
                            } else {
                                bushelVals.push(bushelHistoryObj[year]['act']);
                            }
                        }
                    }
                    return this.linearRegressionSlope(bushelVals, years);
                },
                linearRegressionSlope: function (y, x) {
                    y = y.map(num => parseInt(num));
                    x = x.map(num => parseInt(num));
                    const n = y.length;
                    let sum_x = 0;
                    let sum_y = 0;
                    let sum_xy = 0;
                    let sum_xx = 0;
                    let sum_yy = 0;

                    for (let i = 0; i < y.length; i++) {
                        sum_x += x[i];
                        sum_y += y[i];
                        sum_xy += (x[i] * y[i]);
                        sum_xx += (x[i] * x[i]);
                        sum_yy += (y[i] * y[i]);
                    }
                    const m = (n * sum_xy - sum_x * sum_y) / (n * sum_xx - sum_x * sum_x);
                    return m;
                },
                updateEstimate: function (block, farmIndex, commIndex, varIndex) { //pushes block's client-side status to server
                    const self = this;
                    if (block['isSameAsLastYear'] > 0) { //set keep estimate flag
                        let argsObj = {
                            sameAsLastYear: 1,
                            PK: block.PK
                        };
                        $.get('API/updateEstimate.php', argsObj, function (data) {
                            self.recalcParentsExpectedBushels(farmIndex, commIndex, varIndex);
                            $.notify("Info Updated.", "success");
                        }).fail(function () {
                            $.notify("Couldn't contact server.");
                        });
                    } else { //send estimate
                        let argsObj = {
                            bushelVal: block['bushelHistory'][self.curYear]['est'],
                            PK: block.PK
                        };
                        $.get('API/updateEstimate.php', argsObj, function (data) {
                            self.recalcParentsExpectedBushels(farmIndex, commIndex, varIndex);
                            $.notify("Info Updated.", "success");
                        });
                    }
                },
                resetBlockEstimate: function (block, farmIndex, commIndex, varIndex) {
                    const self = this;
                    block['bushelHistory'][self.curYear]['est'] = block['bushelHistory'][self.curYear - 1]['act'];
                    block['isSameAsLastYear'] = 0;
                    self.updateEstimate(block, farmIndex, commIndex, varIndex);
                },
                recalcParentsExpectedBushels: function (farmIndex, commIndex, varIndex) {
                    //TODO recalc bushels, and set estimate needed numbers
                    //varieties
                    let blocks = this.blockManagementTree.farms[farmIndex]['commodities'][commIndex]['varieties'][varIndex]['blocks'];
                    let newVarietyAnticipatedTotal = 0;
                    let newVarietyEstimateNeededTotal = 0;
                    for (let blockID in blocks) {
                        if (blocks.hasOwnProperty(blockID)) {
                            newVarietyAnticipatedTotal += (blocks[blockID].isDeleted > 0 ? 0 : (blocks[blockID].isFinished > 0 ? parseInt(blocks[blockID].bushelsReceived) : parseInt(blocks[blockID].bushelHistory[this.curYear].est)));
                            newVarietyEstimateNeededTotal += (blocks[blockID].isDeleted > 0 ? 0 : (blocks[blockID].isSameAsLastYear > 0 ? 0 : (blocks[blockID]['bushelHistory'][this.curYear]['est'] != blocks[blockID]['bushelHistory'][this.curYear - 1]['act'] ? 0 : 1)));
                        }
                    }
                    this.blockManagementTree.farms[farmIndex]['commodities'][commIndex]['varieties'][varIndex]['bushelsAnticipated'] = newVarietyAnticipatedTotal;
                    this.blockManagementTree.farms[farmIndex]['commodities'][commIndex]['varieties'][varIndex]['estimatesNeeded'] = newVarietyEstimateNeededTotal;
                    //commodities
                    let newCommAnticipatedTotal = 0;
                    let newCommEstimateNeededTotal = 0;
                    let varieties = this.blockManagementTree.farms[farmIndex]['commodities'][commIndex]['varieties'];
                    for (let varID in varieties) {
                        if (varieties.hasOwnProperty(varID)) {
                            newCommAnticipatedTotal += parseInt(varieties[varID]['bushelsAnticipated']);
                            newCommEstimateNeededTotal += parseInt(varieties[varID]['estimatesNeeded']);
                        }
                    }
                    this.blockManagementTree.farms[farmIndex]['commodities'][commIndex]['bushelsAnticipated'] = newCommAnticipatedTotal;
                    this.blockManagementTree.farms[farmIndex]['commodities'][commIndex]['estimatesNeeded'] = newCommEstimateNeededTotal;
                    //farms
                    let comms = this.blockManagementTree.farms[farmIndex]['commodities'];
                    let newFarmAnticipatedtotal = 0;
                    let newFarmEsimateNeededTotal = 0;
                    for (let commID in comms) {
                        if (comms.hasOwnProperty(commID)) {
                            newFarmAnticipatedtotal += comms[commID]['bushelsAnticipated'];
                            newFarmEsimateNeededTotal += comms[commID]['estimatesNeeded'];
                        }
                    }
                    this.blockManagementTree.farms[farmIndex]['bushelsAnticipated'] = newFarmAnticipatedtotal;
                    this.blockManagementTree.farms[farmIndex]['estimatesNeeded'] = newFarmEsimateNeededTotal;
                },
                toggleFinished: function (block, farmIndex, commIndex, varIndex) {
                    const self = this;
                    $.get('API/processBlock.php', {finish: block['PK']}, function (data) {
                        block.isFinished ^= 1;
                        self.recalcParentsExpectedBushels(farmIndex, commIndex, varIndex);
                    });
                },
                retireBlock: function (block, farmIndex, commIndex, varIndex) {
                    const self = this;
                    let argsObj = {
                        deletedFlag: block.isDeleted ^ 1,
                        PK: block.PK
                    };
                    $.get('API/toggleBlockRetirement.php', argsObj, function () {
                        block.isDeleted ^= 1;
                        self.recalcParentsExpectedBushels(farmIndex, commIndex, varIndex);
                    }).fail(function (data) {
                        $.notify("Could not contact server.");
                    });
                }
            },
            mounted: function () {
                const self = this;
                $.getJSON('API/getBlocksAndMetadata.php', function (data) {
                    self.blockManagementTree = data;
                });
            }
        })
    ;

    let CommoditiesTree = {};
    $(document).ready(function () {
        //attach listeners
        getCommoditiesTree();
        $("#hider").click(function () {
            $("#longtab").slideToggle();
        });
        $("#optiontoggle").click(function () { //for desktop-view sidebar
            $("#options").slideToggle();
            $("#optionstab").toggleClass("fa-chevron-down fa-minus");
        });
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
                const curCommodity = event.target.defaultValue;
                varSelector.off();
                strSelector.off();
                let curVariety = null;
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
