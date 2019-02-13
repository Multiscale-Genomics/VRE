<?php

require "../../phplib/genlibraries.php";
redirectOutside();

InputTool_checkRequest($_REQUEST);

$from = InputTool_getOrigin($_REQUEST);

list($rerunParams,$inPaths) = InputTool_getPathsAndRerun($_REQUEST);

$dirName = InputTool_getDefExName();

// LAIA ***********************
// set default values
$def = Array(
        "nucleR"=> Array(
                "width"       => 147,
                "pcKeepComp"  => 0.02,
                "dyad_length" => 50,
                "threshold" => "Percentage",
                "thresholdValue" => "",
                "thresholdPercentage" => "35%",
                "minoverlap" => 80,
                "wthresh"    => 0.6,
                "hthresh"    => 0.4
        ),
        "nucDyn" => Array(
                "range"     => "All",
                "equalSize" => "FALSE",
                "roundPow"  => 5,
                "readSize"  => 140,
                "maxDiff"   => 70 ,
                "maxLen"    => 140,
                "same_magnitude" => 2,
//              "threshold"=> "Percentage",
//              "thresholdValue" => "" ,
//              "thresholdPercentage" => "80%",
                "category"  => Array(),
                "scale"     => 2,
                "combined"  => "TRUE",
                "shift_min_nreads" => 3,
                "shift_threshold" => 0.075,
                "indel_min_nreads" => 15,
                "indel_threshold" => 0.5,
        ),
        "NFR" => Array(
                "minwidth"  => 110,
                "threshold" => 400
        ),
        "periodicity" => Array(
                "periodicity" => 165,
        ),
        "txstart" => Array(
                "window"        => 300,
                "open_thresh"   => 215,
                "max_uncovered" => 150
        ),
        "gausfitting" => Array(
                "range" => "All"
        ),
	"description" => "",
	"project" => $dirName
);


if (count($rerunParams)){
	$def_tmp=array_merge($def,$rerunParams);
	$def = $def_tmp;
}
// LAIA ***********************

// get tool details
$toolId = "nucldynwf";
$tool   = getTool_fromId($toolId,1);

// ND TOOL OPERATIONS:
// op = 0 || count(fn) = 1/2 -> BAM

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
                        <h1 class="page-title"> <?php echo $tool['title']; ?>
                        </h1>
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
																		<input type="hidden" name="input_files_public_dir[refGenome_chromSizes]" value="refGenomes/" />
															<input type="hidden" id="base-url"     value="<?php echo $GLOBALS['BASEURL']; ?>"/>

															<?php if(isset($_REQUEST['fn'])) { ?>
																<?php foreach ($_REQUEST['fn'] as $fn) { ?>
																	<input type="hidden" id="fn1" name="fn[]" value="<?php echo $fn; ?>" />
																<?php } ?>
															<?php } ?>
															<!--<input type="hidden" name="numInputs" id="numInputs" value="<?php echo count($_REQUEST['fn']); ?>" />-->

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

															<?php if(isset($rerunParams['nucleR'])) echo '<input type="hidden" class="default-block" id="default-block1" value="'.$rerunParams['nucleR'].'" />'; ?>

															<!-- BEGIN PORTLET 2: NUCLER -->
                              <div class="portlet box blue form-block-header" id="form-block-header1">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <input type="checkbox" class="make-switch switch-block" name="arguments[<?php echo $tool['arguments']['nucleR']['name']?>]" id="switch-block1" data-size="mini">
                                        <div style="float:right;margin-left:20px;"> <?php echo $tool['arguments']['nucleR']['description']?></div>
                                      </div>
                                      <div class="tools">
                                          <a href="javascript:;" class="collapse"></a>
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block1">
                                      <div class="form-body">
                                          <div class="row">
                                            <div class="col-md-12">
                                             <?php echo $tool['arguments']['nucleR']['help']?>
                                            </div>
                                          </div>
																					<h4>&nbsp;</h4>
																					<div class="row">
                                              <div class="col-md-12">
                                                  <!--<div class="form-group">
                                                      <label class="control-label">MNase-seq data <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Input MNase-seq data used to compute nucleosome positioning and the subsequent analyses.</p>"></i></label>
                                                      <select aria-invalid="false" multiple="multiple" name="input_files[MNaseSeq][]" id="params_nuclr_mnase" class="form-control form-field-enabled params_nuclr_inputs">
																												<?php foreach ($inPaths as $file) {  ?>
																													<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>" selected><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
						      																		</select>		
                                                  </div>-->
																									<?php 
																										$ff = matchFormat_File($tool['input_files']['condition1']['file_type'], $inPaths);
																										$p = [];
																										$r = 0;
																										foreach($ff as $fi) {
																											$p[] = $fi[0];
																											$r ++;
																										}
																									 ?> 
																									 <div class="form-group">
                                                    <label class="control-label">MNase-seq data <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Input MNase-seq data used to compute nucleosome positioning and the subsequent analyses.</p>"></i></label>
																										<div class="input-group">
																												<span class="input-group-addon" style="vertical-align: top; padding-top: 10px;"><i class="fa fa-file"></i></span>
																												<textarea 
																													name="visible_MNaseSeq"
																													class="form-control form-field-enabled field_required" 
																													style="height:<?php echo ($r > 1 ? $r*34 : 34); ?>px"
																													placeholder="<?php echo $GLOBALS['placeholder_textarea']; ?>"
																													readonly><?php echo implode("\n", $p); ?></textarea>
																												<div id="hidden_visible_MNaseSeq">
																												<?php foreach($ff as $fi) { ?>
																												<input type="hidden" name="input_files[MNaseSeq][]" class="form-field-enabled" value="<?php echo $fi[1]; ?>">
																												<?php } ?>
																												</div>
                                                        <span class="input-group-btn input-tool">
																														<button class="btn green" type="button" 
																														onclick="toolModal('visible_MNaseSeq', 'input_files[MNaseSeq][]', <?php echo getArrayJS($tool['input_files']['condition1']['data_type']); ?>, <?php echo getArrayJS($tool['input_files']['condition1']['file_type']); ?>, true);"><i class="fa fa-check-square-o"></i> Select</button>
                                                        </span>
                                                    </div>
                                                	</div>
                                              </div>
                                          </div>

																					<h4>&nbsp;</h4>
                                          <h6 class="form-section"></h6>

	                                        <div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucleR:width']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucleR:width']['help']?></p>"></i></label>
                                                      <input type="number" step="any" name="arguments[<?php echo $tool['arguments']['nucleR:width']['name']?>]" id="params_nuclr_width" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucleR:width']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucleR:width'], $rerunParams['nucleR:width']); ?>
                                              </div>
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucleR:minoverlap']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucleR:minoverlap']['help']?></p>"></i></label>
                                                      <input type="number" step="any" name="arguments[<?php echo $tool['arguments']['nucleR:minoverlap']['name']?>]" id="params_nuclr_minoverlap" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucleR:minoverlap']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucleR:minoverlap'], $rerunParams['nucleR:minoverlap']); ?>
                                              </div>
                                          </div>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucleR:dyad_length']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucleR:dyad_length']['help']?></p>"></i></label>
                                                      <input type="number" step="any" name="arguments[<?php echo $tool['arguments']['nucleR:dyad_length']['name']?>]" id="params_nuclr_dyad_len" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucleR:dyad_length']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucleR:dyad_length'], $rerunParams['nucleR:dyad_length']); ?>
                                              </div>
                                              <div class="col-md-6">
                                                <div class="form-group">
                                                <label class="control-label"><?php echo $tool['arguments']['nucleR:threshold']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucleR:threshold']['help']?></p>"></i></label>
                                                <div class="input-group">
                                                  <div class="input-icon right" id="nucr-perc">
																											<i class="fa fa-percent"></i>
																											<?php  
																											if(isset($rerunParams['nucleR:thresholdPercentage'])) $val1 = $rerunParams['nucleR:thresholdPercentage']; 
																											else $val1 = $tool['arguments']['nucleR:thresholdPercentage']['default'];
																											?>
                                                      <input type="number" step="1" min="0" max="100" class="form-control form-field-enabled" name="arguments[<?php echo $tool['arguments']['nucleR:thresholdPercentage']['name']?>]" id="nucleR_thresholdPercentage" value="<?php echo $val1; ?>" >
                                                  </div>
																									<div id="nucr-absval" class="display-hide">
																											<?php  
																											if(isset($rerunParams['nucleR:thresholdValue'])) $val2 = $rerunParams['nucleR:thresholdValue']; 
																											else $val2 = $tool['arguments']['nucleR:thresholdValue']['default'];
																											?>
                                                      <input type="number" step="any" class="form-control form-field-disabled" name="arguments[<?php echo $tool['arguments']['nucleR:thresholdValue']['name']?>]" id="nucleR_thresholdValue" value="<?php echo $val2; ?>">
                                                  </div>
                                                  <div class="input-group-btn" id="swbglev">
                                                      <input type="checkbox" class="make-switch" id="switch-bglevel" data-size="normal" data-on-text="Abs. Value" data-off-text="Percentage" data-on-color="info" data-off-color="info" data-label-text="Abs. Value">
                                                  </div>
                                                </div>
                                                <span class="help-block-form display-block" id="lab-nucr-perc"> <?php echo $tool['arguments']['nucleR:thresholdPercentage']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucleR:thresholdPercentage']['help']?></p>"></i></span>
                                                <span class="help-block-form display-hide" id="lab-nucr-absval"> <?php echo $tool['arguments']['nucleR:thresholdValue']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucleR:thresholdValue']['help']?></p>"></i></span>
                                                </div>
                                              </div>
                                          </div>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucleR:hthresh']['description']?>  <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucleR:hthresh']['help']?></p>"></i></label>
                                                      <input type="number" step="0.1" min="0" max="1" name="arguments[<?php echo $tool['arguments']['nucleR:hthresh']['name']?>]" id="params_nuclr_hthresh" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucleR:hthresh']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucleR:hthresh'], $rerunParams['nucleR:hthresh']); ?>
                                              </div>
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucleR:wthresh']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucleR:wthresh']['help']?></p>"></i></label>
                                                      <input type="number" step="0.1" min="0" max="1" name="arguments[<?php echo $tool['arguments']['nucleR:wthresh']['name']?>]" id="params_nuclr_wthresh" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucleR:wthresh']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucleR:wthresh'], $rerunParams['nucleR:wthresh']); ?>
                                              </div>
                                          </div>
                                          <h4 class="form-section">Advanced Settings</h4>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucleR:pcKeepComp']['description']?>  <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucleR:pcKeepComp']['help']?></p>"></i></label>
                                                      <input type="number" step="0.01" min="0" max="1" name="arguments[<?php echo $tool['arguments']['nucleR:pcKeepComp']['name']?>]" id="params_nuclr_pcKeepComp" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucleR:pcKeepComp']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucleR:pcKeepComp'], $rerunParams['nucleR:pcKeepComp']); ?>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 2: NUCLER -->
				
															<?php if(isset($rerunParams['nucDyn'])) echo '<input type="hidden" class="default-block" id="default-block2" value="'.$rerunParams['nucDyn'].'" />'; ?>

															<!-- BEGIN PORTLET 3: NUCLEOSOME DYNAMICS -->
															<?php if(count($_REQUEST['fn']) > 1 || isset($_REQUEST["op"]) || (isset($_REQUEST['rerunDir']))){ ?>
                              <div class="portlet box blue form-block-header" id="form-block-header2">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <input type="checkbox" class="make-switch switch-block" name="arguments[<?php echo $tool['arguments']['nucDyn']['name']?>]" id="switch-block2" data-size="mini">
                                        <div style="float:right;margin-left:20px;"> <?php echo $tool['arguments']['nucDyn']['description']?></div>
                                      </div>
                                      <div class="tools">
                                          <a href="javascript:;" class="collapse"></a>
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block2">
                                      <div class="form-body">
                                          <div class="row">
                                            <div class="col-md-12">
                                              <?php echo $tool['arguments']['nucDyn']['help']?>
                                            </div>
                                          </div>
                                          <h4>&nbsp;</h4>

                                          <div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label">MNase-seq reference state (condition C1) <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>MNase data used to define the initial state  when comparing nucleosome positioning</p>"></i></label>
                                                      <select  name="input_files[condition1]" id="params_nucdyn_c1" class="form-control form-field-enabled params_nucdyn_inputs">
																												<option selected value> -- select a file -- </option>
																													<?php foreach ($inPaths as $file) {  ?>
																													<?php $p = explode("/", $file['path']); ?>
                                                      		<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																													<?php } ?>
																											</select>		
																									</div>-->
																									<?php $ff = matchFormat_File($tool['input_files']['condition1']['file_type'], $inPaths); ?> 
																									<?php InputTool_printSelectFile($tool['input_files']['condition1'], $rerunParams['condition1'], $ff[0], false, true); ?>
																									<!--<div class="form-group">
                                                    <label class="control-label"><?php echo $tool['input_files']['condition1']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['input_files']['condition1']['help']?></p>"></i></label>
																										<div class="input-group">
																												<span class="input-group-addon"><i class="fa fa-file"></i></span>
                                                        <input type="text" 
																													name="visible_<?php echo $tool['input_files']['condition1']['name']?>" 
																													class="form-control form-field-enabled params_input" 
																													placeholder="<?php echo $GLOBALS['placeholder_input']; ?>" 
																													value="<?php echo $ff[0][0]; ?>"
																													readonly>
																												<input type="hidden" name="input_files[<?php echo $tool['input_files']['condition1']['name']?>]" value="<?php echo $ff[0][1]; ?>">
                                                        <span class="input-group-btn input-tool">
																														<button class="btn green" type="button" 
																														onclick="toolModal('visible_<?php echo $tool['input_files']['condition1']['name']?>', 'input_files[<?php echo $tool['input_files']['condition1']['name']?>]', <?php echo getArrayJS($tool['input_files']['condition1']['data_type']); ?>, false);"><i class="fa fa-check-square-o"></i> Select</button>
                                                        </span>
                                                    </div>
                                                	</div>-->
                                              </div>
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label">MNase-seq  final state (condition C2) <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>MNase data used to define the final state  when comparing nucleosome positioning</p>"></i></label>
                                                      <select  name="input_files[condition2]" id="params_nucdyn_c2" class="form-control form-field-enabled params_nucdyn_inputs">
																												<option selected value> -- select a file -- </option>
																													<?php foreach ($inPaths as $file) {  ?>
																													<?php $p = explode("/", $file['path']); ?>
                                                      		<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																													<?php } ?>
																											</select>		
																									</div>-->
																									<?php $ff = matchFormat_File($tool['input_files']['condition2']['file_type'], $inPaths); ?> 
																									<?php InputTool_printSelectFile($tool['input_files']['condition2'], $rerunParams['condition2'], $ff[1], false, true); ?>
																									<!--<div class="form-group">
                                                    <label class="control-label"><?php echo $tool['input_files']['condition2']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['input_files']['condition2']['help']?></p>"></i></label>
                                                    <div class="input-group">
																												<span class="input-group-addon"><i class="fa fa-file"></i></span>
                                                        <input type="text" 
																													name="visible_<?php echo $tool['input_files']['condition2']['name']?>" 
																													class="form-control form-field-enabled params_input" 
																													placeholder="<?php echo $GLOBALS['placeholder_input']; ?>" 
																													value="<?php echo $ff[1][0]; ?>"
																													readonly>
																												<input type="hidden" name="input_files[<?php echo $tool['input_files']['condition2']['name']?>]" value="<?php echo $ff[1][1]; ?>">
                                                        <span class="input-group-btn input-tool">
																														<button class="btn green" type="button" 
																														onclick="toolModal('visible_<?php echo $tool['input_files']['condition2']['name']?>', 'input_files[<?php echo $tool['input_files']['condition2']['name']?>]', <?php echo getArrayJS($tool['input_files']['condition2']['data_type']); ?>, false);"><i class="fa fa-check-square-o"></i> Select</button>
                                                        </span>
                                                    </div>
                                                	</div>-->
                                              </div>
                                          </div>




                                          <h4>&nbsp;</h4>
                                          <h6 class="form-section"></h6>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucDyn:range']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucDyn:range']['help']?></p>"></i></label>
                                                      <input type="text" name="arguments[<?php echo $tool['arguments']['nucDyn:range']['name']?>]" id="params_nucdyn_range" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucDyn:range']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucDyn:range'], $rerunParams['nucDyn:range']); ?>
                                              </div>
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucDyn:maxDiff']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucDyn:maxDiff']['help']?></p>"></i></label>
                                                      <input type="number" step="any" name="arguments[<?php echo $tool['arguments']['nucDyn:maxDiff']['name']?>]" id="params_nucdyn_maxdiff" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucDyn:maxDiff']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucDyn:maxDiff'], $rerunParams['nucDyn:maxDiff']); ?>
                                              </div>
                                          </div>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucDyn:maxLen']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucDyn:maxLen']['help']?></p>"></i></label>
                                                      <input type="number" step="any" name="arguments[<?php echo $tool['arguments']['nucDyn:maxLen']['name']?>]" id="params_nucdyn_maxlen" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucDyn:maxLen']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucDyn:maxLen'], $rerunParams['nucDyn:maxLen']); ?>
                                              </div>
                                              
                                          </div>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucDyn:shift_min_nreads']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucDyn:shift_min_nreads']['help']?></p>"></i></label>
                                                      <input type="number" step="any" name="arguments[<?php echo $tool['arguments']['nucDyn:shift_min_nreads']['name']?>]" id="params_nucdyn_shiftmn" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucDyn:shift_min_nreads']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucDyn:shift_min_nreads'], $rerunParams['nucDyn:shift_min_nreads']); ?>
                                              </div>
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucDyn:shift_threshold']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucDyn:shift_threshold']['help']?></p>"></i></label>
                                                      <input type="number" step="0.01" name="arguments[<?php echo $tool['arguments']['nucDyn:shift_threshold']['name']?>]" id="params_nucdyn_shiftth" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucDyn:shift_threshold']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucDyn:shift_threshold'], $rerunParams['nucDyn:shift_threshold']); ?>
                                              </div>
                                          </div>
                                          <div class="row">
                                  <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucDyn:indel_min_nreads']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucDyn:indel_min_nreads']['help']?></p>"></i></label>
                                                      <input type="number" step="any" name="arguments[<?php echo $tool['arguments']['nucDyn:indel_min_nreads']['name']?>]" id="params_nucdyn_indelmn" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucDyn:indel_min_nreads']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucDyn:indel_min_nreads'], $rerunParams['nucDyn:indel_min_nreads']); ?>
                                              </div>
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['nucDyn:indel_threshold']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucDyn:indel_threshold']['help']?></p>"></i></label>
                                                      <input type="number" step="0.01" name="arguments[<?php echo $tool['arguments']['nucDyn:indel_threshold']['name']?>]" id="params_nucdyn_indeth" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['nucDyn:indel_threshold']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['nucDyn:indel_threshold'], $rerunParams['nucDyn:indel_threshold']); ?>
                                              </div>
																					</div>
																					<h4 class="form-section">Advanced Settings</h4>
                                          <div class="row">
                                              <div class="col-md-6">
																								<div class="form-group">
                                              <label class="control-label"><?php echo $tool['arguments']['nucDyn:equal_size']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucDyn:equal_size']['help']?></p>"></i></label>
                                              <div class="input-group">
																								<div id="nucd-roundp">
																									<input class="form-control" disabled>
                                                  <!--<input type="number" step="any" class="form-control form-field-enabled" id="params_nucdyn_rpow" name="arguments[nucDyn:roundPow]" value="5" >-->
                                                </div>
																								<div id="nucd-reads" class="display-hide">
																										<?php  
																											if(isset($rerunParams['nucDyn:readSize'])) $val3 = $rerunParams['nucDyn:readSize']; 
																											else $val3 = $tool['arguments']['nucDyn:readSize']['default'];
																											?>
                                                    <input type="number" step="any" class="form-control" id="nucDyn_readSize" name="arguments[<?php echo $tool['arguments']['nucDyn:readSize']['name']?>]" value="<?php echo $val3; ?>" disabled>
                                                </div>
																								<div class="input-group-btn">
                                                    <input type="checkbox" class="make-switch" id="switch-eqsize" data-size="normal" data-on-text="TRUE" data-off-text="FALSE" data-on-color="info" data-off-color="default">
                                                </div>
                                              </div>
                                              <!--<span class="help-block-form display-hide" id="lab-nucd-roundp"> Round Power  <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>When <i>equalSize</i> is <i>FALSE</i>, the start and end of each read will be rounded to a power of this number to allow a more granular analysis.</p>"></i></span>-->
                                              <span class="help-block-form display-hide" id="lab-nucd-reads">  <?php echo $tool['arguments']['nucDyn:readSize']['description']?>  <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['nucDyn:readSize']['help']?></p>"></i></span>
                                              </div>

                                              </div>
                                          </div>
                                      </div>
                                  </div>
															</div>
															<?php } ?>
                              <!-- END PORTLET 3: NUCLEOSOME DYNAMICS -->

															<?php if(isset($rerunParams['NFR'])) echo '<input type="hidden" class="default-block" id="default-block3" value="'.$rerunParams['NFR'].'" />'; ?>

                              <!-- BEGIN PORTLET 4: NUCLEOSOME FREE REGIONS -->
                              <div class="portlet box blue form-block-header" id="form-block-header3">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <input type="checkbox" class="make-switch switch-block"  name="arguments[<?php echo $tool['arguments']['NFR']['name']?>]" id="switch-block3" data-size="mini">
                                        <div style="float:right;margin-left:20px;"> <?php echo $tool['arguments']['NFR']['description']?></div>
                                      </div>
                                      <div class="tools">
                                          <a href="javascript:;" class="collapse"></a>
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block3">
                                      <div class="form-body">
                                          <div class="row">
                                            <div class="col-md-12">
																							<?php echo $tool['arguments']['NFR']['help']?> 
																						</div>
                                          </div>
                                          <h4>&nbsp;</h4>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['NFR:minwidth']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['NFR:minwidth']['help']?></p>"></i></label>
                                                      <input type="number" step="any" name="arguments[<?php echo $tool['arguments']['NFR:minwidth']['name']?>]" id="params_nfr_minw" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['NFR:minwidth']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['NFR:minwidth'], $rerunParams['NFR:minwidth']); ?>
                                              </div>
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['NFR:threshold']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['NFR:threshold']['help']?></p>"></i></label>
                                                      <input type="number" step="any" name="arguments[<?php echo $tool['arguments']['NFR:threshold']['name']?>]" id="params_nfr_threshold" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['NFR:threshold']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['NFR:threshold'], $rerunParams['NFR:threshold']); ?>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 4: NUCLEOSOME FREE REGIONS -->

															<?php if(isset($rerunParams['periodicity'])) echo '<input type="hidden" class="default-block" id="default-block4" value="'.$rerunParams['periodicity'].'" />'; ?>

                              <!-- BEGIN PORTLET 5: NUCLEOSOME PHASING -->
                              <div class="portlet box blue form-block-header" id="form-block-header4">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <input type="checkbox" class="make-switch switch-block"  name="arguments[<?php echo $tool['arguments']['periodicity']['name']?>]" id="switch-block4" data-size="mini">
                                        <div style="float:right;margin-left:20px;"> <?php echo $tool['arguments']['periodicity']['description']?></div>
                                      </div>
                                      <div class="tools">
                                          <a href="javascript:;" class="collapse"></a>
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block4">
                                      <div class="form-body">
                                          <div class="row">
                                            <div class="col-md-12">
                                              <?php echo $tool['arguments']['periodicity']['help']?>
                                            </div>
                                          </div>
                                          <h4>&nbsp;</h4>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['periodicity:periodicity']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['periodicity:periodicity']['help']?></p>"></i></label>
                                                      <input type="number" step="any" name="arguments[<?php echo $tool['arguments']['periodicity:periodicity']['name']?>]" id="params_perio_perio" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['periodicity:periodicity']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['periodicity:periodicity'], $rerunParams['periodicity:periodicity']); ?>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 5: NUCLEOSOME PHASING -->

															<?php if(isset($rerunParams['txstart'])) echo '<input type="hidden" class="default-block" id="default-block5" value="'.$rerunParams['txstart'].'" />'; ?>

                              <!-- BEGIN PORTLET 6: TSS CLASSIFICATION -->
                              <div class="portlet box blue form-block-header" id="form-block-header5">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <input type="checkbox" class="make-switch switch-block"  name="arguments[<?php echo $tool['arguments']['txstart']['name']?>]" id="switch-block5" data-size="mini">
                                        <div style="float:right;margin-left:20px;"> <?php echo $tool['arguments']['txstart']['description']?></div>
                                      </div>
                                      <div class="tools">
                                          <a href="javascript:;" class="collapse"></a>
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block5">
                                      <div class="form-body">
                                          <div class="row">
                                            <div class="col-md-12">
                                              <?php echo $tool['arguments']['txstart']['help']?>
                                            </div>
                                          </div>
                                          <h4>&nbsp;</h4>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['txstart:window']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['txstart:window']['help']?></p>"></i></label>
                                                      <input type="number" step="any" name="arguments[<?php echo $tool['arguments']['txstart:window']['name']?>]" id="params_txstart_win" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['txstart:window']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['txstart:window'], $rerunParams['txstart:window']); ?>
                                              </div>
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['txstart:open_thresh']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['txstart:open_thresh']['help']?></p>"></i></label>
                                                      <input type="number" step="any" name="arguments[<?php echo $tool['arguments']['txstart:open_thresh']['name']?>]" id="params_txstart_opent" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['txstart:open_thresh']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['txstart:open_thresh'], $rerunParams['txstart:open_thresh']); ?>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 6: TSS CLASSIFICATION -->

															<?php if(isset($rerunParams['gausfitting'])) echo '<input type="hidden" class="default-block" id="default-block6" value="'.$rerunParams['gausfitting'].'" />'; ?>

                              <!-- BEGIN PORTLET 7: STIFFNESS -->
                              <div class="portlet box blue form-block-header" id="form-block-header6">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <input type="checkbox" class="make-switch switch-block" name="arguments[<?php echo $tool['arguments']['gausfitting']['name']?>]" id="switch-block6" data-size="mini">
                                        <div style="float:right;margin-left:20px;"> <?php echo $tool['arguments']['gausfitting']['description']?></div>
                                      </div>
                                      <div class="tools">
                                          <a href="javascript:;" class="collapse"></a>
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block6">
                                      <div class="form-body">
                                          <div class="row">
                                            <div class="col-md-12">
                                              <?php echo $tool['arguments']['gausfitting']['help']?> 
                                            </div>
                                          </div>
                                          <h4>&nbsp;</h4>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label"><?php echo $tool['arguments']['gausfitting:range']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['gausfitting:range']['help']?></p>"></i></label>
                                                      <input type="text" name="arguments[<?php echo $tool['arguments']['gausfitting:range']['name']?>]" id="params_gausfit_range" class="form-control form-field-enabled" value="<?php echo $tool['arguments']['gausfitting:range']['default']?>">
                                                  </div>-->
																									<?php echo InputTool_printField($tool['arguments']['gausfitting:range'], $rerunParams['gausfitting:range']); ?>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 7: STIFFNESS -->

                              <div class="alert alert-danger err-tool display-hide">
                                  <strong>Error!</strong> You forgot to fill out some mandatory fields, please check them before submit the form.
															</div>

															<div class="alert alert-danger err-blocks display-hide">
                                  <strong>Error!</strong> Is mandatory to select NucleR analysis.
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

                <div class="modal fade bs-modal" id="myModal1" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Warning!</h4>
                            </div>
                            <div class="modal-body"> This analysis uses nucleosome calls as input. 'NucleR' has been automatically selected to compute them. </div>
                            <div class="modal-footer">
                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Accept</button>
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
