<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/26/17
 * Time: 1:29 PM
 */
require '../config.php';
packapps_authenticate_user('storage');
require_once('../scripts-common/Mobile_Detect.php');
$detect = new Mobile_Detect();
if ($detect->isMobile()){
    die("<script>location.replace('mobile.php')</script>");
}
?>
<!doctype html>
<html lang="en" xmlns:v-bind="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storage Insights</title>

    <!-- Color the status bar on mobile devices -->
    <!--    <meta name="theme-color" content="rgb(0,188,212)">-->
    <link rel="stylesheet" href="../styles-common/material.min.css">
    <!-- Material Design icons -->
    <link rel="stylesheet" href="../styles-common/materialIcons/material-icons.css">
    <!-- Your styles -->
    <link rel="stylesheet" href="../styles-common/styles.css">
    <!-- Sunburst styling -->
    <link rel="stylesheet" href="../styles-common/sunburst/sunburst.css">
</head>
<body>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer
            mdl-layout--fixed-header">
    <header class="mdl-layout__header">
        <div class="mdl-layout__header-row">
            <span style="color:white" class="mdl-layout-title"><i style="vertical-align: text-bottom;" class="material-icons">track_changes</i> Storage Insights</span>
            <div class="mdl-layout-spacer"></div>
            <button id="refreshButton" style="color:white" class="mdl-button mdl-js-button mdl-button--icon">
                <i class="material-icons">sync</i>
            </button>
            <button class="mdl-button mdl-js-button mdl-button--icon">
                <i style="color:white; margin-right:10px" class="material-icons">settings</i>
            </button>
            <button class="mdl-button mdl-js-button mdl-button--icon">
                <a style="color: white; text-decoration: none" href="/" class="material-icons">close</a>
            </button>
        </div>
    </header>
    <!-- Buildings Sidebar -->
    <div id="locationsBar" class="mdl-layout__drawer">
        <span class="mdl-layout-title"></span>
        <!-- Static All Rooms button -->
        <nav class="mdl-navigation">
            <div style="position:relative; overflow:hidden; text-overflow: ellipsis;" v-on:click="updateRoom(0,0,true)" class="mdl-js-ripple-effect mdl-navigation__link">
                <span class="mdl-ripple"></span>
                <div class="availabilityDotGreen"></div>
                All Room View
                <br>
                <div style="font-size: x-small" class="dateSubtitle">
                    Combined View
                </div>
            </div>
        </nav>
        <template v-for="(building, building_index) in buildings">
            <span class="mdl-layout-title">{{ building.building_name }}</span>
            <nav class="mdl-navigation">
                <div style="position:relative;overflow: hidden; text-overflow:ellipsis;" v-for="(room, index) in building.rooms" v-on:click="updateRoom(building_index, index, false)"
                     class="mdl-js-ripple-effect mdl-navigation__link">
                    <span class="mdl-ripple"></span>
                    <div v-bind:class="{ availabilityDotGreen: (room.isAvailable > 0), availabilityDotRed: (room.isAvailable == 0) }"></div>
                    {{ room.room_name }}
                    <br>
                    <div style="font-size: x-small" class="dateSubtitle">
                        <span v-if="(room.isAvailable > 0)">Opened: </span>
                        <span v-else>Closed: </span>
                        {{ room.lastAvailabilityChange }} days ago
                    </div>
                </div>
            </nav>
        </template>
    </div>
    <!-- Actual Data page -->
    <main class="mdl-layout__content">
        <div id="currentRoomStats" class="page-content">
            <div id="topInfoBar" style="text-align: center; border-bottom: 3px solid #e0e0e0;">
                <!-- Room Title -->
                <div style="display: inline-block">
                    <h2>
                        <span v-if="isInAllRoomView">All Rooms</span>
                        <span v-else>{{ locations.buildings[currentBuildingID]['rooms'][currentRoomID]['room_name'] }}</span>
                    </h2>
                </div>
                <!-- Pivot Box -->
                <div v-if="currentRoomHasInventory" id="pivotLists" style="display:inline-block; vertical-align: super;">
                    <div class="wrapper">
                        <div style="position: absolute; right: 40px;">
                            <div v-on:click="togglePivotOptions" style="cursor: pointer; background-color:rgba(0,0,0,.12);padding: 4px; border-radius: 5px" class="mdl-layout__title">
                                <i style="vertical-align: sub" class="material-icons">filter_list</i>
                                Pivot/Filter
                                <i v-bind:class="{ rotate: pivotOptionsIsOpen }" style="vertical-align: middle;" class="material-icons">keyboard_arrow_down</i>
                            </div>
                            <ul id="pivotOptionsList" class="mdl-shadow--6dp mdl-list"
                                style="border-radius: 5px; display: none;margin-bottom:0px; padding-bottom: 0px; margin-top: 5px; padding-top: 5px; background-color: white; z-index: 99">
                                <!--Static item to reset-->
                                <li v-if="pivotOptionsIsDirty" style="transition: visibility 0s, opacity 0.5s linear;" class="mdl-list__item">
                                    <span style="white-space: nowrap" class="mdl-list__item-primary-content">
                                        <i v-on:click="initPivotLists(currentRoomStats.updateSunburst)" style="cursor: pointer" class="material-icons mdl-list__item-icon">sync</i>
                                        Reset Filter
                                    </span>
                                </li>
                                <draggable @update="pivotUpdated" v-model="pivotLists.Delivered">
                                    <li v-for="pivotField in pivotLists.Delivered" class="mdl-list__item">
                                    <span class="mdl-list__item-primary-content">
                                        <i style="cursor:move" class="material-icons mdl-list__item-icon">drag_handle</i>
                                        {{ pivotField.name }}
                                    </span>
                                        <a class="mdl-list__item-secondary-action" v-on:click="removeFromPivotList('Delivered', pivotField.id)"><i class="material-icons">close</i></a>
                                    </li>
                                </draggable>
                            </ul>
                        </div>
                    </div>
                </div>
                <div v-else class="mdl-color--red-100" style="font-size:large;text-align:center; padding:10px">
                    <i style="vertical-align: middle;" class="material-icons">error_outline</i>
                    <b> This view contains no inventory.</b>
                </div>
            </div>
            <div v-pre id="sunburst_wrapper" style="text-align: center;width: 100%">
                <div v-pre id="sequence"></div>
                <div v-pre id="sunburst" style="display:inline-block">
                    <div v-pre style="" id="chart">
                        <div v-pre id="explanation" style="visibility: hidden;">
                            <span id="bushel_total"></span><br/>
                            Total Bushels<br/>
                            <span><b><span id="percentage"></span></b> of this view</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--                        <div style=" border-top:3px solid #e0e0e0; margin-top:15px">-->
            <!--                            <h4>Selection Details</h4>-->
            <!--                        </div>-->
        </div>
    </main>
</div>
<div id='snackbar' style='z-index: 100' class="mdl-js-snackbar mdl-snackbar">
    <div class="mdl-snackbar__text"></div>
    <button class="mdl-snackbar__action" type="button"></button>
</div>
</body>
<script src="../scripts-common/material.min.js"></script>
<script src="../scripts-common/vue.min.js"></script>
<script src="../scripts-common/Sortable.min.js"></script>
<script src="../scripts-common/vuedraggable.min.js"></script>
<script src="../scripts-common/jquery.min.js"></script>
<script src="../scripts-common/sunburst/d3.js"></script>
<script src="sequences.js"></script>
<script language="JavaScript">
    //Vue instances
    var locations = new Vue({
        el: "#locationsBar",
        data: {
            buildings: []
        },
        methods: {
            updateRoom: function (building_id, room_id, isAllRooms) {
                changeActiveRoom(building_id, room_id, isAllRooms);
            }
        },
        mounted: function () {
            var self = this;
            $.getJSON('API/getRooms.php', function (data) {
                self.buildings = data;
            });
        },
        updated: function () {
            componentHandler.upgradeDom();
        }
    });

    var currentRoomStats = new Vue({
        el: "#currentRoomStats",
        data: {
            pivotLists: [],
            fieldID2fieldName: {},
            pivotOptionsIsOpen: false,
            pivotOptionsIsDirty: false,
            currentRoomHasInventory: false,
            currentBuildingID: 0,
            currentRoomID: 0,
            isInAllRoomView: true,
            locations: locations
        },
        mounted: function () {
            this.initPivotLists(this.updateSunburst);
        },
        methods: {
            initPivotLists: function (callback) {
                var self = this;
                var data = {};
                if (!this.isInAllRoomView) {
                    data.room_id = this.currentRoomID;
                }
                $.getJSON('API/getPivotLists.php', data, function (json) {
                    self.pivotLists = json;
                    self.pivotOptionsIsDirty = false;
                    for (var list in json) {
                        if (json.hasOwnProperty(list)) {
                            for (var item in json[list]) {
                                if (json[list].hasOwnProperty(item)) {
                                    self.fieldID2fieldName[json[list][item]['id']] = json[list][item]['name'];
                                }
                            }
                        }
                    }
                    callback();
                });
            },
            togglePivotOptions: function () {
                this.pivotOptionsIsOpen = !this.pivotOptionsIsOpen;
                $('#pivotOptionsList').slideToggle();
            },
            pivotUpdated: function () {
                this.pivotOptionsIsDirty = true;
                this.updateSunburst();
            },
            removeFromPivotList: function (list, id) {
                for (var listItemIndex in this.pivotLists[list]) {
                    if (this.pivotLists[list].hasOwnProperty(listItemIndex)) {
                        if (this.pivotLists[list][listItemIndex]['id'] === id) {
                            this.pivotOptionsIsDirty = true;
                            this.pivotLists[list].splice(listItemIndex, 1);
                            this.updateSunburst();
                            return;
                        }
                    }
                }
            },
            updateSunburst: function () {
                $('#chart').find('svg').remove();
                $('#sequence').find('svg').remove();
                //Get data and graph
                var data = {};
                if (!this.isInAllRoomView) {
                    data.room_id = this.currentRoomID;
                }
                if (this.pivotOptionsIsDirty) {
                    for (var list in this.pivotLists) {
                        if (this.pivotLists.hasOwnProperty(list)) {
                            data['ordering_' + list] = [];
                            for (var pivotField in this.pivotLists[list]) {
                                data['ordering_' + list].push(this.pivotLists[list][pivotField]['id']);
                            }
                            data['ordering_' + list] = JSON.stringify(data['ordering_' + list]);
                        }
                    }
                }
                var self = this;
                $.getJSON('API/getRoomContents.php', data, function (json) {
                    if (Object.keys(json.children).length > 0) {
                        self.currentRoomHasInventory = true;
                        createVisualization(json);
                    } else {
                        self.currentRoomHasInventory = false;
                    }
                }).error(function () {
                    snack('Server Communication Error.', 4000);
                });
            }
        }
    });

    //non-vue functions

    function changeActiveRoom(building_id, room_id, all_rooms) {
        if (all_rooms == false) {
            currentRoomStats.isInAllRoomView = false;
            currentRoomStats.currentBuildingID = building_id;
            currentRoomStats.currentRoomID = room_id;
        } else {
            currentRoomStats.isInAllRoomView = true;
        }
        currentRoomStats.initPivotLists(currentRoomStats.updateSunburst);
    }

    function snack(message, length) {
        var data = {
            message: message,
            timeout: length
        };
        document.querySelector('#snackbar').MaterialSnackbar.showSnackbar(data);
    }

    //refresh button
    $('#refreshButton').on('click', function () {
        currentRoomStats.initPivotLists(currentRoomStats.updateSunburst);

    });

</script>
</html>