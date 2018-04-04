<?php

require "../../phplib/genlibraries.php";
redirectOutside();

// check inputs
if (!isset($_REQUEST['fn']) && !isset($_REQUEST['rerunDir'])){
	$_SESSION['errorData']['Error'][]="Please, before running TADit, please select the input files for running this tool.";
	redirect('/workspace/');
}

if(count($_REQUEST['fn']) < 2) {
	$_SESSION['errorData']['Error'][] = "Please, select at least two FASTQ files.";
	redirect('/workspace/');
}


$rerunParams  = Array();
$inPaths = Array();
$formats = Array();

if ($_REQUEST['rerunDir']){
	$dirMeta = $GLOBALS['filesMetaCol']->findOne(array('_id' => $_REQUEST['rerunDir'])); 
	if (!is_array($dirMeta['inPaths']) && !isset($dirMeta['raw_params'])){
		$_SESSION['errorData']['Error'][]="Cannot rerun job ".$_REQUEST['rerunDir'].". Some folder metadata is missing.";
		redirect('/workspace/');
	}
	if (is_array($dirMeta['inPaths'][0])){
		$_SESSION['errorData']['Internal'][]="Cannot rerun job ".$_REQUEST['rerunDir'].". New directory metadata not implemeted yet.";
		redirect('/workspace/');
	}
	foreach ($dirMeta['inPaths'] as $inPath){
		$file['path'] = $inPath;
		$file['fn'] = getGSFileId_fromPath($inPath);
		$file['format'] = getAttr_fromGSFileId($file['fn'],'format');
		array_push($formats,$file['format']);
		array_push($inPaths,$file);
	}
	$rerunParams = $dirMeta['raw_params'];
	//$inPaths=$dirMeta['inPaths']
	//$_REQUEST['fn']= array_map("getGSFileId_fromPath",$dirMeta['inPaths']);
}else{
	if (!is_array($_REQUEST['fn']))
		$_REQUEST['fn'][]=$_REQUEST['fn'];

	foreach($_REQUEST['fn'] as $fn){
		$file['path'] = getAttr_fromGSFileId($fn,'path');
		$file['fn'] = $fn;
		$file['format'] = getAttr_fromGSFileId($fn,'format');
		array_push($formats,$file['format']);
		array_push($inPaths,$file);
	}
	//array_push($inPaths,getAttr_fromGSFileId($fn,'path'));
}

//var_dump($formats);

$count_val = array_count_values($formats);

if(!(in_array("FASTQ", $formats))) {
	$_SESSION['errorData']['Error'][] = "Please, select at least two FASTQ files.";
	redirect('/workspace/');
} elseif($count_val['FASTQ'] != 2) {
	$_SESSION['errorData']['Error'][] = "Please, select at least two FASTQ files.";
	redirect('/workspace/');
}

if(in_array("FASTA", $formats) && !in_array("GEM", $formats)) {
	$_SESSION['errorData']['Error'][] = "If you provide a FASTA file, you must provide a GEM file too.";
	redirect('/workspace/');
}

$formats = array_unique($formats);

// default project dir
$dirNum="000";
$reObj = new MongoRegex("/^".$_SESSION['User']['id']."\\/run\d\d\d$/i");
$prevs  = $GLOBALS['filesCol']->find(array('path' => $reObj, 'owner' => $_SESSION['User']['id']));
if ($prevs->count() > 0){
        $prevs->sort(array('_id' => -1));
        $prevs->next();
        $previous = $prevs->current();
        if (preg_match('/(\d+)$/',$previous["path"],$m) ){
            $dirNum= sprintf("%03d",$m[1]+1);
        }
}
$dirName="run".$dirNum;
$prevs  = $GLOBALS['filesCol']->find(array('path' => $GLOBALS['dataDir']."/".$_SESSION['User']['dataDir']."/$dirName", 'owner' => $_SESSION['User']['id']));
if ($prevs->count() > 0){
    $dirName="run".rand(100, 999);
}

// get tool details
$toolId = "tadbit_map_parse_filter";
$tool   = getTool_fromId($toolId,1);

?>

<?php require "../../htmlib/header.inc.php"; ?>
 
<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid page-sidebar-fixed">
  <div class="page-wrapper">

  <?php require "../../htmlib/top.inc.php"; ?>
  <?php require "../../htmlib/menu.inc.php"; ?>

	<!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                              <li>
                                  <a href="home/">Home</a>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <a href="workspace/">User Workspace</a>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>Tools</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span><?=$tool['name']?></span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"><?=$tool['title']?></h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
                        <div class="row">
				<div class="col-md-12">
				<?php if(isset($_SESSION['errorData'])) { ?>
					<div class="alert alert-warning">
					<?php foreach($_SESSION['errorData'] as $subTitle=>$txts){
						print "$subTitle<br/>";
						foreach($txts as $txt){
							print "<div style=\"margin-left:20px;\">$txt</div>";
						}
					}
					unset($_SESSION['errorData']);
					?>
					</div>
				<?php } ?>
															<!-- BEGIN PORTLET 0: INPUTS -->
                              <div class="portlet box blue-oleo">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <div style="float:left;margin-right:20px;"> <i class="fa fa-sign-in" ></i> Inputs</div>
                                      </div>
                                  </div>
                                  <div class="portlet-body">
																		<ul class="feeds" id="list-files-run-tools">
																		<?php foreach ($inPaths as $file) {
																			$path= $file['path'];
																			$p = explode("/", $path); 
																			?>
																			<li class="tool-122 tool-list-item">
																			<div class="col1">
																				<div class="cont">
																					<div class="cont-col1">
																						<div class="label label-sm label-info">
																							<i class="fa fa-file"></i>
																						</div>
																					</div>
																					<div class="cont-col2">
																					<div class="desc">
																					<span class="text-info" style="font-weight:bold;"><?php echo $p[1]; ?>  /</span> <?php echo $p[2]; ?> 
																						<!--
																						<?php if($file['format'] == 'PDB') { ?>
																
																							<a href="javascript:openNGL('<?php echo $file['fn']; ?>', '<?php echo $p[2]; ?> ');" style="margin-left:5px;">
																								<div class="label label-sm label-info tooltips" style="padding:4px 5px;" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Click here to preview this file with NGL.</p>">
																									<i class="fa fa-window-maximize font-white"></i>
																								</div>
																							</a>
																						
																						<?php } ?>								
																						-->
																					</div>
																					</div>
																				</div>
																			</div>
																			</li>
																		<?php } ?>
                                </ul>
                                  </div>
                              </div>
                              <!-- END PORTLET 0: INPUTS -->

			 <form action="#" class="horizontal-form" id="tadbit_map_parse_filter-form">
				  <input type="hidden" name="tool" value="<?=$toolId;?>" />
					<input type="hidden" id="base-url"     value="<?php echo $GLOBALS['BASEURL']; ?>"/>

				 
                              <!-- BEGIN PORTLET 1: ANALYZES -->
                              <div class="portlet box blue-oleo">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <div style="float:left;margin-right:20px;"> <i class="fa fa-check-square-o" ></i> Project</div>
                                      </div>
                                  </div>
                                  <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">Name</label>
                                                    <input type="text" name="project" id="dirName" class="form-control" value="<?php echo $dirName;?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">Description</label>
                                                    <textarea id="description" name="description" class="form-control" style="height:120px;" placeholder="Write a short description here..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 1: ANALYZES -->
                              <!-- BEGIN PORTLET 2: Hi-C Mapping -->
                              <div class="portlet box blue " id="form-block-header1">
                                  <div class="portlet-title">
                                      <div class="caption">
                                         Hi-C Mapping
                                      </div>
                                  </div>
                                  <div class="portlet-body form " id="form-block1">
                                      <div class="form-body">
                                          <h4 class="form-section">File inputs</h4>
																					<div class="row">
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">READ1 <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>From paired-end sequencing. This FASTQ should contain only one of the ends.</p>"></i></label>
                                                      <select  name="input_files[read1]" class="form-control params_tadbit_inputs_mapping">
																												<option selected value> -- select a file -- </option>
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'FASTQ') { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
                                                  </div>
                                              </div>

                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">READ2 <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>From paired-end sequencing. This FASTQ should contain only the other end.</p>"></i></label>
                                                      <select  name="input_files[read2]" class="form-control params_tadbit_inputs_mapping">
																												<option selected value> -- select a file -- </option>
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'FASTQ') { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
                                                  </div>
                                              </div>
																							
																						</div>

																						<div class="row">
                                              <div class="col-md-6">
																								<div class="form-group " id="">
																										<?php if(!(in_array("GEM", $formats))) {  ?>
																										<label>Indexed reference genome</label>
																										<span class="tooltip-mt-radio"><i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Reference genome, indexed with gem-indexer, on which reads will be mapped.</p>"></i></span>
																										<select name="input_files_public_dir[mapping:refGenome]" id="map_refgenome" class="form-control">
																											<option value="">Select the reference genome</option>
				<?php
				$tool_options = $tool['input_files_public_dir']['mapping:refGenome']['enum_items'];
				for ($i=0; $i<count($tool_options['name']); $i++){ ?>
					<option value="<?=$tool_options['name'][$i]?>"><?=$tool_options['description'][$i]?></option>
			        <?php } ?>
			</select>
																										<?php } else { ?>
                                                      <label class="control-label">Reference genome index <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Reference genome</p>"></i></label>
                                                      <select  name="input_files[mapping:ref_genome_gem]" id="ref_genome_gem1" class="form-control">
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'GEM') { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
																										<?php } ?>
																								</div>
																						</div>
																					</div>

                                          <h4 class="form-section">Settings</h4>
                                          <div class="row">
																							<div class="col-md-6">
												  												<div class="form-group">
                                                      <label class="control-label">Restriction Enzyme <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>For fragment-based mapping. Name of the restriction enzyme used to do the Hi-C experiment.</p>"></i></label>
																											<input type="text" name="arguments[mapping:rest_enzyme]" id="rest_enzyme" class="form-control tadbit-map-group" >
																											<img class="Typeahead-spinner" src="assets/layouts/layout/img/loading-spinner-blue.gif" style="display: none;">	
                                                  </div>
                                              </div>
                                          </div>
																					<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                    <label class="control-label">Iterative mapping <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Use iterative mapping instead of fragment-based mapping.</p>"></i></label>
																										<select  name="arguments[mapping:iterative_mapping]" id="iterative_mapping" class="form-control">
																												<option selected value="0"> False </option>
																												<option value="1"> True </option>
																											</select>		
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Windows <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>For iterative mapping, or to refine fragment-based mapping. Should be like &quot;1:20 1:25 1:30 1:35 1:40 1:45 1:50&quot;</p>"></i></label>
																											<input type="text" name="arguments[mapping:windows]" id="windows" class="form-control tadbit-map-group" >
                                                  </div>
																							</div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 2: Hi-C Mapping -->
															<!-- BEGIN PORTLET 3: Parsing mapped reads -->
                              <div class="portlet box blue" id="form-block-header2">
                                  <div class="portlet-title">
                                      <div class="caption">
                                         Parsing mapped reads
		                                  </div>
                                  </div>
                                  <div class="portlet-body form" id="form-block2">
                                      <div class="form-body">
																					<h4 class="form-section">File inputs</h4>
																					<?php if((in_array("FASTA", $formats) && in_array("GEM", $formats))) { ?>
																					<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Reference genome sequence <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Reference genome</p>"></i></label>
                                                      <select  name="input_files[parsing:ref_genome_fasta]" id="ref_genome_fasta" class="form-control">
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'FASTA') { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
                                                  </div>
                                              </div>
																							<!--<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Reference genome index <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Reference genome</p>"></i></label>
                                                      <select  name="input_files[parsing:ref_genome_gem]" id="ref_genome_gem2" class="form-control">
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'GEM') { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
                                                  </div>
																							</div>-->
																						</div>
																					<?php } else { ?>
																					<div class="row">
                                              <div class="col-md-6">
																								<div class="form-group " id="">
																									<label>Indexed reference genome <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Reference genome, indexed with gem-indexer, on which reads will be mapped.</p>"></i></label>
																									<select name="input_files_public_dir[parsing:refGenome]" id="ref_genome" class="form-control">
			<option value="">Select the reference genome</option>
		       	<?php
			$tool_options = $tool['input_files_public_dir']['parsing:refGenome']['enum_items'];
			for ($i=0; $i<count($tool_options['name']); $i++){ ?>
				<option value="<?=$tool_options['name'][$i]?>"><?=$tool_options['description'][$i]?></option>
			<?php } ?>
																									</select>
																								</div>
																							</div>
																						</div>
																					<?php } ?>
                                          <h4 class="form-section">Settings</h4>
                                          <div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Filter chromosomes <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Only chromosome names passing. Ex: chrX, 1, 2B, chrMito, Mito, chrXIV.</p>"></i></label>
																											<input type="text" name="arguments[parsing:chromosomes]" id="chromosomes" class="form-control" >
                                                  </div>
																							</div>
																							<div class="col-md-6">
																							</div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 3: Parsing mapped reads -->
															<!-- BEGIN PORTLET 4: Filtering of artifactual reads -->
                              <div class="portlet box blue" id="form-block-header3">
                                  <div class="portlet-title">
                                      <div class="caption">
                                         Filtering of artifactual reads
		                                  </div>
                                  </div>
                                  <div class="portlet-body form" id="form-block3">
                                      <div class="form-body">
                                          <h4 class="form-section">Settings</h4>
																					<div class="row">
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">Which filters to apply <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0;'>In order to remove interactions between reads that are experimental artifacts, or just uninformative, a series of adjustable filters can be applied:<br>
            	<ol style='text-align:left;'><li> self-circle: reads are coming from a single RE fragment and point to the outside (—-<===—===>—)</li>
            	<li> dangling-end: reads are coming from a single RE fragment and point to the inside (—-===>—<===—)</li>
            	<li> error: reads are coming from a single RE fragment and point in the same direction</li>
            	<li> extra dangling-end: reads are coming from different RE fragment but are close enough (< Maximum molecule length) and point to the inside. Maximum molecule length parameter depends on the size of the sequenced fragments.</li>
            	<li> too close from REs: semi-dangling-end filter, start position of one of the read is too close (5 bp by default) from RE cutting site (with 4 base-pair-cutter enzyme it can be set to 4 nt). This filter is in general not taken into account in in-situ Hi-C experiments, and with 4bp cutters as the ligation may happen only one side of the DNA fragments.</li>
            	<li> too short: remove reads coming from small restriction less than 100 bp (default) because they are comparable to the read length, and are thus probably artifacts.</li>
            	<li> too large: remove reads coming from large restriction fragments (default: 100 Kb, P < 10-5 to occur in a randomized genome) as they likely represent poorly assembled or repeated regions</li>
            	<li> over-represented: reads coming from the top 0.5% most frequently detected restriction fragments, they may be prone to PCR artifacts or represent fragile regions of the genome or genome assembly errors</li>
            	<li> duplicated: the combination of the start positions (and direction) of the reads is repeated -> PCR artifact (only keep one copy)</li>
            	<li> random breaks: start position of one of the read is too far (more than Minimum distance to RE site) from RE cutting site. Non-canonical enzyme activity or random physical breakage of the chromatin.</li></ol></p>"></i></label>
                                                      <select class="form-control valid select2_tad1" name="arguments[filtering:filters][]" id="filters" aria-invalid="false" multiple="multiple">
                                                          <option value=""></option>
                                                          <option value="1" selected>self-circle</option>
                                                          <option value="2" selected>dangling-end</option>
																													<option value="3" selected>error</option>
																													<option value="4" selected>extra dangling-end</option>
                                                          <option value="5">too close from RES</option>
																													<option value="6">too short</option>
																													<option value="7">too large</option>
																													<option value="8">over-represented</option>
                                                          <option value="9" selected>duplicated</option>
																													<option value="10" selected>random breaks</option>
                                                      </select>
                                                  </div>
                                              </div>
																							<div class="col-md-6">
																									<div class="form-group display-hide" id="fg_min_dist_RE">
                                                      <label class="control-label">Minimum distance to RE site <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Used to exclude reads starting too close from a RE site. Such reads are called pseudo-dangling-ends.</p>"></i></label>
																											<input type="number" name="arguments[filtering:min_dist_RE]" id="min_dist_RE" class="form-control" value="5" min="0" disabled>
                                                  </div>
																							</div>
																						</div>
                                          <div class="row">
																							<div class="col-md-6">
																									<div class="form-group display-hide" id="fg_min_fragment_size">
                                                      <label class="control-label">Minimum fragment size <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>To exclude small genomic RE fragments (smaller than sequenced reads)</p>"></i></label>
																											<input type="number" name="arguments[filtering:min_fragment_size]" id="min_fragment_size" class="form-control" value="50" min="0" disabled>
                                                  </div>
																							</div>
                                              <div class="col-md-6">
																									<div class="form-group display-hide" id="fg_max_fragment_size">
                                                      <label class="control-label">Maximum fragment size <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>to exclude large genomic RE fragments (probably resulting from gaps in the reference genome).</p>"></i></label>
																											<input type="number" name="arguments[filtering:max_fragment_size]" id="max_fragment_size" class="form-control" value="100000" min="0" disabled>
                                                  </div>
																							</div>
																					</div>
                                      </div>
                                  </div>
                              </div>
															<!-- END PORTLET 4: Filtering of artifactual reads -->



                              <div class="alert alert-danger err-nd display-hide">
                                  <strong>Error!</strong> You forgot to fill out some mandatory fields, please check them before submit the form.
                              </div>

                              <div class="alert alert-warning warn-nd display-hide">
                                  <strong>Warning!</strong> At least one analysis should be selected.
                              </div>

                              <div class="form-actions">
                                  <button type="submit" class="btn blue" style="float:right;">
                                      <i class="fa fa-check"></i> Compute</button>
                              </div>
                              </form>
                            </div>
                        </div>
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->
    
									<div class="modal fade bs-modal" id="modalNGL" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title"></h4>
                            </div>
                            <div class="modal-body">
                              <div id="viewport" style="width:100%; height:500px;background:#ddd;"></div>
                             </div>
                            <div class="modal-footer">
                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
				</div>

 
<?php 

require "../../htmlib/footer.inc.php"; 
require "../../htmlib/js.inc.php";

?>
