<?php

require "../phplib/genlibraries.php";
redirectOutside();

$exp = array();
foreach (array_values(iterator_to_array($GLOBALS['studiesCol']->find(array('_id'=>strtoupper($_GET['id'])),array('experimenttype'=>1, 'releasedate'=>1, 'accession'=>1, 'organism'=>1, 'name'=>1, 'assays'=>1, 'samples'=>1, 'arraydesign'=>1, 'protocol'=>1, 'description'=>1, 'lastupdatedate'=>1, 'provider'=>1, 'bibliography'=>1, 'files'=>1, 'secondaryaccession'=>1))->sort(array('releasedate'=>1)))) as $v)
	$exp[$v['_id']] = array($v['experimenttype'], $v['releasedate'], $v['accession'], $v['organism'], $v['name'], $v['assays'], $v['samples'], $v['arraydesign'], $v['protocol'], $v['description'], $v['lastupdatedate'], $v['provider'], $v['bibliography'], $v['files'], $v['secondaryaccession']);

$experiment = array();
$experiment = $exp[$_GET['id']];

//var_dump($experiment);

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
                                  <a href="repository/repositoryList.php">From Repository List</a>
																	<i class="fa fa-circle"></i>
                              </li>
							  							<li>
                                  <span><?php echo $experiment[2]; ?></span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title">Experiment <?php echo $experiment[2]; ?></h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->

												<div class="mt-element-step">
                                    <div class="row step-line">
                                        <div class="mt-step-desc">
																				Please select any data of this experiment and it will be automatically uploaded to your workspace.
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
                                        <span class="caption-subject font-dark bold uppercase"><?php echo $experiment[2].' - '.$experiment[4]; ?></span>
                                    </div>
                                  </div>
                                    <div class="portlet-body">

                                        <table class="table table-striped" id="table-experiment">

                                            <tbody>
                                              <tr>
                                                <td class="col-1-te">Status</td>
												<?php
												$d1 = date_create($experiment[1]);
												$d2 = date_create($experiment[10]);
												?>
                                                <td style="font-style:italic;">Released on <?php echo date_format($d1,"j F Y"); ?>, last updated on <?php echo date_format($d2,"j F Y"); ?></td>
                                              </tr>
                                              <tr>
                                                  <?php if(sizeof($experiment[3]) > 1){ ?>
												  <td class="col-1-te">Organisms</td>
                                                  <td>
												  <?php
													foreach($experiment[3] as $k => $v):
														echo $v.', ';
													endforeach;
												   ?>
												   </td>
												   <?php } else { ?>
												   <td class="col-1-te">Organism</td>
                                                   <td><?php  echo $experiment[3]; ?> </td>
												   <?php } ?>
                                                </tr>
                                                <tr>
                                                  <td class="col-1-te">Samples (<?php echo $experiment[6]; ?>)</td>
                                                  <td><a href="https://www.ebi.ac.uk/arrayexpress/experiments/<?php echo $experiment[2]; ?>/samples/" target="_blank">Click for detailed sample information and links to data</a></td>
                                                </tr>
												<?php if(isset($experiment[7])){ ?>
                                                <tr>
												  <?php if(is_multi_array($experiment[7])){ ?>
                                                  <td class="col-1-te">
													Arrays (<?php echo count($experiment[7]); ?>)</td>
                                                  <td>
													<?php foreach($experiment[7] as $k => $v):  ?>
													<a href="https://www.ebi.ac.uk/arrayexpress/arrays/<?php echo $v['accession']; ?>/?ref=<?php echo $experiment[2]; ?>" target="_blank"><?php echo $v['name']; ?></a><br>
													<?php endforeach; ?>
												  </td>
                                                  <?php } else { ?>
												  <td class="col-1-te">
													Array (1)</td>
                                                  <td><a href="https://www.ebi.ac.uk/arrayexpress/arrays/<?php echo $experiment[7]['accession']; ?>/?ref=<?php echo $experiment[2]; ?>" target="_blank"><?php echo $experiment[2].' - '.$experiment[7]['name']; ?></a></td>
												  <?php } ?>
												</tr>
												<?php } ?>
                                                <tr>
                                                  <td class="col-1-te">Protocols (<?php echo sizeof($experiment[8]); ?>)</td>
                                                  <td><a href="https://www.ebi.ac.uk/arrayexpress/experiments/<?php echo $experiment[2]; ?>/protocols/" target="_blank">Click for detailed protocol information</a></td>
                                                </tr>
                                                <tr>
                                                  <td class="col-1-te">Description</td>
                                                  <td><?php echo $experiment[9]; ?></td>
												</tr>
                                                <tr>
												  <?php if(sizeof($experiment[0]) > 1 ){ ?>
                                                  <td class="col-1-te">Experiment types</td>
                                                  <td>
												  <?php foreach($experiment[0] as $k => $v):  
												  	echo $v.', '; 
												  endforeach; ?>
												  </td>
												  <?php } else { ?>
												  <td class="col-1-te">Experiment type</td>
                                                  <td><?php echo $experiment[0]; ?></td>
												  <?php } ?>
                                                </tr>
												<?php if(isset($experiment[11])){ ?>
                                                <tr>
												  <?php if(is_multi_array($experiment[11])){ ?>
                                                  <td class="col-1-te">
													Contacts</td>
                                                  <td>
													<?php foreach($experiment[11] as $k => $v):  ?>
													<?php if(($v['role'] == 'submitter') && (isset($v['email']))) { ?>
                                                  	<a href="mailto:<?php echo $v['email']; ?>"> <i class="fa fa-envelope"></i> <?php echo $v['contact']; ?></a>, 
													<?php }else{ ?>
													<?php echo $v['contact']; ?>,  
													<?php } ?>
													<?php endforeach; ?>
												  </td>
                                                  <?php } else { ?>
												  <td class="col-1-te">
													Contact</td>
                                                  <td><a href="mailto:<?php echo $experiment[11]['email']; ?>"> <i class="fa fa-envelope"></i> <?php echo $experiment[11]['contact']; ?></a></td>
												  <?php } ?>
												</tr>
												<?php } ?>
												<?php if(isset($experiment[12])){ ?>
                                                <tr>
												  <td class="col-1-te">
													Citation</td>
                                                  <td><a href="http://europepmc.org/abstract/MED/<?php echo $experiment[12]['accession']; ?>" target="_blank"><?php echo $experiment[12]['title']; ?></a> <?php echo $experiment[12]['authors']; ?></td>
												</tr>
												<?php } ?>
												<?php if(isset($experiment[13])){ ?>
                                                <tr>
												  <?php if(is_multi_array($experiment[13])){ ?>
                                                  <td class="col-1-te">
													Files</td>
                                                  <td>
													<?php 
													$files = array();
													$files['idf'] = array();
													$files['processed'] = array();
													$files['raw'] = array();
													$files['sdrf'] = array();
													$files['adf'] = array();
													$files['twocolumns'] = array();
													$files['biosamples'] = array();
													$files['mageml'] = array();
													$files['additional'] = array();
													foreach($experiment[13] as $k => $v): 
														$aux['name'] = $v['name'];
														$aux['url'] = $v['url'];
														if(gettype($v['kind']) != 'NULL') {
															if(gettype($v['kind']) != 'array') array_push($files[$v['kind']], $aux);
															else if(in_array('processed', $v['kind'])) array_push($files['processed'], $aux);
														}else{
															array_push($files['additional'], $aux);
														}	
													endforeach; ?>
													<table>
													<tbody>
														<?php if(!empty($files['idf'])){ ?>
														<tr>
															<td class="col-1-subt">Investigation description <?php if(sizeof($files['idf']) > 1) echo '('.sizeof($files['idf']).')'; ?></td>
															<td class="col-2-subt">	
															<?php foreach($files['idf'] as $k => $v): ?>
																<a href="<?php echo $v['url']; ?>" target="_blank"><i class="fa fa-download"></i> <?php echo $v['name']; ?></a> 
															<?php endforeach; ?>
															</td>
														</tr>
														<?php } ?>
														<?php if(!empty($files['sdrf'])){ ?>
														<tr>
															<td class="col-1-subt">Sample and data relationship <?php if(sizeof($files['sdrf']) > 1) echo '('.sizeof($files['sdrf']).')'; ?></td>
															<td class="col-2-subt">	
															<?php foreach($files['sdrf'] as $k => $v): ?>
																<a href="<?php echo $v['url']; ?>" target="_blank"><i class="fa fa-download"></i> <?php echo $v['name']; ?></a> 
															<?php endforeach; ?>
															<td>
														</tr>
														<?php } ?>
														<?php if(!empty($files['raw'])){ ?>
														<tr>
															<td class="col-1-subt">Raw data <?php if(sizeof($files['raw']) > 1) echo '('.sizeof($files['raw']).')'; ?></td>
															<td class="col-2-subt">	
															<?php foreach($files['raw'] as $k => $v): ?>
																<a href="<?php echo $v['url']; ?>" target="_blank"><i class="fa fa-download"></i> <?php echo $v['name']; ?></a>&nbsp;&nbsp;&nbsp; 
															<?php endforeach; ?>
															<td>
														</tr>
														<?php } ?>
														<?php if(!empty($files['processed'])){ ?>
														<tr>
															<td class="col-1-subt">Processed data <?php if(sizeof($files['processed']) > 1) echo '('.sizeof($files['processed']).')'; ?></td>
															<td class="col-2-subt">
															<?php if(sizeof($files['processed']) <= 6) { ?>	
															<?php foreach($files['processed'] as $k => $v): ?>
																<a href="<?php echo $v['url']; ?>" target="_blank"><i class="fa fa-download"></i> <?php echo $v['name']; ?></a>&nbsp;&nbsp;&nbsp; 
															<?php endforeach; ?>
															<?php }else{ ?>
																<a href="https://www.ebi.ac.uk/arrayexpress/experiments/<?php echo $experiment[2]; ?>/files/processed/" target="_blank"><i class="fa fa-folder"></i> Click to browse processed data</a>
															<?php } ?>
															<td>
														</tr>
														<?php } ?>
														<?php if(!empty($files['biosamples'])){ ?>
														<tr>
															<td class="col-1-subt">Experiment design <?php if(sizeof($files['biosamples']) > 1) echo '('.sizeof($files['biosamples']).')'; ?></td>
															<td class="col-2-subt">	
															<?php foreach($files['biosamples'] as $k => $v): ?>
																<a href="<?php echo $v['url']; ?>" target="_blank"><i class="fa fa-download"></i> <?php echo $v['name']; ?></a>&nbsp;&nbsp;&nbsp; 
															<?php endforeach; ?>
															<td>
														</tr>
														<?php } ?>
														<?php if(!empty($files['adf'])){ ?>
														<tr>
															<td class="col-1-subt">Array design <?php if(sizeof($files['adf']) > 1) echo '('.sizeof($files['adf']).')'; ?></td>
															<td class="col-2-subt">	
															<?php foreach($files['adf'] as $k => $v): ?>
																<a href="<?php echo $v['url']; ?>" target="_blank"><i class="fa fa-download"></i> <?php echo $v['name']; ?></a>&nbsp;&nbsp;&nbsp; 
															<?php endforeach; ?>
															<td>
														</tr>
														<?php } ?>
														<?php if(!empty($files['additional'])){ ?>
														<tr>
															<td class="col-1-subt">Additional data <?php if(sizeof($files['additional']) > 1) echo '('.sizeof($files['additional']).')'; ?></td>
															<td class="col-2-subt">	
															<?php foreach($files['additional'] as $k => $v): ?>
																<a href="<?php echo $v['url']; ?>" target="_blank"><i class="fa fa-download"></i> <?php echo $v['name']; ?></a>&nbsp;&nbsp;&nbsp; 
															<?php endforeach; ?>
															<td>
														</tr>
														<?php } ?>

													</tbody>
													</table>
												  </td>
                                                  <?php } else { ?>
												  <td class="col-1-te">
													File</td>
                                                  <td>One File!</td>
												  <?php } ?>
												  </td>
												</tr>
												<?php } ?>
												<?php if(isset($experiment[14])){ ?>
                                                <tr>
												  <td class="col-1-te">
													Links</td>
                                                  <td><a href="http://www.ncbi.nlm.nih.gov/projects/geo/query/acc.cgi?acc=<?php echo $experiment[14]; ?>" target="_blank">GEO -  <?php echo $experiment[14]; ?></a></td>
												</tr>
												<?php } ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- END EXAMPLE TABLE PORTLET-->
																<div class="form-actions">
			
																	<div id="bottom-validated-files">

																		<div id="go-out-uploadform"><input type="button" class="btn default" value="BACK TO REPOSITORY LIST" onclick="location.href='/repository/repositoryList.php';" /></div>
																
																	</div>

																</div>
	
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
