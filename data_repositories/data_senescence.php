<?php

require "../phplib/genlibraries.php";
redirectOutside();

// get list of sample datasets for TADkit
$sampleList = [];
$curr_path = getcwd();
$path = "../visualizers/tadkit/tadkit/senescence_data/";
chdir($path);
$json_list = glob("*.json");
chdir($curr_path);

$webpath = $GLOBALS['BASEURL']."visualizers/tadkit/tadkit/#!/project/dataset?conf=senescence_data/";
foreach($json_list as $item) {

	$aux = [];
	$aux["path"] = $webpath.$item;
	$a1 = explode("_", $item);
	array_pop($a1);
	$aux["name"] = implode(" ", $a1);
	$sampleList[] = $aux;

}

?>

<?php require "../htmlib/header.inc.php"; ?>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid page-sidebar-fixed">
  <div class="page-wrapper">

  <?php require "../htmlib/top.inc.php"; ?>
  <?php require "../htmlib/menu.inc.php"; ?>

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
                                  <span>Data Repositories</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>Data Senescence</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title">Data Senescence
                        </h1>
                        <!-- END PAGE TITLE-->
												<!-- END PAGE HEADER-->

												<div class="row">
													<div class="col-md-12">
													<?php  
														$error_data = false;
														if ($_SESSION['errorData']){ 
															$error_data = true;
														?>
														<?php if ($_SESSION['errorData']['Info']) { ?> 
															<div class="alert alert-info">
														<?php } else { ?>
															<div class="alert alert-danger">
														<?php } ?>
															
																	<?php 
														foreach($_SESSION['errorData'] as $subTitle=>$txts){
																		print "<strong>$subTitle</strong><br/>";
																	foreach($txts as $txt){
																		print "<div>$txt</div>";
															}
														}
															unset($_SESSION['errorData']);
															?>
															</div>
															<?php } ?>
														</div>
													</div>
											
											<form name="sampleDataForm" id="sampleDataForm"  action="" method="post"  class="horizontal-form">

													<div class="portlet box blue-oleo">
                                  <div class="portlet-title">
                                      <div class="caption">
																				<div style="float:left;margin-right:20px;"> <i class="fa fa-database" ></i> Select dataset</div>
                                      </div>
                                  </div>
                                  <div class="portlet-body form">
                                    <div class="form-body">
                                        
																				<div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
																										<label class="control-label">List of models to visualize with TADkit visualizer</label>
                                    <select class="form-control form-field-enabled valid" name="" id="sample-data-viewers" aria-invalid="false">
																		<option value="">Please select a model</option>
																		<?php

                                    foreach ($sampleList as $sample){
                                        //$sampleName=$sample['name'];
																				?><option value="<?php echo $sample['path'] ?>"><?php echo $sample['name'];?></option><?php
                                    }
                                    ?>
                                    </select>
                                                </div>
                                            </div>
																						
																				</div>
																				
                                    </div>
                                  </div>
                              </div>

					</form>

                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
