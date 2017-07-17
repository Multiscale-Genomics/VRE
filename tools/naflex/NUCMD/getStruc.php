<?php
# getStruc.php#
require "phplib/global.inc.php";
require "phplib/aux.inc.php";
#
?>

<?php
$data = getNUCDBData($_REQUEST['idCode'], True);
if (!$_REQUEST['idCode'] or !$data['_id']) {
    print errorPageMMB("Error", "<h3>Unknown</h3>");
    exit;
}
$data['idCodelc'] = strtolower($data['_id']);
$path = $GLOBALS['parmbsc1Dir']."/".$data['_id'];

$_SESSION['idTraj'] = $data['_id'];

if ( $_SESSION['User'] )
	$idSession = $_SESSION['User']['id'];
else
	$idSession = $_SESSION['BNSId'];

#

$external_ap = "bignasim";
require "../master/header.php";

print headerMMB("PDB Structure " . $_REQUEST['idCode'], "");
?>

<div id="pagewrap_vre">

   <div class="breadcrumb-trail breadcrumbs" itemprop="breadcrumb"><span class="trail-begin"><a href="/MuGVRE" title="Virtual Research Environment" rel="home" class="trail-begin">Home</a></span> <span class="sep">&raquo;</span> <span class="trail-end">BigNASim</span></div>



<script>

$(document).ready( function() {

        menuTabs("Search");
	$('.toHide').hide();
	imagePreview();

	$('.expandable').click(function(event) {
		event.preventDefault();
		//$('.toHide').slideUp('slow');
		$(this).parent().find(".toHide").slideToggle('slow');
		//if( $(this).text() ){
		//	alert( $(this).text());
		//	var txt = $(this).text();
		//	var newtxt = '';
		//	if(txt.match(/expand/)) { newtxt = txt.replace("expand","shrink"); }
		//	if(txt.match(/shrink/)) { newtxt = txt.replace("shrink","expand"); }
		//	$(this).text(newtxt);
		//}
		//$('.toHide').slideToggle('slow');		
	});

	// Only for IE, we need to change video format for the trajectory video popup (jqueryImage)
	var ms_ie = false;
	var ua = window.navigator.userAgent;
	var old_ie = ua.indexOf('MSIE ');
	var new_ie = ua.indexOf('Trident/');

	if ((old_ie > -1) || (new_ie > -1)) {
		ms_ie = true;
	}

	if ( ms_ie ) {
		// Modifying video format from ogv to mp4 in jqueryImages
		var h = $('#VideoFormat').attr('href');
		var newh = h.replace('ogv','mp4');
		$('#VideoFormat').attr('href',newh);
		var h2 = $('#VideoFormat').attr('href');
		jqueryImages=jQuery(".jqueryImages").jqueryImages();
	}


	$('#UMM').toggle();
	$( "#ummLink" ).click(function(event) {
                event.preventDefault();
                //$('#UMM').slideToggle('slow');
                //$('#reducedUMM').slideToggle('slow');
                var $infoUmm = $('#reducedUMM');
                var $UMM = $('#UMM');
                //$UMM.fadeToggle('slow', function() {
                //      $infoUmm.fadeToggle('slow');
                //});
                $UMM.slideToggle('slow', function() {
                        $infoUmm.slideToggle('slow');
                });
                var link = $(this).html();
                //var res = link.replace("full", "reduced");
                if(link.match(/full/)){ 
                        $t = "Unified Molecular Modeling (UMM) Metadata<br/><i>(Click to see <strong>reduced</strong> UMM)</i>";
                }
                else{
                        $t = "Unified Molecular Modeling (UMM) Metadata<br/><i>(Click to see <strong>full</strong> UMM)</i>";
                }
                $(this).html($t);
        });

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

</script>

<script src="js/video-js/video.js"></script>
<style type="text/css"> .vjs-default-skin .vjs-volume-control { display: none; } </style>
<?php 

foreach($data as $key=>$value) {
	if(!$value){
		$data[$key] = "-";
	}
}

#$ff = $data['forceField'];
$ff = "parmBSC1";

#$frames = $data['Frames'];
#$frameStep = $data['FrameStep'];
#$time = "-";

#if(preg_match('/[np]s/',$frameStep,$matches)){
#	$units = $matches[0];
#	$fs = preg_replace('/$units/', '', $frameStep);
#	$time = $frames * $fs;
#	if ($units == "ps"){
#		$time = $time/1000;
#		$time = "$time ns";
#	}
#	else{
#		$time = "$time $units";
#	}
#	#echo "$time $units";
#}

$pdb = "-";
#if($data['PDB'] and $data['PDB'] != 'No'){
if($data['PDB'] and preg_match('/^\d+\w{3}$/',$data['PDB'])){
	$code = strtoupper($data['PDB']);
	$pdb = '<a target="_blank" href="'.$GLOBALS['homePDB'].'getStruc.php?idCode='.$code.'" > [PDB] </a>';
	$nucdb = '<a target="_blank" href="http://ndbserver.rutgers.edu/service/ndb/atlas/summary?searchTarget='.$code.'" > [NDB] </a>';
}

?>

<h3>Entry: <?=$data['_id']?></h3>

<div class="metaImageSection">
    <div class="metaImage">
	<hr/>
		<h4>Nucleic Acid Data:</h4>
	<hr/>
<!--
<pre>
        Sess: <?=session_id();?>
        Sess: <?=$_SESSION['BNSId']?>
</pre>
-->	
	<div style="max-width: 40%">
		<table style="border: 1px solid #ddd; background:#eee; ">
                <?php
                        if(isset($data['sequenceMulti'])) {
                                foreach ($data['sequenceMulti'] as $k => $v){
                                        print "<tr>\n";
                                        print "<td><strong>Sequence $k:</strong></td><td style='word-break: break-all;'>$v</td>\n";
                                        print "</tr>\n";
                                }
                        }
                        else{
                ?>
                        <tr>
                          <td><strong>Sequence</strong></td><td style="word-break: break-all;"><?=$data['sequence']?></td>
                        </tr>
                        <tr>
                          <td><strong>Rev. Sequence</strong></td><td style="word-break: break-all;"><?=$data['rev_sequence']?></td>
                        </tr>
                <?php
			}
		?>
			<tr>
			  <td><strong>Type</strong></td><td><?=$data['NucType']?></td>
			</tr>
			<tr>
			  <td><strong>SubType</strong></td><td><?=$data['SubType']?></td>
			</tr>
			<tr>
			  <td><strong>Chains</strong></td><td><?=$data['Chains']?></td>
			</tr>
			<tr>
			  <td><strong>Pdb</strong></td><td><?=$code?><?=$pdb?><?=$nucdb?></td>
			</tr>
			<tr>
			  <td><strong>Ligands</strong></td><td><?=$data['Ligands']?></td>
			</tr>
			<tr>
			  <td><strong>Keywords</strong></td><td style="word-break: break-all;"><i><?=$data['description']?></i></td>
			</tr>
		</table>
	</div>
	<div>
		<img id="snapshot" alt="" src="getPict.php?idSim=<?=$data['_id']?>" />
	</div>
<?php
	$proj = strtolower($code);

	# pdb1u9s.ent-250.gif
	#$fileName = "pdb$proj.ent-250.gif";
	#$file = "$plots2D/$fileName";
	$fileNameBig = "pdb$proj.ent-500.gif";
	$fileBig = "$plots2D/$fileNameBig";

	#if (file_exists($file) && is_readable($file) and file_exists($fileBig) && is_readable($fileBig)) {
	if (file_exists($fileBig) && is_readable($fileBig)) {
?>
	<div>
	  <a name="plot" href="<?=$GLOBALS['RNAView']?>/<?=$fileNameBig?>" class="jqueryImages">
		<img style="width: initial;" src="<?=$GLOBALS['RNAView']?>/<?=$fileNameBig?>">
	  </a>
	</div>
<?php
	}
?>
</div>
</div>

<div class="metaImageSection">
    <div class="metaImage">
	<hr/>
		<h4 class="expandable"><a href="">MD Simulation >> <i>(Click to expand/shrink)</i></a></h4>
	<hr/>

	<div class="metaImage toHide" id="toHide2" style="border: none;">
	<div>
		<table id="reducedUMM" style="border: 1px solid #ddd; background:#eee;">
			<tr>
				<th colspan="2">Simulation Metadata</th>
			</tr>
			<tr>
			  <td><strong>Force Field</strong></td><td><?=$ff?></td>
			</tr>
			<tr>
			  <td><strong>Simulation Date</strong></td><td><?=$data['date']?></td>
			</tr>
			<tr>
			  <td><strong>Simulated Time</strong></td><td><?=$data['time']?></td>
			</tr>
			<tr>
			  <td><strong>Time Step</strong></td><td><?=$data['FrameStep']?></td>
			</tr>
			<tr>
			  <td><strong>Parts</strong></td><td><?=$data['Parts']?></td>
			</tr>
			<tr>
			  <td><strong>Temperature</strong></td><td><?=$data['Temperature']?></td>
			</tr>
			<tr>
			  <td><strong>Water</strong></td><td><?=$data['Water']?></td>
			</tr>
			<tr>
			  <td><strong>Additional Solvent</strong></td><td><?=$data['AdditionalSolvent']?></td>
			</tr>
			<tr>
			  <td><strong>Counter Ions</strong></td><td><?=$data['CounterIons']?></td>
			</tr>
			<tr>
			  <td><strong>Ionic Concentration</strong></td><td><?=$data['IonicConcentration']?></td>
			</tr>
			<tr>
			  <td><strong>Additional Ions</strong></td><td><?=$data['AdditionalIons']?></td>
			</tr>
			<tr>
			  <td><strong>Additional Molecules</strong></td><td><?=$data['AdditionalMolecules']?></td>
			</tr>
			<tr>
			  <td><strong>Ions Parameters</strong></td><td><?=$data['IonsParameters']?></td>
			</tr>
		</table>
	<div>
                <a id="ummLink" href=""> Unified Molecular Modeling (UMM) Metadata<br/><i>(Click to see <strong>full</strong> UMM)</i></a>
		<table id="UMM" style="border: 1px solid #ddd; background:#eee; margin-top: 10px;">
			<tr>
				<th colspan="2">Simulation Metadata (UMM)</th>
			</tr>
		<?php	
			ksort($data);
			$cont = 1;
			#print "<tr>";
			foreach($data as $key=>$value) {
				if($key == "idCodelc" or $key == "ontology" or preg_match('/Web$/',$key)){ continue;}
                                print "<tr><td><strong>$key</strong></td><td style=\"word-break: break-all; -ms-word-break: break-all;\">$value</td></tr>\n";
 				#print "<td><strong>$key</strong></td><td>$value</td>\n";
				#if ($cont % 2 == 0) {print "</tr><tr>";};
				$cont++;
			}
		?>
		</table>
	</div>

	</div>
        <div id="videoTraj">
                <!--<video class="video-js vjs-default-skin" id="" width="300" height="300"  poster="<?=$path?>/INFO/structure.png" data-setup='{"controls":true}'>-->
                <!--<video style="border: 1px solid #ddd; margin-left:auto; margin-right:auto;" class="video-js vjs-default-skin" id="video_dna" width="200" height="200" data-setup='{"controls":true}'>-->
               <!-- <video style="border: 1px solid #ddd; margin-left:auto; margin-right:auto;" id="video_dna" width="200" height="200" controls>
                <source src="<?=$path?>/INFO/structure.ogv" type="video/ogg"/>
                <source src="<?=$path?>/INFO/structure.mp4" type="video/mp4"/>
                </video>-->

		<div id='jmolLink' style='margin-left:auto; margin-right:auto;'>
			<a href="<?=$path?>/INFO/structure.ogv" class="jqueryImages" title="" id="VideoFormat">
			<img style="width: 30%; height: 30%;" src="images/video.png">
			</a>
			<a href="visualize.php" target="_blank">
			<img style="width: 40%; height: 40%;" src="images/jsmol.png">
			</a>
		</div>
	</div>

	<div>
		<table id="QC" style="border: 1px solid #ddd; background:#eee;">
			<tr>
				<th colspan="2">Quality Control</th>
			</tr>

			<?php
			QCplot("RMSd",$path,$data['RMSd_avg'],$data['RMSd_stdev']);
			QCplot("Rgyr",$path,$data['Rgyr_avg'],$data['Rgyr_stdev']);
			QCplot("SASA",$path,$data['SASA_avg'],$data['SASA_stdev']);
			QCplot("RMSf",$path,0,0);
			?>

		</table>
	</div>
	</div>

    </div>
</div>
<!--
	<div>
	<a href="<?=$path?>/INFO/structure.rmsd.png" class="preview jqueryImages"><img style="width: 30px;" src="<?=$path?>/INFO/structure.rmsd.png"></a>
	</div>
-->
<div class="metaImageSection">
    <div class="metaImage">
	<hr/>
		<h4 class="expandable"><a href="">Trajectory Analyses >> <i>(Click to expand/shrink)</i></a></h4>
	<hr/>

	<div class="metaImage toHide" id="toHide3" style="border: none;">
<?php

	$checkDuplex = 0;
	if (file_exists("$path/CURVES/seq.info")){
		$checkDuplex = checkDuplex("$path/CURVES/seq.info");
	}

	if (file_exists("$path/CURVES/helical_bp") && is_dir("$path/CURVES/helical_bp") && $checkDuplex) {
?>
	<div>
	        <a href="../NAFlex2/nucleicAcidAnalysis2.php?type=CURVES&amp;proj=<?=$data['_id']?>" target="_blank">
        	  <div class="imgLink"><img alt="" src="../NAFlex2/images/axis-bp.png" ></div>
	          <div class="titleLink">Curves Analysis</div>
        	</a>
	</div>
<?php
	}
	if (file_exists("$path/STIFFNESS/FORCE_CTES/rise_avg.dat.gnuplot")  && $checkDuplex) {
?>
	<div>
	        <a href="../NAFlex2/nucleicAcidAnalysis2.php?type=STIFFNESS&amp;proj=<?=$data['_id']?>" target="_blank">
        	  <div class="imgLink"><img alt="" src="../NAFlex2/images/helicalParamsBPS2.png"></div>
	          <div class="titleLink">Stiffness Analysis</div>
        	</a>
	</div>
<?php
	}
	if ( is_dir("$path/PCAZIP") && file_exists("$path/PCAZIP/pcazdump.info.log") && file_exists("$path/PCAZIP/${data['_id']}_pcazipOut.anim1.pdb")) {
?>
	<div>
        	<a href="../NAFlex2/nucleicAcidAnalysis2.php?type=PCAZIP&amp;proj=<?=$data['_id']?>" target="_blank">
	          <div class="imgLink"><img alt="" src="../NAFlex2/images/pcazip_nuc.gif"></div>
        	  <div class="titleLink">PCAzip Analysis</div>
	        </a>
	</div>
<?php
	}
	if (file_exists("$path/NMR_JC") && is_dir("$path/NMR_JC")) {
?>
	<div>
	        <a href="../NAFlex2/nucleicAcidAnalysis2.php?type=NMR_JC&amp;proj=<?=$data['_id']?>" target="_blank">
        	  <div class="imgLink"><img alt="" src="../NAFlex2/images/desoxyribose_J1p2pp_trans.png"></div>
	          <div class="titleLink">NMR_JC Analysis</div>
        	</a>
	</div>
<?php
	}
	if (file_exists("$path/NMR_NOE") && is_dir("$path/NMR_NOE")) {
?>
	<div>
	        <a href="../NAFlex2/nucleicAcidAnalysis2.php?type=NMR_NOE&amp;proj=<?=$data['_id']?>" target="_blank">
        	  <div class="imgLink"><img alt="" src="../NAFlex2/images/desoxyribose_J1p3p_trans.png"></div>
	          <div class="titleLink">NMR NOEs Analysis</div>
        	</a>
	</div>
<?php
	}
	if (file_exists("$path/HBs") && is_dir("$path/HBs") && $data['rev_sequence'] != '-' && (file_exists("$path/HBs/N1-H3.avg.dat.png") or file_exists("$path/HBs/O6-H41.avg.dat.png")) && $checkDuplex) {
?>
	<div>
	        <a href="../NAFlex2/nucleicAcidAnalysis2.php?type=HBs&amp;proj=<?=$data['_id']?>" target="_blank">
        	  <div class="imgLink"><img alt="" src="../NAFlex2/images/CG_AT_HB_trans.png"></div>
	          <div class="titleLink">HBs Analysis</div>
        	</a>	
	</div>
<?php
	}
	if (file_exists("$path/STACKING") && is_dir("$path/STACKING") && $data['rev_sequence'] != '-' && file_exists("$path/STACKING/energies/stackingContactMapsMIN.dat.png")) {

	  if($checkDuplex){
?>
	<div>
	        <a href="../NAFlex2/nucleicAcidAnalysis2.php?type=STACKING&amp;proj=<?=$data['_id']?>" target="_blank">
        	  <div class="imgLink"><img alt="" src="../NAFlex2/images/stacking2.png"></div>
	          <div class="titleLink">Stacking Analysis</div>
        	</a>
	</div>
<?php
	  }
	  else{
?>
	<div>
	        <a href="../NAFlex2/nucleicAcidAnalysis2.php?type=STACKING_2&amp;proj=<?=$data['_id']?>" target="_blank">
        	  <div class="imgLink"><img alt="" src="../NAFlex2/images/stacking2.png"></div>
	          <div class="titleLink">Stacking Analysis</div>
        	</a>
	</div>

<?php
	  }
	}
	if (file_exists("$path/CONTACTS") && is_dir("$path/CONTACTS") && $data['rev_sequence'] != '-') {
?>
	<div>
    	    <a href="../NAFlex2/nucleicAcidAnalysis2.php?type=CONTACTS&amp;proj=<?=$data['_id']?>" target="_blank">
        	  <div class="imgLink"><img alt="" src="../NAFlex2/images/distContactMap.png"></div>
	          <div class="titleLink">Contacts Analysis</div>
        	</a>
	</div>
<?php
	}
?>
<?php
	if (file_exists("$path/EXPvsMD") && is_dir("$path/EXPvsMD") and ( ! preg_match('/DDD_bsc1/',$data['_id']) ) ) {
?>
	<div>
    	    <a href="../NAFlex2/nucleicAcidAnalysis2.php?type=EXPvsMD&amp;proj=<?=$data['_id']?>" target="_blank">
        	  <div class="imgLink"><img alt="" src="images/MDvsExp.png"></div>
	          <div class="titleLink">Experimental vs MD Analysis</div>
        	</a>
	</div>
<?php
	}
?>
    </div>
    </div>
</div>

<div class="metaImageSection">
    <form id="metatraj" target="_blank" method="post" action="metatrajSGE.php" onsubmit="javascript: submitType();">
    <input type="hidden" name="idCode" value="<?=$data['_id']?>" />
    <input type="hidden" name="idSession" value="<?=$idSession?>" />
    <input type="hidden" name="type" value="download" />
      <div class="metaImage">
	<hr/>
		<h4 class="expandable"><a href="">Trajectory Selection >> <i>(Click to expand/shrink)</i></a></h4>
	<hr/>
	<div class="metaImage toHide" id="toHide4" style="border: none;">
	<div class="restSelections">
	<div class="inputSelection">
        	<strong>Selection:</strong>
		<input name='mask' id='mask' value='name *' size='30' />
		<br/>
		<strong><a href="http://pythonhosted.org/MDAnalysis/documentation_pages/selections.html" target="_blank">MDAnalysis Atom Selection Syntax</a></strong>
		<br/>
		<strong>e.g. resid 2:11 or resid 13:20</strong>
		<!--&nbsp;&nbsp;&nbsp;&nbsp;-->
	</div>
	<div class="inputSelection">
        	<strong>Frame</strong> (start:stop:step)
        	<input name='frames' id='frames' value='1:20:1' size='30' />
	</div>
	</div>
	<div class="trajSelection">
                <strong>Download</strong><img class="metaLogo" alt="" src="../NAFlex2/images/Download.png"><br/><br/>
                <strong>Format: &nbsp; &nbsp; &nbsp; &nbsp;</strong>
                <select name="format" id="formatSel">
                  <!--<option selected="selected" value="pcz">PCZ-Compressed</option>-->
                  <option selected="selected" value="mdcrd">ASCII CRD</option>
                  <option value="dcd">DCD binary</option>
                  <option value="netcdf">NetCDF binary</option>
                  <option value="xtc">Gromacs XTC binary</option>
                  <option value="trr">Gromacs TRR binary</option>
                  <option value="pdb">PDB (Models)</option>
                  <option value="gro">Gromacs GRO</option>
                </select>
                <!--<a href="metatraj.php?idCode=<?=$data['_id']?>">-->
                <br/><br/>
                <a class="submitMetatraj" href="javascript: submitform('download');">
                <img id="metaDownload" alt="" src="../NAFlex2/images/Download2.png">
                </a>
	</div>
	<div class="trajSelection">
                <strong>Analysis</strong><img class="metaLogo" alt="" src="../NAFlex2/images/Magnifier.png"><br/><br/>
                <strong>Analyse the selected trajectory using NAFlex server. &nbsp; &nbsp; &nbsp; &nbsp;</strong>
                <br/><br/>
                <a class="submitMetatraj" href="javascript: submitform('naflex');">
                <img id="metaNAFlex" alt="" src="../NAFlex2/images/NA_Flex_Logo.png">
                </a>
	</div>
      </div>
      </div>
    </form>
</div>

</div>


<?php
require "../master/footer.php";
//print footerMMB();
?>
