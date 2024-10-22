// Dimensions of sunburst.
var width = 750;
var height = 600;
var radius = Math.min(width, height) / 2;

// Breadcrumb dimensions: width, height, spacing, width of tip/tail.
var b = {
    w: 155, h: 30, s: 3, t: 10
};

// Total size of all segments; we set this later, after loading the data.
var totalSize = 0;

var vis = {};
var partition = {};
var arc = {};



// Main function to draw and set up the visualization, once we have the data.
function createVisualization(json) {

    vis = d3.select("#chart").append("svg:svg")
        .attr("width", width)
        .attr("height", height)
        .append("svg:g")
        .attr("id", "container")
        .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

    partition = d3.partition()
        .size([2 * Math.PI, radius * radius]);

    arc = d3.arc()
        .startAngle(function(d) { return d.x0; })
        .endAngle(function(d) { return d.x1; })
        .innerRadius(function(d) { return Math.sqrt(d.y0); })
        .outerRadius(function(d) { return Math.sqrt(d.y1); });
    // Basic setup of page elements.
    // initializeBreadcrumbTrail();

    // Bounding circle underneath the sunburst, to make it easier to detect
    // when the mouse leaves the parent g.
    vis.append("svg:circle")
        .attr("r", radius)
        .style("opacity", 0);

    // Turn the data into a d3 hierarchy and calculate the sums.
    var root = d3.hierarchy(json)
        .sum(function(d) { return d.size; })
        .sort(function(a, b) { return b.value - a.value; });

    // For efficiency, filter nodes to keep only those large enough to see.
    var nodes = partition(root).descendants()
        .filter(function(d) {
            return (d.x1 - d.x0 > 0.005); // 0.005 radians = 0.29 degrees
        });

    var path = vis.data([json]).selectAll("path")
        .data(nodes)
        .enter().append("svg:path")
        .attr("display", function(d) { return d.depth ? null : "none"; })
        .attr("d", arc)
        .attr("fill-rule", "evenodd")
        .style("fill", function(d) { return d.data.color; })
        .style("opacity", 1)
        .on("mouseover", mouseover);

    // Add the mouseleave handler to the bounding circle.
    d3.select("#container").on("mouseleave", mouseleave);

    // Get total size of the tree = value of root node from partition.
    totalSize = path.datum().value;
};

// Fade all but the current sequence, and show it in the breadcrumb trail.
function mouseover(d) {
    var percentage = (100 * d.value / totalSize).toPrecision(3);
    var percentageString = percentage + "%";
    if (percentage < 0.1) {
        percentageString = "< 0.1%";
    }

    d3.select("#bushel_total")
        .text(d.value);

    d3.select("#percentage")
        .text(percentageString);

    d3.select("#explanation")
        .style("visibility", "");

    var sequenceArray = d.ancestors().reverse();
    sequenceArray.shift(); // remove root node from the array
    updateBreadcrumbs(sequenceArray, percentageString);

    // Fade all the segments.
    d3.selectAll("path")
        .style("opacity", 0.3);

    // Then highlight only those that are an ancestor of the current segment.
    vis.selectAll("path")
        .filter(function(node) {
            return (sequenceArray.indexOf(node) >= 0);
        })
        .style("opacity", 1);
}

// Restore everything to full opacity when moving off the visualization.
function mouseleave(d) {

    // Hide the breadcrumb trail
    $('#sequence').css('visibility', 'hidden');

    // Deactivate all segments during transition.
    d3.selectAll("path").on("mouseover", null);

    // Transition each segment to full opacity and then reactivate it.
    d3.selectAll("path")
        .transition()
        .duration(1000)
        .style("opacity", 1)
        .on("end", function() {
            d3.select(this).on("mouseover", mouseover);
        });

    d3.select("#explanation")
        .style("visibility", "hidden");
}

// function initializeBreadcrumbTrail() {
//     // Add the svg area.
//     var trail = d3.select("#sequence").append("svg:svg")
//         .attr("width", width)
//         .attr("height", 50)
//         .attr("id", "trail")
//         .on('load', function() {
//             trail.attr('width', this.width);
//         });
//     // Add the label at the end, for the percentage.
//     trail.append("svg:text")
//         .attr("id", "endlabel")
//         .style("fill", "#000");
// }

// Generate a string that describes the points of a breadcrumb polygon.
function breadcrumbPoints(d, i) {
    var points = [];
    points.push("0,0");
    points.push(b.w + ",0");
    points.push(b.w + b.t + "," + (b.h / 2));
    points.push(b.w + "," + b.h);
    points.push("0," + b.h);
    if (i > 0) { // Leftmost breadcrumb; don't include 6th vertex.
        points.push(b.t + "," + (b.h / 2));
    }
    return points.join(" ");
}

// Update the breadcrumb trail to show the current sequence and percentage.
function updateBreadcrumbs(nodeArray, percentageString) {
    var storageTrail = [];
    for(var nodeIndex in nodeArray){
        if(nodeArray.hasOwnProperty(nodeIndex)){
            storageTrail.push(nodeArray[nodeIndex].data);
        }
    }

    var htmlString = "";
    var isFirstinTrail = true;
    for(var trailItem in storageTrail) {
        if (storageTrail.hasOwnProperty(trailItem)) {
            htmlString += "<div style=\"padding: 10px; background-color:rgba(0, 0, 0, 0.12); border-radius: 5px\"><div class=\"upper\">"
                + currentRoomStats.fieldID2fieldName[storageTrail[trailItem]['fieldID']]
                + "</div><div style=\"height: 1em; border-radius: 15px; margin: 2px; background-color: "
                + storageTrail[trailItem]['color']
                + "\"></div><div class=\"lower\">"
                + storageTrail[trailItem]['name']
                + "</div></div><div><i style=\"font-size: xx-large; transition: 0s\" class=\"material-icons\">keyboard_arrow_right</i></div>";
        }
    }
    htmlString += "<div style='font-size: 1.3em'>" + percentageString + "</div>";

    $('#sequence').html(htmlString);

// // Data join; key function combines name and depth (= position in sequence).
// var trail = d3.select("#trail")
//     .selectAll("g")
//     .data(nodeArray, function(d) { return d.data.name + d.depth; });
//
// // Remove exiting nodes.
// trail.exit().remove();
//
// // Add breadcrumb and label for entering nodes.
// var entering = trail.enter().append("svg:g");
//
// entering.append("svg:polygon")
//     .attr("points", breadcrumbPoints)
//     .style("fill", function(d) { return d.data.color; });
//
// entering.append("svg:text")
//     .attr("x", (b.w + b.t) / 2)
//     .attr("y", b.h / 2)
//     .attr("dy", "0.35em")
//     .attr("text-anchor", "middle")
//     .text(function(d) { return d.data.name; });
//
// // Merge enter and update selections; set position for all nodes.
// entering.merge(trail).attr("transform", function(d, i) {
//     return "translate(" + i * (b.w + b.s) + ", 0)";
// });
//
// // Now move and update the percentage at the end.
// d3.select("#trail").select("#endlabel")
//     .attr("x", (nodeArray.length + 0.5) * (b.w + b.s))
//     .attr("y", b.h / 2)
//     .attr("dy", "0.35em")
//     .attr("text-anchor", "middle")
//     .text(percentageString);
//
// // Make the breadcrumb trail visible, if it's hidden.
d3.select("#sequence")
    .style("visibility", "");

}