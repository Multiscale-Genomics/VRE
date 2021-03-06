<?php

require "../../phplib/genlibraries.php";
redirectOutside();

InputTool_checkRequest($_REQUEST);

$from = InputTool_getOrigin($_REQUEST);

list($rerunParams,$inPaths) = InputTool_getPathsAndRerun($_REQUEST);

$dirName = InputTool_getDefExName();

// get tool details
$toolId = "dnashape";
$tool   = getTool_fromId($toolId,1);

// DNASHAPE TOOL OPERATIONS:
// op = 0 || count(fn) = 2 -> TSV + FASTA

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
                        <h1 class="page-title"> <?php echo $tool['title']; ?> </h1>
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
															<!-- BEGIN PORTLET 2: OPTIONS -->
                              <div class="portlet box blue form-block-header" id="form-block-header1">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <i class="fa fa-cogs" ></i> Tool settings
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block1">
                                      <div class="form-body">
																					<h4 class="form-section">File inputs</h4>
																					<div class="row">

                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label">Binding Data <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>The tab-separated plain-text file must contain on each line a sequence and the corresponding (relative) binding affinity score.</p>"></i></label>
                                                      <select  name="input_files[binding_data]" class="form-control">
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'TSV') { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
																									</div>-->
																									<?php $ff = matchFormat_File($tool['input_files']['binding_data']['file_type'], $inPaths); ?> 
																									<?php InputTool_printSelectFile($tool['input_files']['binding_data'], $rerunParams['binding_data'], $ff[0], false, true); ?>
																							</div>

																							<div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label">Target <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Binding sites of the target protein will be predicted on the specified segment of the genome.</p>"></i></label>
                                                      <select  name="input_files[target]" class="form-control">
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'FASTA') { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
																									</div>-->
																									<?php $ff = matchFormat_File($tool['input_files']['target']['file_type'], $inPaths); ?> 
																									<?php InputTool_printSelectFile($tool['input_files']['target'], $rerunParams['target'], $ff[0], false, true); ?>
                                              </div>


                                          </div>

																					<h4 class="form-section">Settings</h4>
	
																						<div class="row">
                                              <div class="col-md-6">
												  											<!--<div class="form-group ">
                                                      <label class="control-label">Kmer1 <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Sequence information is encoded using a first-order feature, where each position along the DNA sequence is treated independently.</p>"></i></label>
                                                      <select class="form-control" name="arguments[kmer1]" id="kmer1" aria-invalid="false">
                                                          <option value="true" selected>True</option>
                                                          <option value="false">False</option>
                                                      </select>
																									</div>-->
																									<?php echo InputTool_printField($tool['arguments']['kmer1'], $rerunParams['kmer1']); ?>
																							</div>
																							<div class="col-md-6">
												  											<!--<div class="form-group ">
                                                      <label class="control-label">Shape <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Features that encode the shape of DNA (such as Minor Groove width, Roll, Propeller twist and Helical twist) are used to improve the quality of the binding site prediction.</p>"></i></label>
                                                      <select class="form-control" name="arguments[shape]" id="shape" aria-invalid="false">
                                                          <option value="true" selected>True</option>
                                                          <option value="false">False</option>
                                                      </select>
																									</div>-->	
																									<?php echo InputTool_printField($tool['arguments']['shape'], $rerunParams['shape']); ?>
																							</div>

                                          </div>

																					<div class="row">
																							<div class="col-md-6">
												  											<!--<div class="form-group ">
                                                      <label class="control-label">Maximum results <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>The specified number of top-scoring results will be included in the output.</p>"></i></label>
																											<input type="number" name="arguments[max_results]" id="max_results" class="form-control" value="1000">
																									</div>-->
																									<?php echo InputTool_printField($tool['arguments']['max_results'], $rerunParams['max_results']); ?>
																							</div>

                                              <div class="col-md-6">
												  											<!--<div class="form-group ">
                                                      <label class="control-label">Dimer <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Identify binding sites for a homodimer of the target protein; the relative orientation between the two proteins must be specified, as well as the spacing between the binding sites.</p>"></i></label>
                                                      <select class="form-control" name="arguments[dimer]" id="dimer" aria-invalid="false">
                                                          <option value="on">On</option>
                                                          <option value="off" selected>Off</option>
                                                      </select>
																									</div>-->
																									<?php echo InputTool_printField($tool['arguments']['dimer'], $rerunParams['dimer']); ?>
																							</div>

                                          </div>

																					<div class="row">
	
																							<div class="col-md-6">
												  											<div class="form-group dimer_group display-hide">
                                                      <label class="control-label"><?php echo $tool['arguments']['dimer:orientation']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['dimer:orientation']['help']?></p>"></i></label>
																											<select class="form-control" name="arguments[<?php echo $tool['arguments']['dimer:orientation']['name']?>]" id="dimer_orientation" aria-invalid="false" disabled>
																													<?php $enum_items = $tool['arguments']['dimer:orientation']['enum_items']; 
																														for ($i=0; $i<count($enum_items['name']); $i++) {
																															if($tool['arguments']['dimer:orientation']['default'] == $enum_items["name"][$i]) $sel = "selected";
																															else $sel = "";
																															echo '<option value="'.$enum_items["name"][$i].'" '.$sel.'>'.$enum_items["description"][$i].'</option>';
																														}
																													?>
                                                      </select>
                                                  </div>
																							</div>
	
																							<div class="col-md-6">
												  											<div class="form-group dimer_group display-hide">
                                                      <label class="control-label"><?php echo $tool['arguments']['dimer:spacing']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['dimer:spacing']['help']?></p>"></i></label>
																											<input type="number" name="arguments[<?php echo $tool['arguments']['dimer:spacing']['name']?>]" id="dimer_spacing" class="form-control" value="0" disabled>
                                                  </div>
																							</div>

                                          </div>

                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 2: OPTIONS -->

                             
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
