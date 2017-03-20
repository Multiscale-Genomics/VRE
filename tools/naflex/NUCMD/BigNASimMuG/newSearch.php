<?php
require "phplib/global.inc.php";
if ($_REQUEST['new'])
    unset($_SESSION['inputData']);
#
$_SESSION['inputData']['new'] = 1;
if (!$_SESSION['inputData']['seqMin'])
    $_SESSION['inputData']['seqMin'] = '0.0';
if (!$_SESSION['inputData']['seqMax'])
    $_SESSION['inputData']['seqMax'] = '120';
if (!$_SESSION['inputData']['timeMin'])
    $_SESSION['inputData']['timeMin'] = '0.0';
if (!$_SESSION['inputData']['timeMax'])
    $_SESSION['inputData']['timeMax'] = '1000000';
if (!isset($_SESSION['inputData']['limit']))
    $_SESSION['inputData']['limit'] = 100;
if (!isset($_SESSION['inputData']['anyType']))
    $_SESSION['inputData']['anyType'] = 'on';
#
$_SESSION['inputData']['typeForm'] = '';        
foreach (array_values($GLOBALS['simData']->distinct("moleculeType")) as $val) {
    if ($val) 
        $_SESSION['inputData']['typeForm'] .= getCBox('typeForm['.$val.']', True, $_SESSION['inputData']['typeForm'][$val], $val, True,'onclick="document.searchForm.anyType.checked=false"')." ";
}

$ontoCount = array();
foreach ($ontoHash2 as $keyOnto => $valueOnto){
	$rex = new MongoRegex("/^$ontoHash2[$keyOnto]/");
	$ontoCount[$keyOnto] = $GLOBALS['simData']->count(array('ontology' => $rex));
}

# Force Fields Selector
$listFFList = array();
$listFFList['Select Force Field'] = 'Select Force Field:';
foreach (array_values($GLOBALS['simData']->distinct("forceField")) as $val) {
    $val = ucfirst($val);
    $val = preg_replace("/bsc/","BSC",$val);
    if ($val) {
        $listFFList["$val"] = "$val";
    }
}

# Force Fields Selector
$listLengthList = array();
$listLengthList['Select Simulation Length'] = 'Select Simulation Length:';
$listLengthList['Nanosecond Range'] = "NanoSecondRange";
$listLengthList['Microsecond Range'] = "MicroSecondRange";

# Temperature Selector
$listTempList = array();
$listTempList['Select Temperature'] = 'Select Temperature:';
$listTempList["Physiological Temperature (298K to 300K)"] = "PhysiologicalTemp";
$listTempList["Non Physiological Temperature"] = "NonPhysiologicalTemp";

# Water Type Selector
$listWaterList = array();
$listWaterList['Select Solvent Type'] = 'Select Water Type:';
foreach (array_values($GLOBALS['simData']->distinct("Water")) as $val) {
    if ($val) {
        $listWaterList["Water $val"] = "$val";
    }
}

# Ions Parameters Selector
$listIonsList = array();
$listIonsList['Select Ions Parameters'] = 'Select Ions Parameters:';
foreach (array_values($GLOBALS['simData']->distinct("IonsParameters")) as $val) {
    if ($val and $val != "-") {
        $listIonsList["$val"] = "$val";
    }
}

# Ionic Concentration Selector
$listIonicConcList = array();
$listIonicConcList['Select Ionic Concentration'] = 'Select Ionic Concentration:';
$listIonicConcList["Electroneutral"] = "Electroneutral";
$listIonicConcList["Added Salt: Physiological (0.15M)"] = "Physiological Charge";
$listIonicConcList["Added Salt: Non-Physiological"] = "NonPhysiological Charge";

$listBPS = array();
$listBPS['BPS'] = "Select Base Pair Step";
$listBPS['GC'] = "GC";
$listBPS['GT'] = "GT";
$listBPS['AC'] = "AC";
$listBPS['AT'] = "AT";
$listBPS['CG'] = "CG";
$listBPS['TG'] = "TG";
$listBPS['CA'] = "CA";
$listBPS['TA'] = "TA";
$listBPS['CC'] = "CC";
$listBPS['CT'] = "CT";
$listBPS['TT'] = "TT";
$listBPS['TC'] = "TC";
$listBPS['AA'] = "AA";
$listBPS['AG'] = "AG";
$listBPS['GG'] = "GG";
$listBPS['GA'] = "GA";

#
print headerMMB("BIGNASim Database", array(), False);
?>
<script>
$(document).ready( function() {

        menuTabs("Search");

	// Ontology Cascade Sheet
        $(".header").click(function () {

            $header = $(this);
            //getting the next element
            $content = $header.next();
            //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
            $content.slideToggle(500, function () {
                //execute this after slideToggle is done
                //change text of header based on visibility of content div

                //var currentId = $header.attr('id');
                var text = $header.text();

                $header.text(function () {
                    //change text based on condition
                    var expand = text.replace(" (Click to Collapse)","");
                    return $content.is(":visible") ? text+" (Click to Collapse)" : expand;
                });
            });

        });


  	$("#idSubSeq").change(function(event) {
		var requestData = $("#searchForm").serialize();
        	$.post('getCountMongo.php', requestData, function(data) {
			var obj = $.parseJSON(data);
			$("#mongoCount").hide().html("Results found: ("+ obj.count + ")").fadeIn();
	        });
	});

  	$("#ontology input, #ontology select, #searchBPS select").click(function(event) {
		var requestData = $("#searchForm").serialize();
	        $.post('getCountMongo.php', requestData, function(data) {
	console.log(data);
			var obj = $.parseJSON(data);
			$("#mongoCount").hide().html("Results found: ("+ obj.count + ")").fadeIn();
        	});
	});

/*  	$("#idSubSeq").change(function(event) {
		var requestData = $("#searchForm").serialize();
	        $.post('getCountMongo.php', requestData, function(data) {
			var obj = $.parseJSON(data);
			$("#mongoCount").hide().html("Results found: ("+ obj.count + ")").fadeIn();
        	});
	});
*/
	var $loading = $('<div id="loading"><img src="images/loading2.gif"></div>').insertBefore('#searchBPS');

	$(document).ajaxStart(function() {
		$loading.show();
	}).ajaxStop(function() {
		$loading.hide();
	});

	// Ajax without jquery to use "success" handler for a specific function:
  	$("#idSubSeqPDB").bind('input',function(event) {
		$.ajax({
			url: "getSequenceFromPDB.php",
			data: { idPdb: $(this).val() },
			type: "POST",
			success: function(response) {
	                        $("#idSubSeq").val(response);
			}
		}).done(function(response) {
			$("#idSubSeq").trigger("change");
		});
	});
	

});
</script>
<h3>BIGNASim database structure and analysis portal for nucleic acids simulation data</h3>
<div class="form">
<!--
<pre>
        Sess: <?=session_id();?>
        Sess: <?=$_SESSION['BNSId']?>
</pre>
-->
<form name="searchForm" id="searchForm" action="search.php" method="post">

	<h4 style="font-size: 150%"> Search by Sequence </h4> <hr>
	<div class="searchDiv" id="searchSequence" style="background:beige";>
	<p class="contact" style="float:left;"><label for="Sequence">Sequence (Regular Expressions Allowed)<br/><kbd>Ex: GG[GTAC]GG</kbd></label></p> 
	<input id="idSubSeqPDB" name="idSubSeqPDB" placeholder="Seq from PDBCode" tabindex="1" type="text" style="width: 16%; margin: 10px; float:right;">
	<input id="idSubSeq" name="idSubSeq" placeholder="Nucleotide [Sub]Sequence" tabindex="1" type="textarea">
	</div>

	<br/><br/>

	<h4 style="font-size: 150%"> Search by Base Pair Step </h4> <hr>
	<div class="searchDiv" id="searchBPS" style="background:beige";>
	<p class="contact"><label for="idBPS">Base Pair Step / Flanking Region <i>(Number of bases)</i></label></p> 
	<select class="select-style" name="idBPS">
	<?php
	foreach ($listBPS as $key => $value){
		echo "<option title='".$key."' value='".$value."'>".$value."</option>";
	}
	?>
	</select>
	<input id="flank" name="flank" placeholder="#nucs" tabindex="1" type="num"> 
	<br/>
	</div>

	<br/><br/>

	<h4 style="font-size: 150%"> Search by Ontology </h4> <hr>
	<div class="searchDiv" id="ontology" style="background:beige";>
	<p class="contact"><label for="ontoType">Ontology Search:</label><label style="text-align: right;display:block;color:red;" id="mongoCount"></label></p> 

<?php
	include "htmlib/onto.inc.html";
?>
	</br>
	</div>
	</br></br>

	<input class="buttom" type="submit" value="Search"> <input class="buttom" type="reset" value="Reset">

</form>
</div>


<?php
print footerMMB();
