<?php

require "../../phplib/genlibraries.php";
redirectOutside();

InputTool_checkRequest($_REQUEST);

$from = InputTool_getOrigin($_REQUEST);

list($rerunParams,$inPaths) = InputTool_getPathsAndRerun($_REQUEST);


$dirName = InputTool_getDefExName();

// get tool details
$toolId = "tadbit_map_parse_filter";
$tool   = getTool_fromId($toolId,1);

// TADBIT MAP, PARSE & FILTER TOOL OPERATIONS:
// op = 0 || count(fn) = 3 / 4 -> FASTQ* + FASTQ* + FASTA + GEM
// op = 1 || count(fn) = 2 -> FASTQ* + FASTQ*

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
                                  <span><?php echo $tool['name']; ?></span>
                              </li>
                            </ul>
												</div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"><?php echo $tool['title']; ?></h1>
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

															<?php if($from == "tool") { ?>			

															<div class="row">
																<div class="col-md-12">
																			
																	<div class="mt-element-step">
																		<div class="row step-line">
																				<div class="col-md-6 mt-step-col first active">
																						<div class="mt-step-number bg-white">1</div>
																						<div class="mt-step-title uppercase font-grey-cascade">Select tool</div>
																				</div>
																				<div class="col-md-6 mt-step-col last active">
																						<div class="mt-step-number bg-white">2</div>
																						<div class="mt-step-title uppercase font-grey-cascade">Configure tool</div>
																				</div>
																		</div>
																	</div>

																</div>
															</div>

															<?php } ?>

															<form action="#" class="horizontal-form" id="tool-input-form">
																	<input type="hidden" name="tool" value="<?php echo $toolId; ?>" />
																	<input type="hidden" id="base-url" value="<?php echo $GLOBALS['BASEURL']; ?>"/>

				 
                              <!-- BEGIN PORTLET 1: PROJECT -->
                              <div class="portlet box blue-oleo">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <div style="float:left;margin-right:20px;"> <i class="fa fa-check-square-o" ></i> Project</div>
                                      </div>
                                  </div>
                                  <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Select Project</label>
																										<?php InputTool_getSelectProjects(); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Execution Name</label>
                                                    <input type="text" name="execution" id="dirName" class="form-control" value="<?php echo $dirName;?>">
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
															<!-- END PORTLET 1: PROJECT -->

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
                                                  <!--<div class="form-group">
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
																									</div>-->
																									<?php $ff = matchFormat_File($tool['input_files']['read1']['file_type'], $inPaths); ?>
																									<?php InputTool_printSelectFile($tool['input_files']['read1'], $rerunParams['read1'], $ff[0], false, true); ?>
                                              </div>

                                              <div class="col-md-6">
																									<!--<div class="form-group">
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
																									</div>-->
																									<?php $ff = matchFormat_File($tool['input_files']['read2']['file_type'], $inPaths); ?>
																									<?php InputTool_printSelectFile($tool['input_files']['read2'], $rerunParams['read2'], $ff[1], false, true); ?>
                                              </div>
																							
																						</div>

																						<div class="row">
																							<div class="col-md-6">
																								<?php if(($_REQUEST["op"] == 0) || (count($_REQUEST['fn']) == 3 && !isset($_REQUEST["op"])) || (count($_REQUEST['fn']) == 4 && !isset($_REQUEST["op"]))) { ?>

																								<?php $ff = matchFormat_File($tool['input_files']['ref_genome_gem']['file_type'], $inPaths); ?> 
																								<?php  
																									if(empty($ff) && !isset($_REQUEST["op"])) {
																										InputTool_printListOfFiles($tool['input_files_public_dir']['mapping:refGenome'], $rerunParams['mapping:refGenome'], true);
																									} else {
																										InputTool_printSelectFile($tool['input_files']['ref_genome_gem'], $rerunParams['ref_genome_gem'], $ff[0], false, true);
																									}
																								?>

																								<?php } else { ?>

																								<?php InputTool_printListOfFiles($tool['input_files_public_dir']['mapping:refGenome'], $rerunParams['mapping:refGenome'], true); ?>

																								<?php } ?>
																								
																						</div>
																					</div>

                                          <h4 class="form-section">Settings</h4>
                                          <div class="row">
																							<div class="col-md-6">
												  												<div class="form-group">
																											<label class="control-label"><?php echo $tool["arguments"]["mapping:rest_enzyme"]["description"]; ?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool["arguments"]["mapping:rest_enzyme"]["help"]; ?></p>"></i></label>
																											<?php if(isset($rerunParams["mapping:rest_enzyme"])) { ?>
																												<input type="text" name="arguments[<?php echo $tool["arguments"]["mapping:rest_enzyme"]["name"]; ?>]" id="mapping_rest_enzyme" class="form-control" value="<?php echo $rerunParams["mapping:rest_enzyme"]; ?>">
																											<?php } else { ?>
																												<input type="text" name="arguments[<?php echo $tool["arguments"]["mapping:rest_enzyme"]["name"]; ?>]" id="mapping_rest_enzyme" class="form-control" >
																											<?php } ?>
																											<img class="Typeahead-spinner" src="assets/layouts/layout/img/loading-spinner-blue.gif" style="display: none;">
																												<input type="hidden" id="enum_mapping_rest_enzyme" value="<?php echo implode(",", $tool["arguments"]["mapping:rest_enzyme"]["enum_items"]["name"]); ?>">	
																									</div>
                                              </div>
																					</div>

																					<div class="row">
                                              <div class="col-md-6">
																									<!--<div class="form-group">
                                                    <label class="control-label">Iterative mapping <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Use iterative mapping instead of fragment-based mapping.</p>"></i></label>
																										<select  name="arguments[mapping:iterative_mapping]" id="iterative_mapping" class="form-control">
																												<option selected value="0"> False </option>
																												<option value="1"> True </option>
																											</select>		
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['mapping:iterative_mapping'], $rerunParams['mapping:iterative_mapping']); ?>
																							</div>
																							<div class="col-md-6">
																									<!--<div class="form-group">
                                                      <label class="control-label">Windows <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>For iterative mapping, or to refine fragment-based mapping. Should be like &quot;1:20 1:25 1:30 1:35 1:40 1:45 1:50&quot;</p>"></i></label>
																											<input type="text" name="arguments[mapping:windows]" id="windows" class="form-control tadbit-map-group" >
																									</div>-->
																									<?php echo InputTool_printField($tool['arguments']['mapping:windows'], $rerunParams['mapping:windows']); ?>
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
																					<div class="row">
                                              <div class="col-md-6">
	
																							<?php if(($_REQUEST["op"] == 0) || (count($_REQUEST['fn'] && !isset($_REQUEST["op"])) == 3) || (count($_REQUEST['fn']) == 4 && !isset($_REQUEST["op"]))) { ?>

																							<?php $ff = matchFormat_File($tool['input_files']['ref_genome']['file_type'], $inPaths); ?> 
																							<?php  
																									if(empty($ff) && !isset($_REQUEST["op"])) {
																										InputTool_printListOfFiles($tool['input_files_public_dir']['parsing:refGenome'], $rerunParams['parsing:refGenome'], true);
																									} else {
																										InputTool_printSelectFile($tool['input_files']['ref_genome'], $rerunParams['ref_genome'], $ff[0], false, true);
																									}
																								?>

																							<?php } else { ?>

																							<?php InputTool_printListOfFiles($tool['input_files_public_dir']['parsing:refGenome'], $rerunParams['parsing:refGenome'], true); ?>

																							<?php } ?>
																							</div>
																						</div>
	
																					
                                          <h4 class="form-section">Settings</h4>
                                          <div class="row">
                                              <div class="col-md-6">
																									<!--<div class="form-group">
                                                      <label class="control-label">Filter chromosomes <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Only chromosome names passing. Ex: chrX, 1, 2B, chrMito, Mito, chrXIV.</p>"></i></label>
																											<input type="text" name="arguments[parsing:chromosomes]" id="chromosomes" class="form-control" >
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['parsing:chromosomes'], $rerunParams['parsing:chromosomes']); ?>
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
                                                  <!--<div class="form-group">
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
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['filtering:filters'], $rerunParams['filtering:filters']); ?>
                                              </div>
																							<div class="col-md-6">
																									<div class="form-group display-hide" id="fg_min_dist_RE">
                                                      <label class="control-label"><?php echo $tool['arguments']['filtering:min_dist_RE']['description']; ?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['filtering:min_dist_RE']['help']; ?></p>"></i></label>
																											<input type="number" name="arguments[<?php echo $tool['arguments']['filtering:min_dist_RE']['name']; ?>]" id="filtering_min_dist_RE" class="form-control" value="<?php echo $tool['arguments']['filtering:min_dist_RE']['default']; ?>" min="0" disabled>
                                                  </div>
																							</div>
																						</div>
                                          <div class="row">
																							<div class="col-md-6">
																									<div class="form-group display-hide" id="fg_min_fragment_size">
																											<label class="control-label"><?php echo $tool['arguments']['filtering:min_fragment_size']['description']; ?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['filtering:min_fragment_size']['help']; ?></p>"></i></label>
																											<input type="number" name="arguments[<?php echo $tool['arguments']['filtering:min_fragment_size']['name']; ?>]" id="filtering_min_fragment_size" class="form-control" value="<?php echo $tool['arguments']['filtering:min_fragment_size']['default']; ?>" min="0" disabled>
																									</div>
																							</div>
                                              <div class="col-md-6">
																									<div class="form-group display-hide" id="fg_max_fragment_size">
                                                      <label class="control-label"><?php echo $tool['arguments']['filtering:max_fragment_size']['description']; ?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['filtering:max_fragment_size']['help']; ?></p>"></i></label>
																											<input type="number" name="arguments[<?php echo $tool['arguments']['filtering:max_fragment_size']['name']; ?>]" id="filtering_max_fragment_size" class="form-control" value="<?php echo $tool['arguments']['filtering:max_fragment_size']['default']; ?>" min="0" disabled>
                                                  </div>
																							</div>
																					</div>
                                      </div>
                                  </div>
                              </div>
															<!-- END PORTLET 4: Filtering of artifactual reads -->



                              <div class="alert alert-danger err-tool display-hide">
                                  <strong>Error!</strong> You forgot to fill out some mandatory fields, please check them before submit the form.
                              </div>

                              <div class="alert alert-warning warn-tool display-hide">
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
    
									<div class="modal fade bs-modal-lg" id="modalDTStep2" tabindex="-1" role="basic" aria-hidden="true">
      	<div class="modal-dialog modal-lg">
    			<div class="modal-content">
        		<div class="modal-header">
      				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
     	 				<h4 class="modal-title">Select file(s)</h4>
        		</div>
        		<div class="modal-body"><div id="loading-datatable"><div id="loading-spinner">LOADING</div></div></div>
        		<div class="modal-footer">
      				<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
      				<button type="button" class="btn green btn-modal-dts2-ok" disabled>Accept</button>
        		</div>
    			</div>
    			<!-- /.modal-content -->
      	</div>
      	<!-- /.modal-dialog -->
  		</div>

 
<?php 

require "../../htmlib/footer.inc.php"; 
require "../../htmlib/js.inc.php";

?>
