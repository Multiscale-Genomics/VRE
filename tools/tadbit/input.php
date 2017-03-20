<?php

require "../../phplib/genlibraries.php";
redirectOutside();

// check inputs
if (!isset($_REQUEST['fn']) && !isset($_REQUEST['rerunDir'])){
	$_SESSION['errorData']['Error'][]="Please, before running PyDock, select two files of format PDB for running this tool.";
	redirect('/workspace/');
}

$rerunParams  = Array();
$inPaths = Array();

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
		array_push($inPaths,$file);
	}
	//array_push($inPaths,getAttr_fromGSFileId($fn,'path'));
}

if((count($_REQUEST['fn']) != 3) && (count($_REQUEST['fn']) != 4)){
	$_SESSION['errorData']['Error'][] = "Please, select two FASTQ files, a FASTA file and, optionally, a BED or a BEDGRAPH file for running this tool";
	redirect('/workspace/');
}


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
                                  <span>TADbit</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> TADbit </h1>
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

			 <form action="#" class="horizontal-form" id="tadbit-form">
				  <input type="hidden" name="tool" value="tadbit" />
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
                                                      <select  name="input_files[mapping:read1]" class="form-control params_tadbit_inputs_mapping">
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
                                                      <select  name="input_files[mapping:read2]" class="form-control params_tadbit_inputs_mapping">
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
                                          <h4 class="form-section">Settings</h4>
                                          <div class="row">
																							<div class="col-md-6">
												  												<div class="form-group">
                                                      <label class="control-label">Restriction Enzyme <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>For fragment-based mapping. Name of the restriction enzyme used to do the Hi-C experiment.</p>"></i></label>
																											<input type="text" name="arguments[mapping:rest_enzyme]" id="rest_enzyme" class="form-control tadbit-map-group" >
																											<img class="Typeahead-spinner" src="assets/layouts/layout/img/loading-spinner-blue.gif" style="display: none;">	
                                                  </div>
                                              </div>
                                              <!--<div class="col-md-6">
												  												<div class="form-group">
                                                      <label class="control-label">Iterative mapping <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Use iterative mapping instead of fragment-based mapping.</p>"></i></label>
                                                      <select class="form-control valid" name="arguments[mapping:iterative_mapping]" id="iterative_mapping" aria-invalid="false">
                                                          <option value="1" selected="">1</option>
                                                          <option value="2">2</option>
                                                      </select>
                                                  </div>
                                              </div>-->
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Windows <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>For iterative mapping, or to refine fragment-based mapping. Should be like &quot;1:20 1:25 1:30 1:35 1:40 1:45 1:50&quot;</p>"></i></label>
																											<input type="text" name="arguments[mapping:windows]" id="windows" class="form-control tadbit-map-group" >
                                                  </div>
																							</div>
                                          </div>
																					<!--<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Windows <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>For iterative mapping, or to refine fragment-based mapping. Should be like &quot;1:20 1:25 1:30 1:35 1:40 1:45 1:50&quot;</p>"></i></label>
																											<input type="text" name="arguments[mapping:windows]" id="windows" class="form-control tadbit-map-group" >
                                                  </div>
																							</div>
																							<div class="col-md-6">
																							</div>
                                          </div>-->
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
																					<div class="row">
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">Reference genome <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Reference genome</p>"></i></label>
                                                      <select  name="input_files[parsing:ref_genome]" id="ref_genome" class="form-control">
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'FASTA') { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
                                                  </div>
                                              </div>
                                              <div class="col-md-6">
																							</div>
																						</div>
                                          <h4 class="form-section">Settings</h4>
                                          <div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Filter chromosomes <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>REGEXP to consider only chromosome names passing.</p>"></i></label>
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
                                                      <select class="form-control valid select2_tad1" name="arguments[filtering:filters]" id="filters" aria-invalid="false" multiple="multiple">
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
																									<div class="form-group">
                                                      <label class="control-label">Maximum molecule length <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Depends on the maximum size of the sequenced fragments (usually ~400 nt). Can be set to ~1 times this maximum size.</p>"></i></label>
																											<input type="number" name="arguments[filtering:max_mol_length]" id="max_mol_length" class="form-control" value="500">
                                                  </div>
																							</div>
																						</div>
                                          <div class="row">
                                              <div class="col-md-6" id="fg_min_dist_RE">
																									<div class="form-group">
                                                      <label class="control-label">Minimum distance to RE site <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Depends on the maximum size of the sequenced fragments (usually ~400 nt). Can be set to ~1.5 times this maximum size.</p>"></i></label>
																											<input type="number" name="arguments[filtering:min_dist_RE]" id="min_dist_RE" class="form-control" value="750">
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group display-hide" id="fg_min_dist_RE2">
                                                      <label class="control-label">Minimum distance to RE site <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Used to exclude reads starting too close from a RE site. Such reads are called pseudo-dangling-ends.</p>"></i></label>
																											<input type="number" name="arguments[filtering:min_dist_RE2]" id="min_dist_RE2" class="form-control" value="5" disabled>
                                                  </div>
																							</div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
															<!-- END PORTLET 4: Filtering of artifactual reads -->
															<!-- BEGIN PORTLET 5: Normalization by ICE -->
                              <div class="portlet box blue" id="form-block-header4">
                                  <div class="portlet-title">
                                      <div class="caption">
																				 Normalization by ICE
		                                  </div>
                                  </div>
                                  <div class="portlet-body form" id="form-block4">
                                      <div class="form-body">
                                        <h4 class="form-section">Settings</h4>
                                          <div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Resolution <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Resolution of the normalization (should input a number between 10000 (10 kb) and 10000000 (10 Mb)).</p>"></i></label>
																											<input type="number" min="10000" max="10000000" name="arguments[normalization:resolution]" id="resolution" class="form-control" >
                                                  </div>
																							</div>
																							<div class="col-md-6">
																								<div class="form-group">
                                                <label class="control-label">Bin filtering <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Filter bins with low number of mapped reads according to a maximum
   	percentage of zeroes in them, a minimum sum of contacts, or apply a strong
   	filter by fitting a logistic function to the distribution of interactions
   	per bin and cutting out, lower outliers.</p>"></i></label>
																									<div class="input-group">
																										<input type="number" class="form-control field_dependency2 field_dependency2_1" min="0" max="100" name="arguments[normalization:perc_zeros]" id="perc_zeros" value="95.0">
																										<input type="number" class="form-control field_dependency2 field_dependency2_2" style="display:none;" name="arguments[normalization:min_num_cont]" id="min_num_cont" value="2500">
																										<input type="number" class="form-control field_dependency2 field_dependency2_3" style="display:none;" name="arguments[normalization:strong_filter]" id="strong_filter" disabled>
																										<div class="input-group-btn">
																												<button type="button" class="btn green dropdown-toggle" data-toggle="dropdown"><span id="arg_dependency2">Percentage of zeros</span>
																														<i class="fa fa-angle-down"></i>
																												</button>
																												<ul class="dropdown-menu pull-right">
																														<li>
																																<a id="arg_dependency2_1" href="javascript:changeArgDependency('2', '1');"> Percentage of zeros </a>
																														</li>
																														<li>
																																<a id="arg_dependency2_2" href="javascript:changeArgDependency('2', '2');"> Minimum number of contacts </a>
																														</li>
																														<li>
																																<a id="arg_dependency2_3" href="javascript:changeArgDependency('2', '3');"> Strong filter </a>
																														</li>
																												</ul>
																										</div>
																									</div>
																								</div>
																							</div>
                                          </div>
																					<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Keep matrices. Intra-chromosomal matrices <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Matrices, or sub-matrices to save. You can also input a list of chromosome names (e.g.: chr1 chr2 chrX).</p>"></i></label>
																											<input type="text" name="arguments[normalization:intra-chr-matr]" id="intra-chr-matr" class="form-control" placeholder="Chromosome names" >
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Keep matrices. Inter-chromosomal matrices <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Matrices, or sub-matrices to save. You can also input a list of chromosome names (e.g.: chr1 chr2 chrX).</p>"></i></label>
																											<input type="text" name="arguments[normalization:inter-chr-matr]" id="inter-chr-matr" class="form-control" placeholder="Chromosome names" >
                                                  </div>
																							</div>
                                          </div>
																					<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group display-hide">
                                                      <label class="control-label">Keep matrices. Genomic matrices <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Matrices, or sub-matrices to save. You can also input a list of chromosome names (e.g.: chr1 chr2 chrX).</p>"></i></label>
																											<input type="text" name="arguments[normalization:gen-matr]" id="gen-matr" class="form-control" placeholder="Chromosome names" disabled>
                                                  </div>
																							</div>
																							<div class="col-md-6">
																							</div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 5: Normalization by ICE -->
															<!-- BEGIN PORTLET 6: Segmentation -->
                              <div class="portlet box blue form-block-header" id="form-block-header5">
                                  <div class="portlet-title">
																			<div class="caption">
																				<input type="checkbox" class="make-switch switch-block" name="arguments[segmentation]" id="switch-block5" data-size="mini">
                                        <div style="float:right;margin-left:20px;">Segmentation </div>
																			</div>
																			<div class="tools">
                                          <a href="javascript:;" class="collapse"></a>
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block5">
                                      <div class="form-body">
                                          <h4 class="form-section">File inputs</h4>
																					<div class="row">
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">Rich in A compartments <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>BED or bedGraph file with list of protein coding gene or other active/inactive epigenetic mark, to be used to label compartments.</p>"></i></label>
                                                      <select  name="input_files[segmentation:rich_a]" id="rich_a" class="form-control form-field-enabled params_tadbit_inputs_segmentation">
																												<option selected value> -- select a file -- </option>
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if(($file['format'] == 'BED') || ($file['format'] == 'BEDGRAPH')) { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
                                                  </div>
                                              </div>
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Rich in B compartments <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>BED or bedGraph file with list of protein coding gene or other active/inactive epigenetic mark, to be used to label compartments.</p>"></i></label>
                                                      <select  name="input_files[segmentation:rich_b]" id="rich_b" class="form-control form-field-enabled params_tadbit_inputs_segmentation">
																												<option selected value> -- select a file -- </option>
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if(($file['format'] == 'BED') || ($file['format'] == 'BEDGRAPH')) { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
                                                  </div>
																							</div>
																						</div>
                                          <h4 class="form-section">Settings</h4>
                                          <div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Callers <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0;'>Search for TAD borders using TADbit's algorithm, and compartments using first EigenVector of the correlation matrix.</p>"></i></label>
                                                      <select class="form-control form-field-enabled valid select2_tad2" name="arguments[segmentation:callers]" id="callers" aria-invalid="false" multiple="multiple">
                                                          <option value=""></option>
                                                          <option value="1" selected>call TAD borders</option>
                                                          <option value="2" selected>call compartments</option>
																											</select>
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Chromosomes names <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>List of chromosome names where to search for TAD borders or compartments (e.g.: chr1 chr2 chrX)</p>"></i></label>
																											<input type="text" name="arguments[segmentation:chromosome_names]" id="chromosome_names" class="form-control form-field-enabled" >
                                                  </div>
																							</div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 6: Segmentation -->
															<!-- BEGIN PORTLET 7: 3D modeling - parameter optimization -->
                              <div class="portlet box blue" id="form-block-header6">
                                  <div class="portlet-title">
                                      <div class="caption">
                                         3D modeling - parameter optimization
		                                  </div>
                                  </div>
                                  <div class="portlet-body form" id="form-block6">
                                      <div class="form-body">
                                          <h4 class="form-section">Settings</h4>
                                          <div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Genomic position. Chromosome name. <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Coordinates of the genomic region to model.</p>"></i></label>
																											<input type="text" name="arguments[optimization:gen_pos_chrom_name]" id="gen_pos_chrom_name" class="form-control" >
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Genomic position. Begin. <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Coordinates of the genomic region to model.</p>"></i></label>
																											<input type="number" name="arguments[optimization:gen_pos_begin]" id="gen_pos_begin" class="form-control" >
                                                  </div>
																							</div>
                                          </div>
																					<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Genomic position. End. <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Coordinates of the genomic region to model.</p>"></i></label>
																											<input type="number" name="arguments[optimization:gen_pos_end]" id="gen_pos_end" class="form-control" >
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Number of models to compute <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Number of models to compute for each optimization step.</p>"></i></label>
																											<input type="number" name="arguments[optimization:num_mod_comp]" id="num_mod_comp" class="form-control" value="50">
                                                  </div>
																							</div>
                                          </div>
																					<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Number of models to keep <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Number of models to keep from the total of models computed, for the comparison with original Hi-C matrix.</p>"></i></label>
																											<input type="number" name="arguments[optimization:num_mod_keep]" id="num_mod_keep" class="form-control" value="20">
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Maximum distance <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Range of numbers for optimal maxdist parameter, i.e. 400:1000:100; or just a single number e.g. 800; or a list of numbers e.g. 400 600 800 1000.</p>"></i></label>
																											<input type="text" name="arguments[optimization:max_dist]" id="max_dist" class="form-control" value="400:1000:200">
                                                  </div>
																							</div>
                                          </div>
																					<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Upper bound for Z-scored frequencies of interaction <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Range of numbers for optimal upfreq parameter, i.e. 0:1.2:0.3; or just a single number e.g. 0.8; or a list of numbers e.g. 0.1 0.3 0.5 0.9.</p>"></i></label>
																											<input type="text" name="arguments[optimization:upper_bound]" id="upper_bound" class="form-control" value="0:1.2:0.3" >
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Lower bound for Z-scored frequencies of interaction <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Range of numbers for optimal low parameter, i.e. -1.2:0:0.3; or just a single number e.g. -0.8; or a list of numbers e.g. -0.1 -0.3 -0.5 -0.9.</p>"></i></label>
																											<input type="text" name="arguments[optimization:lower_bound]" id="lower_bound" class="form-control" value="-1.2:0:0.3">
                                                  </div>
																							</div>
                                          </div>
																					<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Cutoff distance to consider an interaction between 2 particles <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Range of numbers for optimal cutoff distance. Cutoff is computed based on the resolution. This cutoff distance is calculated taking as reference the diameter of a modeled particle in the 3D model. i.e. 1.5:2.5:0.5; or just a single number e.g. 2; or a list of numbers e.g. 2 2.5.</p>"></i></label>
																											<input type="text" name="arguments[optimization:cutoff]" id="cutoff" class="form-control" value="2" >
                                                  </div>
																							</div>
																							<div class="col-md-6">
																							</div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 7: 3D modeling - parameter optimization -->
															<!-- BEGIN PORTLET 8: 3D modeling - generation of models -->
                              <div class="portlet box blue" id="form-block-header7">
                                  <div class="portlet-title">
                                      <div class="caption">
                                         3D modeling - generation of models
		                                  </div>
                                  </div>
                                  <div class="portlet-body form" id="form-block7">
                                      <div class="form-body">
                                          <h4 class="form-section">Settings</h4>
                                          <div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Number of models to compute <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Number of models to compute for each optimization step.</p>"></i></label>
																											<input type="number" name="arguments[generation:num_models_comp]" id="num_models_comp" class="form-control" value="500">
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Number of models to keep <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Number of models to keep from the total of models computed, for the comparison with original Hi-C matrix.</p>"></i></label>
																											<input type="number" name="arguments[generation:num_models_keep]" id="num_models_keep" class="form-control" value="500">
                                                  </div>
																							</div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 8: 3D modeling - generation of models -->



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
