<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <base href="http://mmb.irbbarcelona.org/BigNASim/">
        <meta charset="utf-8" />
        <link rel="icon" type="image/png" href="images/DNA_extrusion.png" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
        <title>Molecular Modeling and Bioinformatics Group</title>

        <!--        -->
        <!-- STYLES -->
        <!--        -->

        <!-- Drupal mmb style -->
        <link rel="stylesheet" href="css/style.css">

        <!-- Own style -->
        <link rel="stylesheet" href="css/estil.css">

        <!-- Make tables responsive -->
        <link rel="stylesheet" href="css/responsiveTables.css">
        <link rel="stylesheet" href="css/responstables.css">

        <!-- TinyAccordion style -->
        <link rel="stylesheet" href="css/tinyAccordion.css">

        <!-- Ontology Search style -->
        <link rel="stylesheet" href="css/onto.css">

        <!-- Static MD traj info (getStruc.php) style -->
        <link rel="stylesheet" href="css/getStruct.css">

        <!-- Global Search (advSearch.php) style -->
        <link rel="stylesheet" href="css/advSearch.css">

        <!-- Global Search (advSearch.php) style -->
        <link rel="stylesheet" href="js/video-js/video-js.css">

        <!-- Transforming Table to Divs -->
        <link rel="stylesheet" href="css/table2divs.css">

        <!-- Styling Search Form -->
        <link rel="stylesheet" href="css/formStyle.css">

        <!-- Styling Stats Table -->
        <link rel="stylesheet" href="css/stats.css">

        <!-- jqplot css -->
        <link rel="stylesheet" type="text/css" href="js/jqplot/jquery.jqplot.css" />

        <!-- tooltip css -->
        <link rel="stylesheet" type="text/css" href="css/simptip-master/simptip.min.css" />

        <!-- F.A.Q. css -->
        <link rel="stylesheet" type="text/css" href="css/faq.css" />

	<!-- BNSdatamanager -->
        <style type="text/css">
        #folderName {visibility: hidden}
        #fileUpload {visibility: hidden}
        td,th {padding:0px 7px;}
        tr td:last-child {border-right: 1px solid #A8A8A8;}
        tr td:first-child {border-left: 1px solid #A8A8A8;}

        .progress {
                float:left;
                border: 2px solid #387da8;
                height: 16px;
                width: 30%;
        }
        .progress .prgbar {
                background: #387da8;
                width: 30%;
                position: relative;
                height: 16px;
                z-index: 999;
        }
        .progress .prgtext {
                color: #ffffff;
		text-shadow: -1px 0 #5b5b5b, 0 1px #5b5b5b, 1px 0 #5b5b5b, 0 -1px #5b5b5b;
                text-align: left;
                font-size: 13px;
                font-weight: bold;
                width: 30%;
                position: absolute;
                z-index: 1000;
        }
        .progress .prginfo {
                margin: 3px 0;
        }
        .notify {
            background-color:#fefbfa;
            color:#ac8967;
            border:.1em solid;
            border-color: #92b854;
            border-radius:10px;
            padding:5px 5px 5px 5px;
            margin:10px;
            cursor: default;
        }
        a.disabled {
           pointer-events: none;
           cursor: default;
           opacity:0.6;
        }
        </style>



        <!--             -->
        <!-- JavaScripts -->
        <!--             -->

        <!-- JQuery -->
        <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>

        <!-- Image Preview -->
        <script type="text/javascript" src="js/imagePreview.js"></script>

        <!-- Image/Video visualization with jQuery -->
        <script type="text/javascript" src="js/jqueryImages/jqueryImages.js"></script>

        <!-- Tiny Nav for Responsive Menu -->
        <script type="text/javascript" src="js/main-menu.js"></script>

        <!-- JSMol -->
        <script type="text/javascript" src="jsmol/JSmol.min.nojq.js"></script>

        <!-- JSMol auxiliar Scripts -->
        <script type="text/javascript" src="js/jmolScripts.js"></script>

        <!-- Miscellanious Aux Scripts -->
        <script type="text/javascript" src="js/auxScripts.js"></script>

        <!-- Accordion Scripts -->
        <!--<script type="text/javascript" src="js/tinyAccordion/script.js"></script>-->

        <!-- jqplot Scripts -->
        <!--[if lt IE 9]><script language="javascript" type="text/javascript" src="js/jqplot/excanvas.js"></script><![endif]-->
        <script language="javascript" type="text/javascript" src="js/jqplot/jquery.jqplot.min.js"></script>
        <script type="text/javascript" src="js/jqplot/plugins/jqplot.pieRenderer.min.js"></script>

    </head>
    <body class="html front logged-in one-sidebar sidebar-first page-node left-sidebar">
        <div id="wrapper">
            <header id="header" class="clearfix">
                <div id="site-logo">
                    <a href="/www/" title="Home">
                        <img src="http://mmb.pcb.ub.es/www/sites/web.mmb.pcb.ub.es.www/files/logo_0.png" alt="Home" />
                    </a>
                    <a href="index.php" title="Home">
                        <img src="images/BigNASim_rot.png" style="height: 100px; float:right; margin-bottom:10px; margin-right:10px;" alt="BigNASim" />
                    </a>
                </div>            
                <nav id="navigation" role="navigation">
                    <div id="main-menu">
                        <ul class="menu">
                            <li id="HomeLiTab" class="first collapsed"><a href="index.php" id="HomeTab" class="active">Home</a></li>
                            <li id="BrowseLiTab" class="leaf"><a href="browsePag.php" id="BrowseTab" title="">Browse</a></li>
                            <li id="SearchLiTab" class="leaf"><a href="newSearch.php" id="SearchTab" title="">Search</a></li>
                            <!--<li id="advSearchLiTab" class="leaf"><a href="retrAdvSearch.php" id="advSearchTab">Global Analyses</a></li>-->
                            <li id="advSearchLiTab" class="leaf"><a href="analyses.php" id="advSearchTab">Global Analyses</a></li>
                            <li id="statsLiTab" class="leaf"><a href="stats.php" id="statsTab">Statistics</a></li>
                            <li id="HelpLiTab" class="leaf"><a href="help.php" id="HelpTab" >Help</a></li>
			    <li id="SuppMatLiTab" class="leaf"><a href="SuppMaterial.php" id="SupplMatTab" >Suppl. Material</a></li>
                        </ul>      
                    </div>
                </nav>                
            </header>
            <script> var molTypes = {"Dna": 136, "Rna": 14, "Prot-Dna": 6};
            </script>
            <script>console.log('PHP: {"_id.idGroup":"A"}');</script><script>console.log('PHP: {"_id.idGroup":"C"}');</script><script>console.log('PHP: {"_id.idGroup":"G"}');</script><script>console.log('PHP: {"_id.idGroup":"T"}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^G$|^A$","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^C$|^T$","flags":"i"}}');</script><script> var baseTypes = {"A": 1347, "C": 1834, "G": 1965, "T": 1271};
            </script>
            <script>console.log('PHP: {"_id.idGroup":{"regex":"^AT$|^TA$","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^CG$|^GC$","flags":"i"}}');</script><script>console.log('PHP: {"$or":[{"_id.idGroup":{"regex":"^GC$|^CG$","flags":"i"}},{"_id.idGroup":{"regex":"^AT$|^TA$","flags":"i"}}]}');</script><script> var bpTypes = {"A-T\/T-A": 1170, "C-G\/G-C": 1703};
            </script>
            <script>console.log('PHP: {"_id.idGroup":{"regex":"^GC[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^GT[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^GG[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^GA[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^AC[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^AT[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^AA[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^AG[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^CG[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^CC[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^CT[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^CA[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^TA[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^TG[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^TT[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"_id.idGroup":{"regex":"^TC[ACGTUX][ACGTUX]","flags":"i"}}');</script><script>console.log('PHP: {"$or":[{"_id.idGroup":{"regex":"^GC[ACGTUX][ACGTUX]","flags":"i"}},{"_id.idGroup":{"regex":"^GT[ACGTUX][ACGTUX]","flags":"i"}},{"_id.idGroup":{"regex":"^AC[ACGTUX][ACGTUX]","flags":"i"}},{"_id.idGroup":{"regex":"^AT[ACGTUX][ACGTUX]","flags":"i"}}]}');</script><script>console.log('PHP: {"$or":[{"_id.idGroup":{"regex":"^CG[ACGTUX][ACGTUX]","flags":"i"}},{"_id.idGroup":{"regex":"^CA[ACGTUX][ACGTUX]","flags":"i"}},{"_id.idGroup":{"regex":"^TG[ACGTUX][ACGTUX]","flags":"i"}},{"_id.idGroup":{"regex":"^TA[ACGTUX][ACGTUX]","flags":"i"}}]}');</script><script>console.log('PHP: {"$or":[{"_id.idGroup":{"regex":"^CC[ACGTUX][ACGTUX]","flags":"i"}},{"_id.idGroup":{"regex":"^CT[ACGTUX][ACGTUX]","flags":"i"}},{"_id.idGroup":{"regex":"^TT[ACGTUX][ACGTUX]","flags":"i"}},{"_id.idGroup":{"regex":"^TC[ACGTUX][ACGTUX]","flags":"i"}}]}');</script><script>console.log('PHP: {"$or":[{"_id.idGroup":{"regex":"^AA[ACGTUX][ACGTUX]","flags":"i"}},{"_id.idGroup":{"regex":"^AG[ACGTUX][ACGTUX]","flags":"i"}},{"_id.idGroup":{"regex":"^GG[ACGTUX][ACGTUX]","flags":"i"}},{"_id.idGroup":{"regex":"^GA[ACGTUX][ACGTUX]","flags":"i"}}]}');</script><script> var bpsTypes = {"GC": 337, "GT": 105, "GG": 251, "GA": 179, "AC": 104, "AT": 181, "AA": 207, "AG": 172, "CG": 377, "CC": 122, "CT": 142, "CA": 121, "TA": 156, "TG": 107, "TT": 120, "TC": 156};
            </script>
            <script> var ontoNATypes = {"Dna": 141, "Rna": 14, "Hybrid": 1};
            </script>
            <script> var ontoStructureTypes = {"Duplex": 140, "Single Strand": 6, "Quadruplex": 5, "Triplex": 4, "HollidayJunction": 1};
            </script>
            <script> var ontoSystemTypes = {"Naked": 146, "Complex": 6};
            </script>
            <script> var ontoTrajectoryTypes = {"Equilibrium": 156};
            </script>
            <script> var ontoHelicalTypes = {"B": 120, "A": 16, "Mixed": 5, "Z": 3, "Hoogsteen": 2};
            </script>
            <script> var ontoFFTypes = {"ParmBSC1": 108, "ParmBSC0": 24, "Parm99": 18, "OL4": 1, "OL1+OL4": 1, "Charmm36": 1, "Cheng-Garcia": 1, "OL1": 1};
            </script>
            <script> var ontoLenTypes = {"Nanosecond Range": 99, "Microsecond Range": 57};
            </script>
            <script> var ontoChargeTypes = {"Electroneutrality": 81, "Added Salt": 75};
            </script>
            <script>
                jQuery(document).ready(function () {

                    menuTabs("stats");

                    var molTypes_jqplot = [];
                    for (var prop_name in molTypes) {
                        molTypes_jqplot.push([prop_name, molTypes[prop_name]]);
                    }
                    var baseTypes_jqplot = [];
                    for (var prop_name in baseTypes) {
                        baseTypes_jqplot.push([prop_name, baseTypes[prop_name]]);
                    }
                    var bpTypes_jqplot = [];
                    for (var prop_name in bpTypes) {
                        bpTypes_jqplot.push([prop_name, bpTypes[prop_name]]);
                    }
                    var bpsTypes_jqplot = [];
                    for (var prop_name in bpsTypes) {
                        bpsTypes_jqplot.push([prop_name, bpsTypes[prop_name]]);
                    }
                    var ontoNATypes_jqplot = [];
                    for (var prop_name in ontoNATypes) {
                        ontoNATypes_jqplot.push([prop_name, ontoNATypes[prop_name]]);
                    }
                    var ontoStructureTypes_jqplot = [];
                    for (var prop_name in ontoStructureTypes) {
                        ontoStructureTypes_jqplot.push([prop_name, ontoStructureTypes[prop_name]]);
                    }
                    var ontoSystemTypes_jqplot = [];
                    for (var prop_name in ontoSystemTypes) {
                        ontoSystemTypes_jqplot.push([prop_name, ontoSystemTypes[prop_name]]);
                    }
                    var ontoTrajectoryTypes_jqplot = [];
                    for (var prop_name in ontoTrajectoryTypes) {
                        ontoTrajectoryTypes_jqplot.push([prop_name, ontoTrajectoryTypes[prop_name]]);
                    }
                    var ontoHelicalTypes_jqplot = [];
                    for (var prop_name in ontoHelicalTypes) {
                        ontoHelicalTypes_jqplot.push([prop_name, ontoHelicalTypes[prop_name]]);
                    }
                    var ontoFFTypes_jqplot = [];
                    for (var prop_name in ontoFFTypes) {
                        ontoFFTypes_jqplot.push([prop_name, ontoFFTypes[prop_name]]);
                    }
                    var ontoLenTypes_jqplot = [];
                    for (var prop_name in ontoLenTypes) {
                        ontoLenTypes_jqplot.push([prop_name, ontoLenTypes[prop_name]]);
                    }
                    var ontoChargeTypes_jqplot = [];
                    for (var prop_name in ontoChargeTypes) {
                        ontoChargeTypes_jqplot.push([prop_name, ontoChargeTypes[prop_name]]);
                    }
                    //alert("molTypes: " + molTypes_jqplot);

                    //var data = [
                    //  ['Heavy Industry', 12],['Retail', 9], ['Light Industry', 14],
                    //  ['Out of home', 16],['Commuting', 7], ['Orientation', 9]
                    //];
                    var plot1 = jQuery.jqplot('chartdiv1', [molTypes_jqplot],
                            {
                                seriesDefaults: {
                                    // Make this a pie chart.
                                    renderer: jQuery.jqplot.PieRenderer,
                                    rendererOptions: {
                                        // Put data labels on the pie slices.
                                        // By default, labels show the percentage of the slice.
                                        showDataLabels: true,
                                        // Add a margin to seperate the slices.
                                        sliceMargin: 4,
                                        // stroke the slices with a little thicker line.
                                        lineWidth: 5
                                    }
                                },
                                legend: {show: true, location: 'e'}
                            }
                    );
                    var plot2 = jQuery.jqplot('chartdiv2', [baseTypes_jqplot],
                            {
                                seriesDefaults: {
                                    // Make this a pie chart.
                                    renderer: jQuery.jqplot.PieRenderer,
                                    rendererOptions: {
                                        // Put data labels on the pie slices.
                                        // By default, labels show the percentage of the slice.
                                        showDataLabels: true,
                                        // Add a margin to seperate the slices.
                                        sliceMargin: 4,
                                        // stroke the slices with a little thicker line.
                                        lineWidth: 5
                                    }
                                },
                                legend: {show: true, location: 'e'}
                            }
                    );
                    var plot3 = jQuery.jqplot('chartdiv3', [bpTypes_jqplot],
                            {
                                seriesDefaults: {
                                    // Make this a pie chart.
                                    renderer: jQuery.jqplot.PieRenderer,
                                    rendererOptions: {
                                        // Put data labels on the pie slices.
                                        // By default, labels show the percentage of the slice.
                                        showDataLabels: true,
                                        // Add a margin to seperate the slices.
                                        sliceMargin: 4,
                                        // stroke the slices with a little thicker line.
                                        lineWidth: 5
                                    }
                                },
                                legend: {show: true, location: 'e'}
                            }
                    );
                    var plot4 = jQuery.jqplot('chartdiv4', [bpsTypes_jqplot],
                            {
                                seriesDefaults: {
                                    // Make this a pie chart.
                                    renderer: jQuery.jqplot.PieRenderer,
                                    rendererOptions: {
                                        // Put data labels on the pie slices.
                                        // By default, labels show the percentage of the slice.
                                        showDataLabels: true,
                                        // Add a margin to seperate the slices.
                                        sliceMargin: 4,
                                        // stroke the slices with a little thicker line.
                                        lineWidth: 5,
                                        dataLabels: 'label'
                                    }
                                },
                                legend: {show: true, location: 'e'}
                            }
                    );
                    var plot5 = jQuery.jqplot('chartdiv5', [ontoNATypes_jqplot],
                            {
                                seriesDefaults: {
                                    // Make this a pie chart.
                                    renderer: jQuery.jqplot.PieRenderer,
                                    rendererOptions: {
                                        // Put data labels on the pie slices.
                                        // By default, labels show the percentage of the slice.
                                        showDataLabels: true,
                                        // Add a margin to seperate the slices.
                                        sliceMargin: 4,
                                        // stroke the slices with a little thicker line.
                                        lineWidth: 5,
                                    }
                                },
                                legend: {show: true, location: 'e'}
                            }
                    );
                    var plot6 = jQuery.jqplot('chartdiv6', [ontoStructureTypes_jqplot],
                            {
                                seriesDefaults: {
                                    // Make this a pie chart.
                                    renderer: jQuery.jqplot.PieRenderer,
                                    rendererOptions: {
                                        // Put data labels on the pie slices.
                                        // By default, labels show the percentage of the slice.
                                        showDataLabels: true,
                                        // Add a margin to seperate the slices.
                                        sliceMargin: 4,
                                        // stroke the slices with a little thicker line.
                                        lineWidth: 5,
                                    }
                                },
                                legend: {show: true, location: 'e'}
                            }
                    );

                    var plot7 = jQuery.jqplot('chartdiv7', [ontoSystemTypes_jqplot],
                            {
                                seriesDefaults: {
                                    // Make this a pie chart.
                                    renderer: jQuery.jqplot.PieRenderer,
                                    rendererOptions: {
                                        // Put data labels on the pie slices.
                                        // By default, labels show the percentage of the slice.
                                        showDataLabels: true,
                                        // Add a margin to seperate the slices.
                                        sliceMargin: 4,
                                        // stroke the slices with a little thicker line.
                                        lineWidth: 5,
                                    }
                                },
                                legend: {show: true, location: 'e'}
                            }
                    );

                    var plot8 = jQuery.jqplot('chartdiv8', [ontoTrajectoryTypes_jqplot],
                            {
                                seriesDefaults: {
                                    // Make this a pie chart.
                                    renderer: jQuery.jqplot.PieRenderer,
                                    rendererOptions: {
                                        // Put data labels on the pie slices.
                                        // By default, labels show the percentage of the slice.
                                        showDataLabels: true,
                                        // Add a margin to seperate the slices.
                                        sliceMargin: 4,
                                        // stroke the slices with a little thicker line.
                                        lineWidth: 5,
                                    }
                                },
                                legend: {show: true, location: 'e'}
                            }
                    );

                    var plot9 = jQuery.jqplot('chartdiv9', [ontoHelicalTypes_jqplot],
                            {
                                seriesDefaults: {
                                    // Make this a pie chart.
                                    renderer: jQuery.jqplot.PieRenderer,
                                    rendererOptions: {
                                        // Put data labels on the pie slices.
                                        // By default, labels show the percentage of the slice.
                                        showDataLabels: true,
                                        // Add a margin to seperate the slices.
                                        sliceMargin: 4,
                                        // stroke the slices with a little thicker line.
                                        lineWidth: 5,
                                    }
                                },
                                legend: {show: true, location: 'e'}
                            }
                    );

                    var plot10 = jQuery.jqplot('chartdiv10', [ontoFFTypes_jqplot],
                            {
                                seriesDefaults: {
                                    // Make this a pie chart.
                                    renderer: jQuery.jqplot.PieRenderer,
                                    rendererOptions: {
                                        // Put data labels on the pie slices.
                                        // By default, labels show the percentage of the slice.
                                        showDataLabels: true,
                                        // Add a margin to seperate the slices.
                                        sliceMargin: 4,
                                        // stroke the slices with a little thicker line.
                                        lineWidth: 5,
                                    }
                                },
                                legend: {show: true, location: 'e'}
                            }
                    );

                    var plot11 = jQuery.jqplot('chartdiv11', [ontoLenTypes_jqplot],
                            {
                                seriesDefaults: {
                                    // Make this a pie chart.
                                    renderer: jQuery.jqplot.PieRenderer,
                                    rendererOptions: {
                                        // Put data labels on the pie slices.
                                        // By default, labels show the percentage of the slice.
                                        showDataLabels: true,
                                        // Add a margin to seperate the slices.
                                        sliceMargin: 4,
                                        // stroke the slices with a little thicker line.
                                        lineWidth: 5,
                                    }
                                },
                                legend: {show: true, location: 'e'}
                            }
                    );

                    var plot12 = jQuery.jqplot('chartdiv12', [ontoChargeTypes_jqplot],
                            {
                                seriesDefaults: {
                                    // Make this a pie chart.
                                    renderer: jQuery.jqplot.PieRenderer,
                                    rendererOptions: {
                                        // Put data labels on the pie slices.
                                        // By default, labels show the percentage of the slice.
                                        showDataLabels: true,
                                        // Add a margin to seperate the slices.
                                        sliceMargin: 4,
                                        // stroke the slices with a little thicker line.
                                        lineWidth: 5,
                                    }
                                },
                                legend: {show: true, location: 'e'}
                            }
                    );

                    // Ontology Cascade Sheet
                    $(".header").click(function () {

                        $header = $(this);
                        //getting the next element
                        $content = $header.next();
                        //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
                        $content.slideToggle(500)

                        $plotid = $content.attr('id');
                        eval($plotid).replot();
                    });

                });

            </script>
            <script type="text/javascript">
                function toggleVis(a) {
                    ob = document.getElementById(a);
                    if (ob.style.visibility == 'hidden') {
                        ob.style.visibility = 'visible';
                        ob.style.display = 'inline';
                    } else {
                        ob.style.visibility = 'hidden';
                        ob.style.display = 'none';
                    }
                }
                function ClipBoard() {
                    holdtext.innerText = copytext.innerText;
                    Copied = holdtext.createTextRange();
                    Copied.execCommand("Copy");
                }
                function validateUpload(form, max) {
                    input = document.getElementById('fn');
                    for (idx = 0; idx < input.files.length; ++idx) {
                        file = input.files[idx];
                        if (file && file.size > max) {
                            alert("Not enough space to upload " + file.name + " (" + file.size + "b). Free space is only " + max + "b.");
                            form.fn.value = null;
                            return FALSE;
                        }
                    }
                    form.op.value = 'uploadFile';
                    form.submit();
                }
                </script>

            <h3>BIGNaSim database structure and analysis portal for nucleic acids simulation data</h3>

            <div class="metaImageSection">   
