<?php

require "../../phplib/genlibraries.php";
redirectOutside();

InputTool_checkRequest($_REQUEST);

$from = InputTool_getOrigin($_REQUEST);

list($rerunParams,$inPaths) = InputTool_getPathsAndRerun($_REQUEST);

$dirName = InputTool_getDefExName();

// get tool details
$toolId = "tadbit_segment";
$tool   = getTool_fromId($toolId,1);

// TADBIT SEGMENT TOOL OPERATIONS:
// op = 0 || count(fn) = 1 / 2  -> BAM* + PICKLE


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
                        <h1 class="page-title"> TADbit Segment</h1>
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
															<input type="hidden" name="tool" value="<?php echo $toolId;?>" />
																				<input type="hidden" id="base-url"  value="<?php echo $GLOBALS['BASEURL']; ?>"/>
																				<input type="hidden" name="input_files_public_dir[refGenomes_folder]" value="refGenomes/" />

				 
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
                              <!-- BEGIN PORTLET 5: Normalization by ICE -->
                              <div class="portlet box blue" id="form-block-header4">
                                  <div class="portlet-title">
                                      <div class="caption">
																				 Segmentation
		                                  </div>
                                  </div>
                                  <div class="portlet-body form" id="form-block4">
																			<div class="form-body">

																				<h4 class="form-section">File inputs</h4>
																					<div class="row">

                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label">TADbit-generated BAM file <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Path to a TADbit-generated BAM file with filtered reads</p>"></i></label>
                                                      <select  name="input_files[bamin]" class="form-control">
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'BAM') { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
																									</div>-->
																									<?php $ff = matchFormat_File($tool['input_files']['bamin']['file_type'], $inPaths); ?>
																									<?php InputTool_printSelectFile($tool['input_files']['bamin'], $rerunParams['bamin'], $ff[0], false, true); ?>
																							</div>
																							<?php if(($_REQUEST["op"] == 0 && isset($_REQUEST["op"])) || (count($_REQUEST['fn']) == 2 && !isset($_REQUEST["op"]))) { ?>
																							<div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label">TADbit-generated PICKLE file <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Path to a TADbit-generated PICKLE file with HiC biases</p>"></i></label>
                                                      <select  name="input_files[hic_biases]" class="form-control">
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'PICKLE') { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
																									</div>-->
																									<?php $ff = matchFormat_File($tool['input_files']['hic_biases']['file_type'], $inPaths); ?>
																									<?php InputTool_printSelectFile($tool['input_files']['hic_biases'], $rerunParams['hic_biases'], $ff[0], false, false); ?>
																							</div>
																							<?php } ?>
                                          </div>
				
																				<h4 class="form-section">Settings</h4>
																					<?php InputTool_printSettings($tool['arguments'], $rerunParams); ?>
                                          <!--<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Resolution <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Resolution of the binning (should input a number between 10000 (10 kb) and 10000000 (10 Mb)).</p>"></i></label>
																											<input type="number" min="10000" max="10000000" name="arguments[resolution]" id="resolution" class="form-control" value="100000">
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">List of chromosomes names <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>List of chromosome names where to search for TAD borders or compartments (e.g.: chr1 chr2 chrX)</p>"></i></label>
																											<input type="text" name="arguments[chromosome_names]" id="chromosome_names" class="form-control form-field-enabled" >
                                                  </div>
																							</div>
																					</div>
																					<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Callers <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0;'>Search for TAD borders using TADbit's algorithm, and compartments using first EigenVector of the correlation matrix.</p>"></i></label>
                                                      <select class="form-control form-field-enabled valid select2_tad" name="arguments[callers][]" id="callers" aria-invalid="false" multiple="multiple">
                                                          <option value=""></option>
                                                          <option value="1" selected>call TAD calling</option>
                                                          <option value="2">call compartments</option>
																											</select>
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									
																							</div>-->
																					</div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 5: Normalization by ICE -->



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
