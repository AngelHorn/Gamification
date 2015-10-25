
var w = 1280 - 80,
    h = 800 - 180,
    x = d3.scale.linear().range([0, w]),
    y = d3.scale.linear().range([0, h]),
    color = d3.scale.category20c();
var treemap = d3.layout.treemap()
    .round(false)
    .size([w, h])
    .sticky(true)
    .value(function(d) { return d.size; });

var svg = d3.select("#body").append("div")
    .attr("class", "chart")
    .style("width", w + "px")
    .style("height", h + "px")
    .append("svg:svg")
    .attr("width", w)
    .attr("height", h)
    .append("svg:g")
    .attr("transform", "translate(.5,.5)");

function renderMap(root) {
    svg.selectAll('g').remove();
    var nodes;
    if (root.children) {
        nodes = treemap.nodes(root).filter(d => d.parent == root || (d.parent == root && !d.children));
    } else {
        nodes = [root];
    }
    var cell = svg.selectAll('g')
            .data(nodes)
            .enter()
            .append('svg:g')
            .attr('class', 'cell')
            .attr('transform', d => `translate(${d.x}, ${d.y})`)
.on('click', d => d3.event.button === 0 && zoom(d))
.on('contextmenu', d => { d3.event.preventDefault(); zoom(root.parent ? root.parent : root);} );
cell.append('svg:rect')
    .attr('width', d => d.dx - 1)
.attr('height', d => d.dy - 1)
.style('fill', d => color(d.name))
cell.append('svg:text')
    .attr('x', d => d.dx / 2)
.attr('y', d => d.dy / 2)
.attr('dy', '.35em')
    .attr('text-anchor', 'middle')
    .text(d => d.name)
.style('opacity', function(d) { d.w = this.getComputedTextLength(); return d.dx > d.w ? 1 : 0; });
}

function zoom(d) {
    var kx = w / d.dx, ky = h / d.dy;
    x.domain([d.x, d.x + d.dx]);
    y.domain([d.y, d.y + d.dy]);
    renderMap(d);
    var t = svg.selectAll("g.cell")
        // .transition()
        // .duration(750)
        .attr("transform", function(d) { return "translate(" + x(d.x) + "," + y(d.y) + ")"; });
    t.select("rect")
        .attr("width", function(d) { return kx * d.dx - 1; })
        .attr("height", function(d) { return ky * d.dy - 1; })

    t.select("text")
        .attr("x", function(d) { return kx * d.dx / 2; })
        .attr("y", function(d) { return ky * d.dy / 2; })
        .style("opacity", function(d) { return kx * d.dx > d.w ? 1 : 0; });
}

//d3.json("/assets/dist/D3/treemap.json", function(data) {
//    renderMap(data);
    // node = root = data;

    // var nodes = treemap.nodes(root)
    // 	.filter(function(d) { return d.parent; })
    // 	.reverse();
    // 	console.log(nodes);

    // var cell = svg.selectAll("g")
    // 	.data(nodes)
    // .enter().append("svg:g")
    // 	.attr("class", "cell")
    // 	.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; })
    // 	.on("click", function(d) { d.children && this.remove(); return zoom(d); });

    // cell.append("svg:rect")
    // 	.attr("width", function(d) { return d.dx - 1; })
    // 	.attr("height", function(d) { return d.dy - 1; })
    // 	.style("fill", function(d) { return color(d.name); });

    // cell.append("svg:text")
    // 	.attr("x", function(d) { return d.dx / 2; })
    // 	.attr("y", function(d) { return d.dy / 2; })
    // 	.attr("dy", ".35em")
    // 	.attr("text-anchor", "middle")
    // 	.text(function(d) { return d.name; })
    // 	.style("opacity", function(d) { d.w = this.getComputedTextLength(); return d.dx > d.w ? 1 : 0; });

    // d3.select(window).on("click", function() { zoom(root); });

    // d3.select("select").on("change", function() {
    // treemap.value(this.value == "size" ? size : count).nodes(root);
    // zoom(node);
    // });
//});

function size(d) {
    return d.size;
}

function count(d) {
    return 1;
}

function arrayToTreeMap(menus) {
    var id = 0,level = 0;
    var menu_objects = [],tree = [],not_root_menu = [];
    for (var menu of menus) {
        var menu_object = {
            name: menu['text'],
            menu: menu,
            children: []
        }
        var id = menu['id'];
        var level = menu['father_id'];
        menu_objects[id] = menu_object;
        if (level) {
            not_root_menu.push(menu_object);
        } else {
            tree.push(menu_object);
        }

    }
    for (var menu_object of not_root_menu) {
        var menu = menu_object['menu'];
        var id = menu['id'];
        var level = menu['father_id'];
        menu_object['size'] = 100;
        if (typeof menu_objects['size'] != 'undefined') {
            delete(menu_objects['size']);
        }
        menu_objects[level]['children'].push(menu_object);
    }
    var treeMap = {name: "Root", children: tree};
    return treeMap;
}