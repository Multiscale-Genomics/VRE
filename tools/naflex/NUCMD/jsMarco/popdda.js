/*
 * popdda.js 
 *
 *  Copyright (C) 2016 Marco Pasi <mf.pasi@gmail.com> 
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 * v0.1 160422
 * v0.2 160423
 *
 */
var dinucl = "GG GA AG AA GC GT AT AC CA TA TG CG CC CT TC TT".split(" ");
var dinucli = "GG GA AG AA GC GT AT TA TG CG".split(" ");
var skippers= {
    inner: [4,6,7,9],
    outer: [7,8,12,13,14,15]},
    precision = {               // precision of output
        shift: 2,
        slide: 2,
        rise:  2,
        tilt:  1,
        roll:  1,
        twist: 1 
    },
    unitH = {                   // unit
        shift: '\u212B',
        slide: '\u212B',
        rise: '\u212B',
        tilt: '\u00B0',
        roll: '\u00B0',
        twist: '\u00B0'
    },
    vrange = {                  // view range
        shift: [-1.0, 1.0],
        slide: [-1.5, 0.5],
        rise:  [+2.6, 3.8],
        tilt:  [-5.0, 5.0],
        roll:  [-5.0, 15],
        twist: [+20, 40]
    },
    srange = {                  // stiffness range
        shift: [0.0, 1.0],
        slide: [0.0, 3.0],
        rise:  [0.0, 5.0],
        tilt:  [0.0, 0.01],
        roll:  [0.0, 0.01],
        twist: [0.0, 0.01]
    },
    colormaps = {
        jet8:   ["#000080","#0010ff","#00a4ff","#40ffb7","#b7ff40","#ffb900","#ff3000","#800000"],
        RdBu9:  ['#b2182b','#d6604d','#f4a582','#fddbc7','#f7f7f7','#d1e5f0','#92c5de','#4393c3','#2166ac'],
        BrBG10: ['#543005','#8c510a','#bf812d','#dfc27d','#f6e8c3','#c7eae5','#80cdc1','#35978f','#01665e','#003c30'],
        Spec11: ['#9e0142','#d53e4f','#f46d43','#fdae61','#fee08b','#ffffbf','#e6f598','#abdda4','#66c2a5','#3288bd','#5e4fa2']
    },
    sizes = [0.6, 0.8, 1.0];
colormaps.RdBu9.reverse();
colormaps.BrBG10.reverse();
colormaps.Spec11.reverse();

var margin = {top: 15, right: 100, bottom: 20, left: 70},
    gridSize = 20,
    gridPad = 3,
    cbGridSize = 15,
    width = (gridSize+gridPad)*10,
    height = (gridSize+gridPad)*16,
    transitionDuration = 1000, // ms
    svg, cursorInfoCallback, vname = "twist", cname = "jet8", stiffness_size = getQueryVariable("stiff");

function xScale(x) {
    return +x*(gridSize+gridPad);
}

function yScale(y) {
    return height-(+y+1)*(gridSize+gridPad);
}

function q2cb(q, domain) {
    return [domain[0]].concat(q);
}

function capitalize(string) {
    return string.substr(0, 1).toUpperCase() + string.substr(1);
}

function unit(variable) {
    if(!(variable in unitH))
        return ""
    return unitH[variable]
}

function getQueryVariable(variable)
{
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}

// XXX Use the function.call(instance) pattern
//     to wrap all this into an object with an
//     assigned id, tsv; then the _this pattern
//     can be used for callbacks.
function popdda(id, tsv) {
    svg = d3.select(id).append("svg")
        .attr("width",  width  + margin.left + margin.right)
        .attr("height", height + margin.top  + margin.bottom)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var xlabelsg = svg.append("g").attr("class", "xlabels");
    /* X labels */
    var xlabels = xlabelsg.selectAll(".xlabel")
        .data(dinucli)
      .enter().append("text")
        .text(function (d) { return d; })
        .attr("x", function (d, i) { return xScale(i+0.5); })
        .attr("y", yScale(-1))
        .style("text-anchor", "middle")
        .attr("transform", "translate(-2,12)")
        .attr("class", "x label");

    var ylabelsg = svg.append("g").attr("class", "ylabels");
    /* Y labels */
    var ylabels = ylabelsg.selectAll(".ylabel")
        .data(dinucl)
      .enter().append("text")
        .text(function(d) { return d[0]+".."+d[1]; })
        .attr("x", xScale(0))
        .attr("y", function(d, i) { return yScale(i-0.5); })
        .style("text-anchor", "middle")
        .attr("transform", "translate(-20,3)")
        .attr("class", "y label");

    this.update = function(namev, domain, namec) {
        /* 
         * Color the heatmap by the stored value "vname",
         * using values in the range specified by domain.
         */
        if(namev)
            vname = namev;
        if(namec)
            cname = namec;
        var colors= colormaps[cname];
        var domain = (domain ? domain : vrange[vname]);
        // Update colorScale
        var colorScale = d3.scale.quantile()
            .domain(domain)
            .range(colors);
        var data = q2cb(colorScale.quantiles(), domain);
        var data2= data.concat(domain[domain.length - 1]);

        // Update the colorbar label
        var colorbar = svg.select(".colorbar");
        var cblabel = colorbar.select(".cblabel");
        cblabel
            .transition().duration(transitionDuration/2)
            .style("opacity", 0)
            .transition().duration(transitionDuration/2)
            .style("opacity", 1)
            .text(capitalize(vname)+" ("+unit(vname)+")");

        // Move the colorbar accordingly
        var cb = svg.select(".cb");
        cb.attr("transform", "translate(0, " +
                (cblabel[0][0].getBBox().height) + ")");
        
        // Bind data to rects and texts
        var cbrects = cb.selectAll(".cbrect").data(data);
        var cbticks = cb.selectAll(".cbtick").data(data2);
        cbrects.exit()
            .transition().duration(transitionDuration)
            .style("opacity", 0)
            .remove();
        cbticks.exit()
            .transition().duration(transitionDuration/2)
            .style("opacity", 0)
            .remove();
        
        // Deal with new elements
        cbrects.enter().append("rect")
            .attr("class", "cbrect")
            .attr("y", function(d, i) { return cbGridSize * i; })
            .attr("width", cbGridSize)
            .attr("height", cbGridSize);
        cbticks.enter().append("text")
            .attr("class", "cbtick")
            .attr("x", cbGridSize + 10)
            .attr("y", function(d, i) { return cbGridSize * i; })
            .style("text-anchor", "start")
            .attr("transform", "translate(0, 4)");

        // Update
        cbrects.transition().duration(transitionDuration)
            .style("opacity", 1)
            .style("fill", function(d, i) { return colors[i]; });
        cbticks
            .transition().duration(transitionDuration/2)
            .style("opacity", 0)
            .transition().duration(transitionDuration/2)
            .style("opacity", 1)
            .text(function(d) {return d.toFixed(precision[vname]);});
        var hmrects = svg.selectAll("rect.cell");
        hmrects.transition().duration(transitionDuration)
            .style("fill", function(d) { return colorScale(+d[vname]); });
        
        // Update cursor info
        cursorInfoCallback = function(d) {
            return d[vname] + unit(vname);
        };
        
        if(stiffness_size) {
            var sdomain = srange[vname];
            var sizeScale = d3.scale.quantile()
                .domain(sdomain)
                .range(sizes);
            console.log(sizeScale.quantiles());
            var sgridSize = function(d, i) { return sizeScale(d["s"+vname])*gridSize; };
            var sdelta = function(d) { return 0.5*(1.0-sizeScale(d["s"+vname]))*gridSize; };
            hmrects //.transition().duration(transitionDuration)
                .attr("width", sgridSize)
                .attr("height", sgridSize)
                .attr("transform", function(d, i) {
                    return "translate("+sdelta(d)+","+sdelta(d)+")";
                });
            cursorInfoCallback = function(d) {
                return d[vname] + unit(vname) + "["+d["s"+vname]+"]";
            };
        }

        return true;
    };
    
/***** TSV obtained using:
       #  1  2  3  4  5  6  7  8  9 10 11 12 13 14 15 16 17 18
       # 36 35 34 33 32 31 30 29 28 27 26 25 24 23 22 21 20 19
       
       for abc in libabc.ABC:
           seq = "GC%s%s%s%sGC"%(abc[2:],abc,abc,abc)
           for i in range(libabc.ABC_locations[abc][0],
               libabc.ABC_locations[abc][1]+1):
               j = i-1
               wtet = seq[j:j+4]
               ctet = libabc.wcc(wtet)
               wi = libabc.seq2idx(wtet)
               ci = libabc.seq2idx(ctet)
               print "%4s %4s %2d %2d %2d"%(wtet, abc, j+1, wi[0], wi[1])
               print "%4s %4s %2d %2d %2d"%(ctet, abc, 36-j, ci[0], ci[1])
*****/
    
    d3.tsv(
        tsv,
        // (ab)Use accessor to filter data on input
        function(d) {
            if(d.inner < 10 && // Skip 80% of the redundancy
               !(skippers.inner.indexOf(+d.inner) >= 0 && skippers.outer.indexOf(+d.outer) >= 0)) {
                d.sshift = Math.random()*1.0;
                d.sslide = Math.random()*3.0;
                d.srise  = Math.random()*5.0;
                d.stilt  = Math.random()*0.01;
                d.sroll  = Math.random()*0.01;
                d.stwist = Math.random()*0.01;
                return d;
            }
        },
        // Build the UI
        function(error, data) {
            // Add a g for the colorbar
            var cb = svg.append("g")
                .attr("class", "colorbar")
                .attr("transform", "translate("+ (width + gridSize)
                      +",0)");
            // a sub-g the cb label
            cb.append("text")
                .attr("class", "cblabel")
                .style("text-anchor", "top")
                .text("\u00a0");
            // and a sub-g the actual cb
            cb.append("g")
                .attr("class", "cb");
            
            // Add a cursor information box, which disappears onmouseout
            var cursor = svg.append("g")
                .attr("class", "tcursor")
                .attr("transform", "translate("+
                      (width + gridSize)+","+
                      (height- 17)+")");
            // Add a tetrad text, updated here 
            var cursorText = cursor.append("text")
                .attr("class","tc-tetrad");
            // Add an info text, with a callback to be used by methods
            var cursorInfo = cursor.append("text")
                .attr("transform", "translate(0, 18)")
                .attr("class","tc-info");
            cursorInfoCallback = function(d) { return ""; }

            // Create the heatmap
            var heatmap = svg.append("g").attr("class", "heatmap");
            abc = heatmap.selectAll("rect.cell")
                .data(data)
              .enter().append("rect")
                .attr("x", function(d) { return xScale(d.inner); })
                .attr("y", function(d) { return yScale(d.outer); })
                .attr("rx", 4)
                .attr("ry", 4)
                .attr("class", "cell")
                .attr("width", gridSize)
                .attr("height", gridSize)
                .style("fill", "white")
                .style("cursor", "hand")
                .on("mouseover", function (d) {
                    cursor
                        .transition().duration(transitionDuration/10)
                        .style("opacity", 1);
                    cursorText.text(d.tetrad);
                    cursorInfo.text(cursorInfoCallback(d));
                })
                .on("mouseout", function (d) {
                    cursor
                        .transition().duration(transitionDuration/2)
                        .style("opacity", 0);
                })
                .on("click", function (d) {
                    svg.selectAll("rect.cell").classed("selected",false);
                    d3.select(this).classed("selected",true);
                    loadStiffnessPage(d.tetrad, d.oligo, d.pos);
                });

            update("twist", null, "jet8");
        });
    return this;
}

function loadStiffnessPage(tetrad, oligomer, position) {
    /* 
     * Retrieve stiffness information as a function of the
     * Curves+ helical parameters for a single base-pair step
     * in a specific ABC oligomer, given three information:
     *
     *   tetrad:   The nucleotide sequence of the tetrad (ACGT);
     *   oligomer: The ABC 4-letter code of the oligomer (e.g. AAAA);
     *   position: The position of the base-pair step in the sequence
     *             of the oligo; values > 18 are for the Crick strand.
     *
     */
    //$("#stiffness-external").text(
    //    tetrad+":"+oligomer+"@"+position);


/*
  var $loading = $('<div id="loading"><img src="images/loading2.gif"></div>').insertBefore('#stiffness-external');

  $(document).ajaxStart(function() {
        $loading.show();
  }).ajaxStop(function() {
        $loading.hide();
  });
*/
	var st = "NAFlex_mu"+oligomer;

	$.ajax({
	 url: "http://mmb.irbbarcelona.org/BigNASim/getStiffnessMatrixWithColor.php",
	 type: 'GET',
	 data: {"code":st,"bps":position},
	 success: function (data) {
		//tetrad+":"+oligomer+"@"+position
    		$("#stiffness-external").hide().html(
			//JSON.stringify(data)
			data
		).fadeIn("slow");
	  //alert(JSON.stringify(data));
	},
	  error: function(){
	   //alert("Cannot get data");
	 }
      });
}
