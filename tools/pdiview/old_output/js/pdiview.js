/*
 * Copyright (C) 2015-2017 Marco Pasi <mf.pasi@gmail.com> 
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */

/**** PDIView
  * version 0.1
  ****/

/**** Changelog
 * v0.1		First draft
 ****/

/**** TODO
 * color unused DNA gray/transparent?
 * get "initial" orientation at the end of initial tranformations (catch some event? wait some time?)
 * fix rotateCamera* methods with new viewerControls
 * - remove Safari Hack for image download when Safari supports download file name setting (see webkit bug 102914).
 * look AT interactions, ideally from the interaction groove
 */


/* GLOBAL SETTINGS */
var AWF = 0.3,                  // radius for Axis
    BWF = 0.3,                  // radius for Backbone
    GWF = 0.3,                  // radius for Grooves
    CWF = 0.3;                  // radius for Curvature vectors
var Aco = "midnightblue",
    Bco = "darkred",
    Cco = Aco,
    Gcos= ["mediumvioletred","darkorange","pink","silver"],
    Ncw = "gray",
    Ncs = "khaki",
    Nso = 0.5, // opacity
    Pcc = "steelblue",
    Pcs = "skyblue",
    Pco = 0.5,
    Pso = 0.5; // opacity
var BGCOLORS =["lightgray","white","black","powderblue"];
// NDB colors
var lowsaturation = //RGBYC
    [0xE6BEBE, 0xBEE6BE, 0xBEBEE6, 0xE6E6AA, 0xBEE6E6];
var higsaturation = //RGBYC
    [0xFF7070, 0xA0FFA0, 0xA0A0FF, 0xFFFF70, 0x70FFFF];
var logos = //RGBYC
    [0x00720D, 0xFFCB00, 0x2AB400, 0xFF9200, 0x70FFFF]
var rgbyc = logos;
var NDBColors = NGL.ColormakerRegistry.addSelectionScheme( [ // A red, T blue, C yellow, G green, and U cyan. 
    [rgbyc[0],"DA or A"],
    [rgbyc[1],"DG or G"],
    [rgbyc[2],"DT"],
    [rgbyc[3],"DC or C"],
    [rgbyc[4],"U"],
    ["gray","*"]
]);

var DEBUG = false;

/* GLOBALS */
var stage, repdata, dna_axis, orientation, zoom;
var reference_orientation_component = null;


$(document).ready(function(e){
    $("#plots_selector").change(function(){
        /* Activate carousel on change */
        var selected = $(this).children(":selected" )[0]; 
        var slide_to = parseInt($(selected).attr("data-slide-to"));
        var carousel_id = $(this).attr("data-target");
        $(carousel_id).carousel(slide_to);
        console.log("selector", slide_to)
    });
    $("#plots").on('slide.bs.carousel', function (e) {
        /* Update select on carousel change */
        var destination = $(e.relatedTarget).index();
        var select = $("#plots_selector");
        var option = select.children("[data-slide-to="+destination+"]")[0];
        console.log(destination, option)
        select.val(option.value);
    });
    $("#controls-toggler").slideUp();
    $("#controls-toggler-control").click(function(e){
        var toggler = $("#controls-toggler");
        var toggle_icon = $("#controls-openclose");
        toggler.slideToggle(500);
        toggle_icon.toggleClass("glyphicon-arrow-right glyphicon-arrow-left")
    });
});

/*************************
 * Create the viewer
 */
function ngl_viewer(AXPATH, BBPATH, CRPATH, PDBPATH, PPATH, IPATH, SPATH) {
    repdata = {};
    stage = new NGL.Stage("viewport",
                          {"cameraType": "orthographic",
                           "backgroundColor": "white"});

    stage.signals.hovered.add(function(d){
        var msg = getPickingMessage( d, "" );
        $("#tooltip").html(msg);
    });
    // stage.viewerControls.signals.changed.add(function(e){
    //     console.log(stage.animationControls.animationList.length, stage.viewerControls.getOrientation());
    // });
    // Create RepresentationGroups for the input PDB
    var pdbRG = stage.loadFile(PDBPATH)
        .then(function(c) {
            var some = do_input(c);
            $.extend(some, do_interactions(c, PPATH, IPATH, SPATH)); /* NEW */
            return some;
        }, error);
    var axRG, bbRG, crRG;
    // Define dummy axis if we lack one
    dna_axis = new NGL.Vector3(0,1,0);
    if(AXPATH != "") {
        // Create RepresentationGroups for the axis PDB
        axRG = stage.loadFile(AXPATH)
            .then(function(c) {
                // Get Axis approximate axis
                dna_axis = get_axis(c.structure);
                reference_orientation_component = c;
                return c;})
            .then(do_ax, error);
    } else {
        pdbRG.then(function(rg) {
            reference_orientation_component = rg["Nucleic Acid"].component;
            return rg;});
    }
    if(BBPATH != "") {
        // Create RepresentationGroups for the backbone PDB
        bbRG = stage.loadFile(BBPATH).then(do_bb, error);
    }
    // if(CRPATH != "") {
    //     // Create RepresentationGroups for the curvature PDB
    //     crRG = stage.loadFile(CRPATH).then(do_cr, error);
    // }

    // Wall: resolve all RepresentationGroups
    Promise.all([pdbRG, axRG, bbRG, crRG]).then(function(RG) {
        // Set initial orientation and zoom
        reference_orientation_component.autoView();
        // Aggregate RepresentationGroups in repdata
        RG.forEach(function(rep) {$.extend(repdata, rep);});
        return repdata;
    }).then(
        // Write GUI for RepresentationGroups
        // in specific containers, in a specific order.
        function(RGdata) {
            var lc = $("#"+"lcontrols");
            if(RGdata["Nucleic Acid"])
                lc.append(RGdata["Nucleic Acid"].GUI("nadisplay", true));
            if(RGdata["Axis"])
                lc.append(RGdata["Axis"].GUI("axdisplay", true));
            if(RGdata["Backbone"])
                lc.append(RGdata["Backbone"].GUI("bbdisplay", true));
            if(RGdata["Groove12"])
                lc.append(RGdata["Groove12"].GUI("gr1display", false));
            if(RGdata["Groove21"])
                lc.append(RGdata["Groove21"].GUI("gr2display", false));
            if(RGdata["Curvature"])
                lc.append(RGdata["Curvature"].GUI("crdisplay", true));
            
            var rc = $("#"+"rcontrols");
            if(RGdata["Protein"])
                rc.append(RGdata["Protein"].GUI("prodisplay", true));
            rc.append(GUI_extras(RGdata["Axis"]));
            more_GUI_extras();
        });

    window.addEventListener(
        "resize", function( event ){
            stage.handleResize();
        }, false
    );
}

/*************************
 * Define extra GUI elements.
 */
function GUI_extras(axis) {
    // Background
    var cdiv = $("<div/>", {"class": "colors"});
    cdiv.append("BG: ");
    BGCOLORS.forEach(function(c) {
        cdiv.append(
            $("<a/>", {"class": "dummylink"}).append(
                $("<div/>", {"id": "",
                             "class": "cimg"})
                    .css("background-color", c)
                    .click(function(e) {stage.viewer.setBackground(c);})));
    });

    if(!axis) return cdiv;
    
    function align() {
        //XXX aligns axis horizontal
        rotateCameraTo(dna_axis);
        rotateCameraAxisAngle(cc(new NGL.Vector3(1,0,0)), -Math.PI/2)
    }

    // requires previously defined initial orientation and zoom
    function orient() {
        // reset orientation to initial
        reference_orientation_component.autoView(1000);
    }
    
    // Buttons
    var ddiv = $("<div/>", {"class": "buttons"});
    ddiv.append(
        $("<input/>", {"type": "button",
                       "value": "Reset orientation"})
            .click(orient),
        $("<br/>")
        // $("<input/>", {"type": "button",
        //                "value": "Align Axis"})
        //     .click(align),
        // $("<br/>")
    );

    return [cdiv, ddiv];
}

function safariw(data, target) {
    var url = URL.createObjectURL( data );
    target.location.href = url;
}

function more_GUI_extras() {
    // Safari Hack: open image in new window
    if( typeof window === "undefined" ) return false;
    var ua = window.navigator.userAgent,
        isSafari = ( /Safari/i.test( ua ) && ! /Chrome/i.test( ua ) ),
        safariwin = null;
    // More Buttons
    function image() {
        if(isSafari) { // Safari Hack: open window early, otherwise Safari will block it!
            safariwin = window.open();
            safariwin.document.write("<html><head><link rel='stylesheet' href='/static/style.css'></head><body><h1>Curves+ web server</h1><h3>Thank you for your patience...</h3>Please wait while screenshot is being created. This may take a few seconds...</body></html>")
        }
        var fname = "screenshot.png"
        stage.makeImage({
            factor: 4,
            antialias: true,
            trim: false,
            transparent: false
        }).then( function( blob ){
            if(isSafari) { // Safari Hack: set new window URL to image
                return safariw(blob, safariwin);
            }
            return NGL.download( blob, fname );
        });
    }

    var ediv = $("<div/>", {"style": "position:absolute; bottom: 5px; right: 5px;"});
    ediv.append(
        $("<img/>", {"src": "img/camera.svg",
                     "width": "25px"})
            .click(image));
    $(stage.viewer.container)
        .css("position", "relative")
        .append(ediv);
}

getPickingMessage = function( d, prefix ){
    var msg;
    if( d.atom ){
        msg = d.atom.qualifiedName();
    }else if( d.bond ){
        msg = d.bond.atom1.qualifiedName();
    }else{
        msg = "Hover on atoms for details.";
    }
    return prefix ? prefix + " " + msg : msg;
};

/*************************
 * Representation callbacks
 *
 * Configure representations here.
 * Each method creates a dictionary of RepresentationGroups, one
 * for each selection relevant for the specified component.
 * These are subsequently aggregated and used to design GUI.
 *
 */   
function do_input(comp) {
    return {
        // Nucleic
        "Nucleic Acid":
        new MutuallyExclusiveRepresentationGroup(comp, "Nucleic Acid", "nucleic")
            .addRepresentation( "Wire",
                                comp.addRepresentation( "licorice",   {"colorScheme": NDBColors}))
            .addRepresentation( "Element",
                                comp.addRepresentation( "ball+stick", {"colorScheme": "element"}))
            .addRepresentation( "Surface",
                                comp.addRepresentation( "surface",    {"opacity": Nso,
                                                                       "colorScheme": "uniform",
                                                                       "colorValue":  Ncs})),
        // Protein
        "Protein":
        new MutuallyExclusiveRepresentationGroup(comp, "Protein", "protein")
            .addRepresentation( "Cartoon",
                                comp.addRepresentation( "cartoon",  {"colorScheme":   "uniform",
                                                                     "colorValue":    Pcc,
                                                                     "opacity":       Pco}))
            .addRepresentation( "Wire",
                                comp.addRepresentation( "licorice", {"colorScheme":  "element"}))
            .addRepresentation( "Surface",
                                comp.addRepresentation( "surface",  {"opacity": Pso,
                                                                     "colorScheme": "uniform",
                                                                     "colorValue":  Pcs}))
    };
}

function do_ax(comp) {
    return {
        "Axis":
        new MutuallyExclusiveRepresentationGroup(comp, "Axis", null)
            .addRepresentation( null,
                               comp.addRepresentation( "licorice", {"colorScheme": "uniform",
                                                                    "colorValue":  Aco,
                                                                    "radius":      AWF}))
    };
}

function do_bb(comp) {
    return {
        "Backbone": 
        new MutuallyExclusiveRepresentationGroup(comp, "Backbone", "(:A or :B)")
            .addRepresentation(null,
                               comp.addRepresentation( "licorice", {"colorScheme": "uniform",
                                                                    "colorValue":  Bco,
                                                                    "radius":      BWF})),
        "Groove12": 
        new MutuallyExclusiveRepresentationGroup(comp, "Groove12", ":C")
            .addRepresentation(null,
                               comp.addRepresentation( "licorice", {"colorScheme": "uniform",
                                                                    "colorValue":  Gcos[0],
                                                                    "radius":      GWF})),
        "Groove21": 
        new MutuallyExclusiveRepresentationGroup(comp, "Groove21", ":D")
            .addRepresentation(null,
                               comp.addRepresentation( "licorice", {"colorScheme": "uniform",
                                                                    "colorValue":  Gcos[1],
                                                                    "radius":      GWF})),
    };
}

function do_cr(comp) {
    return {
        "Curvature":
        new MutuallyExclusiveRepresentationGroup(comp, "Curvature", null)
            .addRepresentation(null,
                               comp.addRepresentation( "licorice", {"colorScheme": "uniform",
                                                                    "colorValue":  Cco,
                                                                    "radius":      CWF}))
    };
}

/*************************
*
*/
function read_file(fname, parser, done) {
    $.get(fname).done(function(data) {
        done(parser(data));
    });
}

function parse_pairings(data) {
    var pairings = {};
    var lines = data.split('\n');
    for (var i=0; i<lines.length-1; i++) {
        // resi1 resi2
        var values = lines[i].split('\t');
        pairings[values[1]] = values[0];
    }
    return pairings;
}

function invert_hash(hash) {
    var _hash = {};
    for (var key in hash)
        if (hash.hasOwnProperty(key))
            _hash[hash[key]] = key;
    return _hash;
}

function parse_interactions(data, pairings) {
    var interactions = {};
    var lines = data.split('\n');
    for (var i=0; i<lines.length-1; i++) {
        // resi1 chain1 resn1 atom1 resi2 chain2 resn2 atom2 ?H
        var values = lines[i].split('\t');
        var key = values[0]+":"+values[1];
        if(key in pairings)
            key = pairings[key];
        if(!(key in interactions))
            interactions[key] = [];
        interactions[key].push(values);
    }
    return interactions;
}

function parse_sequence(data) {
    var sequence = [];
    var lines = data.split('\n');
    for (var i=0; i<lines.length-1; i++) {
        // resn1 resn2
        var values = lines[i].split('\t');
        sequence.push(values);
    }
    return sequence;
}

function vertical_sequence(sequence, group) {
    var c = $("<ol/>")
    sequence.forEach(function(basepair, i) {
        c.append($("<li/>").append(
            $("<a/>").click(function(e) {
                group.nenable(basepair[2]);
            }).append(basepair[0]+"--"+basepair[1])));
    });
    return c;
}

function _normalize(name) {
    if(name[0] == "D")
        name = name.slice(1);
    return name;
}

function horizontal_sequence(sequence, group) {
    // var c = $("<table/>")
    // var fors = $("<tr/>");
    // var lins = $("<tr/>");
    // var revs = $("<tr/>");
    // sequence.forEach(function(basepair, i) {
    //     forb = _normalize(basepair[0]);
    //     revb = _normalize(basepair[1]);
        
    //     fors.append($("<td/>").append(
    //         $("<a/>").click(function(e) {
    //             group.nenable(basepair[2]);
    //         }).append(forb)));
        
    //     revs.append($("<td/>").append(
    //         $("<a/>").click(function(e) {
    //             group.nenable(basepair[2]);
    //         }).append(revb)));

    //     lins.append($("<td/>").append(
    //         $("<a/>").click(function(e) {
    //             group.nenable(basepair[2]);
    //         }).append("|")));
    // });
    // c.append(fors);
    // c.append(lins);
    // c.append(revs);
    // return c;
    var c = $("<table/>").append($("<tr/>"));
    c.append($("<th/>", {"class":"seq_initial"})
             .append("5'-<br/><br/>3'-"));
    sequence.forEach(function(basepair, i) {
        forb = _normalize(basepair[0]);
        revb = _normalize(basepair[1]);
        c.append($("<td/>")
                 .click(function(e) {
                     group.nenable(basepair[2]);
                 })
                 .append(forb+"<br/>|<br/>"+revb));
    });
    c.append($("<th/>", {"class":"seq_final"})
             .append("-3'<br/><br/>-5'"));
    return c;
}

function do_interactions(comp, pairings_file, interactions_file, sequence_file) {
    var group = new MutuallyExclusiveRepresentationGroup(comp, "Interactions", null);
    
    read_file(pairings_file, parse_pairings, function(pairings) {
        read_file(interactions_file, function(data) {
            return parse_interactions(data, pairings);
        }, function(interactions) {
            var rev_pairings = invert_hash(pairings);
            for(var res1 in interactions) {
                // resi1 chain1 resn1 atom1 resi2 chain2 resn2 atom2 ?H
                var allresidues = interactions[res1].map(function(values){
                    return values[4]+":"+values[5];
                });
                allresidues.push(res1);
                if(res1 in rev_pairings)
                    allresidues.push(rev_pairings[res1]);
                var interactiongroup = new InteractionGroup(
                    comp, res1, "("+allresidues.join(" or ")+")")
                    .addRepresentation( "Ball&Stick",
                                        comp.addRepresentation( "ball+stick", {"colorScheme": "element",
                                                                               "colorValue":  "#006b8f"}));
                interactions[res1].forEach(function(values) {
                    interactiongroup.addInteraction(
                        values[0]+":"+values[1]+"."+values[3],
                        values[4]+":"+values[5]+"."+values[7],
                        hydrophobic = values[8] == "H");
                    });
                group.addRepresentation(res1, interactiongroup)
            }
        });
    });

    /* Hack in some GUI */
    read_file(sequence_file, parse_sequence, function(sequence) {
        // var c = vertical_sequence(sequence, group);
        var c = horizontal_sequence(sequence, group);
        $("#sequence").append(c);
    });
    group.toggle(true);
    return {"Interactions": group};
}


/*************************
 * Interaction representation group API
 */
function _autoView(component, sele, duration) {
    component.stage.animationControls.zoomMove(
        component.getCenter( sele ),
        component.getZoom( sele ) * 0.65,
        duration
    );
}
var InteractionGroup = function(component, name, selection, representations, 
                            defaultParameters) {
    RepresentationGroup.call(this, component, name, selection, representations, defaultParameters);
};
InteractionGroup.prototype = Object.create(RepresentationGroup.prototype);
    
InteractionGroup.prototype.setVisibility = function(what) {
    // Show/Hide all representations in group, and focus/zoom
    this.reprList.forEach(function(repr) {
        repr.setVisibility(what);
    });
    if(what) {
        _autoView(this.component, this.selection, 1000);
        // var axis = get_axis(repr.repr.structure);
        // rotateCameraTo(axis);
        // rotateCameraAxisAngle(cc(new NGL.Vector3(1,0,0)), -Math.PI/2)
    }
}

InteractionGroup.prototype.all_empty = function() {};
InteractionGroup.prototype.GUI = function() {};
InteractionGroup.prototype.addInteraction = function(atom1, atom2, hydrophobic) {
    /*
     * Add an interaction to this group.
     */
    hydrophobic = typeof hydrophobic === 'undefined' ? false : hydrophobic;
    var parameters = {
        atomPair: [[atom1, atom2]],
        labelColor: "black",
        color: "blue",
        opacity: 0.5,
        scale: 0.25,
        labelUnit: "angstrom",
        labelSize: 0.75
    };
    if(hydrophobic) {
        parameters["labelSize"] = 0;
        parameters["color"] = "green";
    }
    this.addRepresentation( "distance",
                            this.component.addRepresentation(
                                "distance", parameters));
    return this;
}

/*************************
 * Promise functions
 */
function error(err) {
    console.log(err);
}

/*************************
 * Utilities
 */
function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function get_axis(structure) {
    var atoms = structure.atomStore,
        n = atoms.count-1,
        x = atoms.x[0] - atoms.x[n],
        y = atoms.y[0] - atoms.y[n],
        z = atoms.z[0] - atoms.z[n];
    return new NGL.Vector3(x,y,z).normalize();
}

function rotateCameraTo(end) {
    return null;
    // var camera = stage.viewer.camera,
    //     start = camera.getWorldDirection(),
    //     target = stage.viewerControls.target;
    // var angle = Math.acos(start.dot(end) / start.length() / end.length()),
    //     raxis = (new NGL.Vector3()).crossVectors(start, end).normalize();    
    // return rotateCameraAxisAngle(raxis, angle, target);
}

function rotateCameraAxisAngle(axis, angle, target) {
    return null;
    // if (!angle) return;
    // if (!target) target = stage.viewerControls.target;
    // var camera = stage.viewer.camera,
    //     _eye = new NGL.Vector3();
    // var quaternion = (new NGL.Quaternion()).setFromAxisAngle(axis, angle);
    // // rotate the distance vector (_eye)
    // _eye.subVectors(camera.position, target);
    // _eye.applyQuaternion(quaternion);
    // // rotate the camera's up vector
    // camera.up.applyQuaternion(quaternion);
    // // re-apply rotated distance to camera
    // camera.position.addVectors(_eye, target);
    // // reorient camera towards target
    // camera.lookAt(target);
}

function cc(axis) {
    return null;
    // var camera = stage.viewer.camera,
    //     controls = stage.viewerControls;

    // var eye = (new NGL.Vector3()).copy( camera.position ).sub( controls.target ),
    //     eyeDirection = (new NGL.Vector3()).copy( eye ).normalize(),
    //     upDirection = (new NGL.Vector3()).copy( camera.up ).normalize(),
    //     sidewaysDirection = (new NGL.Vector3()).crossVectors( upDirection, eyeDirection ).normalize(),
    //     moveDirection = new NGL.Vector3();
    
    // console.log("  s=[", sidewaysDirection.toArray().toString(),
    //             "]; u=[", upDirection.toArray().toString(),
    //             "]; e=[", eyeDirection.toArray().toString(),
    //             "];");

    /*
     * The following operations are equivalent to:
     * 
     * moveDirection = M*axis
     *
     * where M is defined as follows:
     */
    
    // var M = (new NGL.Matrix4()).makeBasis(sidewaysDirection, upDirection.clone().negate(), eyeDirection);
    
    // eyeDirection.setLength( axis.z );
    // upDirection.setLength( axis.y );
    // sidewaysDirection.setLength( axis.x );

    // console.log("S", sidewaysDirection,
    //             "U", upDirection,
    //             "E", eyeDirection);
    
    // moveDirection.copy( sidewaysDirection.sub( upDirection ).add( eyeDirection ) );

    // console.log("D",moveDirection2axis.clone().applyMatrix4(M).normalize().sub(moveDirection));
    
    return moveDirection;
}

function invcc(axis) {
    return null;
    // Implement inverse operation performed in NGL.Viewer.rotate
    // to directly define axis.
    // var camera = stage.viewer.camera,
    //     controls = stage.viewer.controls;

    // var eye = (new NGL.Vector3()).copy( camera.position ).sub( controls.target ),
    //     eyeDirection = (new NGL.Vector3()).copy( eye ).normalize(),
    //     upDirection = (new NGL.Vector3()).copy( camera.up ).normalize(),
    //     sidewaysDirection = (new NGL.Vector3()).crossVectors( upDirection, eyeDirection ).normalize();
    
    // var M = (new NGL.Matrix4()).makeBasis(sidewaysDirection, upDirection.clone().negate(), eyeDirection);
    // return axis.clone().applyMatrix4(M.transpose()).normalize();
}
