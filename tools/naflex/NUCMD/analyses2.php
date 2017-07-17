<?php
require "phplib/global.inc.php";
require "phplib/aux.inc.php";
print headerMMB("BIGNASim database structure and analysis portal for nucleic acids simulation data");

# Getting Trajectory ids to look at (coming from Search or Browse)
$trajs = array();
foreach (array_keys($_REQUEST) as $k) {
	if (preg_match('/NAFlex/',$k)) {
		array_push($trajs,$k);
	}
}
# Converting php array to Json to use it in jQuery Ajax (mongo queries)
$trajsJson = json_encode($trajs);

# String with all trajectories id, to be shown in the header of the page.
$selection = implode(', ',$trajs);

# Subsequence queried, to be emphasized in Sequences shown.
$idSubSeqCentral = $_REQUEST['idSubSeq'];
$idSubSeq = $idSubSeqCentral;

# Adding flanking region to the sequence queried.
$flank = $_REQUEST['flank'];
$flankUp = str_repeat('X',$flank);
$flankDown = str_repeat('X',$flank);
$idSubSeqFull = $flankUp.$idSubSeqCentral.$flankDown;
if(empty($idSubSeqFull)) { 
	$idSubSeqFull = "ALL_NUCS";
}

# Saving Trajs and Fragment in session, to be used in a possible subsequent MetaTrajectory generation.
$_SESSION['trajs'] = $trajs;
$_SESSION['subSeq'] = $idSubSeqCentral;
$_SESSION['flank'] = $flank;

# Converting sequence to Json to use it in jQuery Ajax (mongo queries)
$seqJson = json_encode($idSubSeqFull);

# Now splitting subsequence into possible different 
# bases/base-pairs/base-pair steps to query the DB.

# Subsequence to bases:
$idSubSeqOnlyChars = preg_replace('/\PL/u', '', $idSubSeq);
$basesTxt = str_split($idSubSeqOnlyChars);
#$basesTxt = preg_replace('/\PL/u', '', $basesTxt)

# Subsequence to base-pairs:
$pair = array();
$pair['A'] = 'T';
$pair['C'] = 'G';
$pair['G'] = 'C';
$pair['T'] = 'A';
$pair['U'] = 'A';

$bases = array();
$basesMulti = array();
$done = array();
for ($i=0;$i<sizeof($basesTxt);$i++){
	#$bases = array("A/T" => "A","C/G" => "C");
	#$bases[$basesTxt[$i]] = 1;

	if($done[$basesTxt[$i]]){
		continue;
	}

	$code = $basesTxt[$i]."/".$pair[$basesTxt[$i]];
	$bases[$code] = $basesTxt[$i];
	
	if($basesTxt[$i] == "A" or $basesTxt[$i] == "G"){
		$basesMulti["R"] = "R";
	} 
	if($basesTxt[$i] == "T" or $basesTxt[$i] == "C"){
		$basesMulti["Y"] = "Y";
	} 

	$done[$basesTxt[$i]] = 1;
	$done[$pair[$basesTxt[$i]]] = 1;
}
$basesMulti["N"] = "N";

# Subsequence to base-pairs:

$basepairs = array();
$basepairsMulti = array();
for ($i=0;$i<sizeof($basesTxt);$i++){
	$pair2 = $pair[$basesTxt[$i]]; 
	$code = "$basesTxt[$i]-$pair2";
	$codeRev = "$pair2-$basesTxt[$i]";

	if($done[$code]){
		continue;
	}

	$basepairs[$code] = "$basesTxt[$i]$pair2";

	if($code == "T-A" or $code == "C-G"){
		$basepairsMulti["Y-R"] = "YR";
	} 
	if($code == "G-C" or $code == "A-T"){
		$basepairsMulti["R-Y"] = "RY";
	} 

	$done[$code] = 1;
	$done[$codeRev] = 1;
}
#$basepairsMulti["N-N"] = "NN";

# Subsequence to base-pair steps:

$pairs = array();
$pairsMulti = array();
for ($i=0;$i<sizeof($basesTxt)-1;$i++){
	$pair = $basesTxt[$i].$basesTxt[$i+1];
	$pairs[$pair] = 1;

	if($pair == "GC" or $pair == "GT" or $pair == "AC" or $pair == "AT"){
		$pairsMulti["RY"] = "RY";
	} 
	if($pair == "CG" or $pair == "TG" or $pair == "CA" or $pair == "TA"){
		$pairsMulti["YR"] = "YR";
	} 
	if($pair == "CC" or $pair == "CT" or $pair == "TT" or $pair == "TC"){
		$pairsMulti["YY"] = "YY";
	} 
	if($pair == "AA" or $pair == "AG" or $pair == "GG" or $pair == "GA"){
		$pairsMulti["RR"] = "RR";
	} 
}
$pairsMulti["NN"] = "NN";

#foreach ($basepairs as $pair => $v){
#	echo "$pair $v<br/>";
#}

# Header with Trajectories and Subsequence info.
if($selection) {
	$selection = "/$selection/"; 
}
else{
	$selection = "/ALL DB trajectories/"; 
}
$filter = "<mark style='background-color: #FF9900;'>$flankUp</mark><mark>$idSubSeqCentral</mark><mark style='background-color: #FF9900;'>$flankDown</mark>";

# Now, if coming from Global Analyses or Browse, not from Search,
# we need to look for any kind of bases/base-pairs/base-pair steps
if(empty($idSubSeq)){
 $bases = array("A" => A,"C" => C,"G" => G,"T" => T);
 #$bases = array("A/T" => "A","C/G" => "C");
 $basesMulti = array("R" => 1,"Y" => 1,"N" => 1);
 #$basepairs = array("A-T" => "AT","C-G" => "CG","T-A" => "TA","G-C" => "GC");
 $basepairs = array("A-T/T-A" => "AT","C-G/G-C" => "CG");
 #$basepairsMulti = array("R-Y" => "RY", "Y-R" => "YR");
 $basepairsMulti = array("R-Y/Y-R" => "RY");
 #$pairs = array("GC" => 1,"GT" => 1,"AC" => 1,"AT" => 1,"CG" => 1,"TG" => 1,"CA" => 1,"TA" => 1,"CC" => 1,"CT" => 1,"TT" => 1,"TC" => 1,"AA" => 1,"AG" => 1,"GG" => 1,"GA" => 1);
 $pairs = array("GC" => 1,"GT" => 1,"GG" => 1,"GA" => 1,"AC" => 1,"AT" => 1,"AA" => 1,"AG" => 1,"CG" => 1,"CC" => 1,"CT" => 1,"CA" => 1,"TA" => 1,"TG" => 1,"TT" => 1,"TC" => 1);
 $pairsMulti = array("RY" => "RY", "YR" => "YR", "YY" => "YY", "RR" => "RR", "NN" => "NN");
}
else{
	$searchResultsSelected = array();
	foreach ($_SESSION['searchResults'] as $k => $v){
		if(isset($_REQUEST[$k]))
			$searchResultsSelected[$k] = $v;
	}

	$listIds = getListIds($searchResultsSelected,$idSubSeq,$flank,$simData);
	$_SESSION['metaTrajList'] = $listIds;
}

?>
<script>
jQuery(document).ready(function(){

  menuTabs("advSearch");

  imagePreview();

  var $loading = $('<div id="loading"><img src="images/loading2.gif"></div>').insertBefore('#advSearchAjax');
  //var $loading = $('<div id="loading"><img src="images/loading2.gif"></div>').insertBefore('#searchGroups');

  $(document).ajaxStart(function() {
	$loading.show();
  }).ajaxStop(function() {
	$loading.hide();
  });

  var toScroll = false;
  $(document).on("click",".metaImage a[name^='block']", function(event) {
	event.preventDefault();

	// Form request data: 'id' attribute from the <a> tag that have been clicked.
	var requestData = {anal: $(this).attr('id'), filterTrajs : <?=$trajsJson?>, subSeq : <?=$seqJson?>};

	// Adding new div section if it is not already there.
	var name = $(this).attr('name');
	if(document.getElementById("block1") == null) {
		$('#ajaxSection').append($('<div class="metaImage" id="'+name+'"></div>'));
	}

	// Connecting to the server and calling php function with request data.
	// Adding data to the newly generated div, with fading effect.
	// We need to explicitly remove the style attribute that is being automatically 
	// added by hide()/fadeIn() functions.
	$.post('queryMongo4.php', requestData, function(data) {
	    $("#block1").hide().html(data).fadeIn().removeAttr('style');
	});

	// Automatically scroll down to the new ajax-generated div
	// Only when selecting a group (first level)
	// Scroll down from actual position to (document_height - footer height - 100)
	var id = $(this).attr('id');
	if(id.match(/,1/)) {
		toScroll = true;
		//var document_height = $(document).height();
		//var footer_height = $("#footer-area").height();
		//var content_height = document_height - footer_height;
		//var content_scroll_pos = $(window).scrollTop();
		//var scroll = content_height - content_scroll_pos;
		//$('html, body').animate({scrollTop:scroll}, 'slow');
	}

  });

  // After Ajax Query is done:
  $(document).ajaxComplete(function() {

	// Re-activate image preview
	imagePreview();
	jqueryImages=$(".jqueryImages").jqueryImages();

	// Activate expandable table (query results)
	//$("#expandable").find('tr:first').parent().children().addClass("odd");
	$("#expandable > tbody >  tr:even").addClass("odd");
	$("#expandable > tbody > tr:not(.odd)").hide();
	$("#expandable > tbody >  tr:first-child").show();
	$("#expandable > tbody >  tr.odd").click(function(){
		//$(this).next("tr").slideToggle(1000);
		$(this).next("tr").slideToggle();
		$(this).find(".arrow").toggleClass("up");
	});

	// Automatically scroll down to the new ajax-generated div
	// Only when selecting a group (first level)
	// Scroll down from actual position to (document_height - footer height - 100)
	if( toScroll ) {
		var document_height = $(document).height();
		var footer_height = $("#footer-area").height();
		var content_height = document_height - footer_height - 100;
		var content_scroll_pos = $(window).scrollTop();
		var scroll = content_height - content_scroll_pos;
		$('html, body').animate({scrollTop:scroll}, 'slow');
		toScroll = false;
	}

	$('.tooltipLink').mouseover(function(){
		eleOffset = $(this).offset();
 		$(this).next().fadeIn("fast").css({
	 		//left: eleOffset.left + $(this).outerWidth(),
 			//left: eleOffset.left,
			left: eleOffset.left-100,
			top: eleOffset.top-250
	        });
	}).mouseout(function(){
		$(this).next().hide();
	});

  });

  // Hide / Show whole list of trajectories used in the query.
  $('#titleComplete').hide();
  $('a.trajSelectionHide').click(function(event){
        event.preventDefault();

	var $link = $(this);
	if($link.text() == '\(see less\)') {
		$('#titleComplete').hide();
		$('#titleReduced').animate({
			opacity: 'toggle',
			height: 'toggle'
		}, 'slow');
	}
	else{
		$('#titleReduced').hide();
		$('#titleComplete').animate({
			opacity: 'toggle',
			height: 'toggle'
		}, 'slow');
	}	
  });

		
});

</script>

<h3>BIGNASim database structure and analysis portal for nucleic acids simulation data</h3>

<div class="metaImageSection" id="ajaxSection">
    <div class="metaImage" id="advSearchAjax">
        <hr/>
		<?php 

			$cassandra = '';
			if($idSubSeq){				
				$cassandra = "<form name=\"retrieve\" id=\"retrieve\" action=\"metatrajFragment.php\" method=\"post\"><div style=\"display: inline-block;\">\n<input class=\"metaTrajButton\" style=\"color: #666666;\" id=\"MetaTraj\" type=\"submit\" class=\"unselected\" title=\"Retrieve Metatrajectory\" value=\"Retrieve MetaTrajectory\">\n<img style=\"float: right;width: 70px; margin-top: 5px; height:50px; padding:0px;\" src=\"images/cassandra_logo_small.png\"></div></form>\n";
			}

			$comes = preg_match_all('/,/',$selection,$pp);
			if($comes > 5){
				$arrTxt = preg_split('/,/',$selection);
				$reduced = "$arrTxt[0], $arrTxt[1], $arrTxt[2], $arrTxt[3], $arrTxt[4]";
				$rest = $comes - 5;
				$reduced.= ", <a href='' class='trajSelectionHide'>and $rest more...</a>/";
				$complete = $selection; 
				#$complete = str_replace('/','',$selection);
				print "<div id='titleReduced' class='mongoFilter'> $reduced <br><br> <p> $filter </p> $cassandra </div>\n";
				print "<div id='titleComplete' class='mongoFilter'> $complete <a href='' class='trajSelectionHide'>(see less)</a><br><br> <p> $filter </p> $cassandra </div>\n";
			}
			else{
				print "<div class='mongoFilter'> $selection <br><br> <p> $filter </p> $cassandra </div>\n";
			}
		?>
                <h4>Global Analyses, select group:</h4>
        <hr/>
<!--
<div>
        <a class='tooltipLink' id='tooltip1' href='#'>Ejemplo básico de Tooltip</a>
        <div class='tooltip'>Contenido del Tooltip 1</div>
</div>
-->
	<div id="containerSelection">
  	   <div id="helpSelection">
		<div class="tab2div_container">
			<!--<p> Base Analyses </p>-->
			<div class='col base-head'>Base Id <br/> (# of Appearances)</div>
		</div>
		<div class="tab2div_container">
			<!--<p> Base Pair Analyses </p>-->
			<div class='col base-pair-head'>Base-Pair Id <br/> (# of Appearances)</div>
		</div>
		<div class="tab2div_container">
			<!--<p> Base Pair Step Analyses </p>-->
			<div class='col base-pair-step-head'>Base-Pair Step Id <br/> (# of Appearances)</div>
		</div>

	   </div>

	<div class="clear"></div>

  	   <div id="bulletsSelection">
	      <div class="tab2div_container">
		<div class="table-row">
<?php

	# Base Analysis A/T,C/G
	foreach ($bases as $base=>$v) {
		$stats = getMongoStats($v,$trajs);
		print "<a id='$v,1' class='col base' name='block1' title='BASE Analysis, $base' href=''>$base<br/> ($stats)</a>";
	}
	print "</div><div class='table-row'>";
	# Base Analysis R,Y
	foreach ($basesMulti as $base=>$v) {
		$stats = getMongoStats($base,$trajs);
		print "<a id='$base,1' class='col base' name='block1' title='BASE Analysis, $base' href=''>$base<br/> ($stats)</a>";
	}

	print "</div>";
	print "</div>";
	print "<div class='tab2div_container'><div class='table-row'></div>";
	print "<div class='table-row'>";

	# Base-Pair Analysis A-T/T-A,C-G/G-C
	foreach ($basepairs as $pair => $v){
		$stats = getMongoStats($v,$trajs);
		print "<a id='$v,1' class='col base-pair' name='block1' title='BASE-PAIR Analysis, $pair' href=''>$pair<br/> ($stats)</a>";
	}
	print "</div><div class='table-row'>";
	# Base-Pair Analysis R-Y/Y-R
	foreach ($basepairsMulti as $pair => $v){
		$stats = getMongoStats($v,$trajs);
		print "<a id='$v,1' class='col base-pair' name='block1' title='BASE-PAIR Analysis, $pair' href=''>$pair<br/> ($stats)</a>";
	}

	print "</div>";
	print "</div>";
	print "<div class='tab2div_container'><div class='table-row'></div>";
	print "<div class='table-row'>";

	# Base-Pair Step Analysis GC,GT,GG,GA,AC,AT,AA,AG,CG,CC,CT,CA,TA,TG,TT,TC
	$cont = 0;
	foreach ($pairs as $pair => $v){
		if($cont and $cont % 4 == 0){
			print "</div><div class='table-row'>";
		}
		$stats = getMongoStats($pair,$trajs,"step");
		print "<a id='$pair,1,step' class='col base-pair-step' name='block1' title='BASE-PAIR STEP Analysis, $pair' href=''>$pair<br/> ($stats)</a>";
		$cont ++;
	}
	# Base-Pair Step Analysis RY,YR,YY,RR 
	print "</div><div class='table-row'>";
	foreach ($pairsMulti as $pair => $v){
		$stats = getMongoStats($pair,$trajs,"step");
		print "<a id='$pair,1,step' class='col base-pair-step' name='block1' title='BASE-PAIR STEP Analysis, $pair' href=''>$pair<br/> ($stats)</a>";
	}
?>
		</div>
	      </div>
	    </div>
	<div><p style="font-style:oblique; clear:both;"><strong>INDEX:</strong> Adenine (<strong>A</strong>), Cytosine (<strong>C</strong>), Guanine (<strong>G</strong>), Thymine (<strong>T</strong>), Purine (<strong>R</strong>), Pyrimidine (<strong>Y</strong>), All (<strong>N</strong>)</p></div>
	  </div>
	</div>
    </div>
<?php
print footerMMB();

?>

