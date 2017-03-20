<?php

require "../phplib/genlibraries.php";
redirectOutside();

$studies = array();
foreach (array_values(iterator_to_array($GLOBALS['studiesCol']->find(array(),array('experimenttype'=>1, 'releasedate'=>1, 'accession'=>1, 'organism'=>1, 'name'=>1, 'assays'=>1, 'files'=>1))->sort(array('releasedate'=>1)))) as $v)
	$studies[$v['_id']] = array($v['experimenttype'], $v['releasedate'], $v['accession'], $v['organism'], $v['name'], $v['assays'], $v['files']);

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
                                  <span>Get Data</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>From Repository List</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title">List of Experiments
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
											
												<div class="mt-element-step">
                                    <div class="row step-line">
                                        <div class="mt-step-desc">
																				Please select an experiment.
																				</div>

										<?php require "../htmlib/stepsup.inc.php"; ?>	
										
                                    </div>
                                </div>

	
                        <div class="row">
                            <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                <div class="portlet light portlet-fit bordered">
                                  <div class="portlet-title">
                                    <div class="caption">
                                        <i class="icon-share font-red-sunglo hide"></i>
                                        <span class="caption-subject font-dark bold uppercase">Browse Experiments</span>
                                    </div>
                                  </div>
									<div class="portlet-body">

										<div id="loading-datatable"><div id="loading-spinner">LOADING</div></div>

                                        <table class="table table-striped table-hover table-bordered" id="table-repository">
                                            <thead>
                                                <tr>
                                                    <th> Accession </th>
                                                    <th> Title </th>
                                                    <th> Type </th>
                                                    <th> Organism </th>
                                                    <th> Assays </th>
                                                    <th> Released </th>
                                                    <th> Processed </th>
                                                    <th> Raw </th>
                                                </tr>
                                            </thead>
                                            <tbody>
											  <?php
												foreach($studies as $key => $value):
											  ?>
												
											  <tr>
                                                <td><a href="repository/experiment.php?id=<?php echo $key; ?>"><?php echo $value[2]; ?></a></td>
                                                <td><?php echo $value[4]; ?></td>
                                                <td><?php echo $value[0]; ?></td>
                                                <td><?php 
												if(sizeof($value[3]) > 1){
													foreach($value[3] as $k => $v):
														echo $v.', ';
													endforeach;
												} else {
													echo $value[3]; 
												}
												?></td>
                                                <td style="text-align:right;"><?php echo $value[5]; ?></td>
												<td><?php echo $value[1]; ?></td>
												<?php 
													$files = array();
													$files['processed'] = array();
													$files['raw'] = array();
													foreach($value[6] as $k => $v):
													  
													  if((gettype($v['kind']) == 'array') || ($v['kind'] == 'raw')) {

														/*$aux = array();
														$aux['name'] = $v['name'];
														$aux['url'] = $v['url'];*/

														if(gettype($v['kind']) == 'array'){
															if(in_array('processed', $v['kind'])) $files['processed'][] = $v['url'];
														}

														if($v['kind'] == 'raw'){
															$files['raw'][] = $v['url'];
														}
													  
													  } 
													
													endforeach; 
												?>
												<td style="text-align:center;">
													<?php 
														switch(sizeof($files['processed'])){
															case '0': echo '&nbsp;';
															break;
															case '1': echo '<a href="'.$files['processed'][0].'"><i class="fa fa-download"></i></a>';
															break;
															default: echo '<a href="https://www.ebi.ac.uk/arrayexpress/experiments/'.$value[2].'/files/processed/" target="_blank"><i class="fa fa-link"></i></a>';
															break;
														}
													?>
												</td>
												<td style="text-align:center;">
													<?php 
														switch(sizeof($files['raw'])){
															case '0': echo '&nbsp;';
															break;
															case '1': echo '<a href="'.$files['raw'][0].'"><i class="fa fa-download"></i></a>';
															break;
															default: echo '<a href="https://www.ebi.ac.uk/arrayexpress/experiments/'.$value[2].'/files/raw/" target="_blank"><i class="fa fa-link"></i></a>';
															break;
														}
													?>
												</td>
                                              </tr>

											  <?php
												endforeach;
											  ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- END EXAMPLE TABLE PORTLET-->
                            </div>
                        </div>
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
