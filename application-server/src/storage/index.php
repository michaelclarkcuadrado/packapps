<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/26/17
 * Time: 1:29 PM
 */
require '../config.php';
packapps_authenticate_user('storage');
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
            <i style="color:white; margin-right:10px" class="material-icons">settings</i>
            <button class="mdl-button mdl-js-button mdl-button--icon">
                <a style="color: white; text-decoration: none" href="/" class="material-icons">close</a>
            </button>
        </div>
    </header>
    <div id="locationsBar" class="mdl-layout__drawer">
        <template v-for="(building, building_index) in buildings">
            <span class="mdl-layout-title">{{ building.building_name }}</span>
            <nav class="mdl-navigation">
                <div style="position:relative;overflow: hidden" v-for="(room, index) in building.rooms" v-on:click="updateRoom(index, building_index, false)" class="mdl-js-ripple-effect mdl-navigation__link">
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
    <main class="mdl-layout__content">
        <div class="page-content">
            <!-- Pivot Box -->
            <div id="pivotLists" style="position: absolute; right: 0; top: 0" class="mdl-shadow--6dp">
                <b>Pivot Options </b><i style="vertical-align: middle" class="material-icons">keyboard_arrow_up</i>
            </div>
            <!-- Your content goes here -->
            <div v-pre id="sequence"></div>
            <div v-pre id="sunburst_wrapper" style="text-align: center;width: 100%">
                <div id="sunburst" style="display:inline-block">
                    <div id="chart">
                        <div id="explanation" style="visibility: hidden;">
                            <span id="bushel_total"></span><br/>
                            Total Bushels<br />
                            <span><b><span id="percentage"></span></b> of this room</span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="currentRoomStats">
                <h2 style="text-align: center">
                    <span v-if="isInAllRoomView">All Rooms</span>
                    <span v-else>{{ locations.buildings[currentBuildingID]['rooms'][currentRoomID]['room_name'] }}</span>
                </h2>
            </div>
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
            updateRoom: function(room_id, building_id, isAllRooms){
                changeActiveRoom(building_id, room_id, isAllRooms);
            }
        },
        mounted: function(){
            var self = this;
            $.getJSON('API/getRooms.php', function(data) {
                self.buildings = data;
            });
        },
        updated: function(){
            componentHandler.upgradeDom();
        }
    });
    var pivotOptions = new Vue({
        el: "#pivotLists",
        data: {
            pivotLists: [],
            pivotOptionsIsDirty: false
        },
        mounted: function(){
            this.getPivotLists();
        },
        methods: {
            getPivotLists: function(){
                $.getJSON('API/getPivotLists.php', function(json){
                    this.pivotLists = json;
                });
            },
            pivotUpdated: function(){
                  updateSunburst();
            }
        }
    });
    var currentRoomStats = new Vue({
        el: "#currentRoomStats",
        data: {
            currentBuildingID: 0,
            currentRoomID: 0,
            isInAllRoomView: true,
            currentRoomHasInventory: false,
            locations: locations
        },
        mounted: function(){
            updateSunburst();
        }
    });

    //functions
    function updateSunburst(){
        //TODO destroy old sunburst and unset vars
        //Get data and graph
        var data = {};
//        if(!currentRoomStats.isInAllRoomView){
//            data.room_id = currentRoomStats.currentRoomID;
//        }
        if(pivotOptions.pivotOptionsIsDirty){

        }
        $.getJSON('API/getRoomContents.php', function(json) {
            console.log("AJAX SUCCESS");
            console.log(json);
            if(Object.keys(json.children).length > 0){
                currentRoomStats.currentRoomHasInventory = true;
                createVisualization(json);
            } else {
                currentRoomStats.currentRoomHasInventory = false;
            }
        }).error(function() {
            snack('Server Communication Error.', 4000);
        });
    }

    function changeActiveRoom(building_id, room_id, all_rooms){
        if(all_rooms == false){
            currentRoomStats.isInAllRoomView = false;
            currentRoomStats.currentBuildingID = building_id;
            currentRoomStats.currentRoomID = room_id;
        } else {
            currentRoomStats.isInAllRoomView = true;
        }
        updateSunburst();
    }

    function snack(message, length) {
        var data = {
            message: message,
            timeout: length
        };
        document.querySelector('#snackbar').MaterialSnackbar.showSnackbar(data);
    }
</script>
</html>