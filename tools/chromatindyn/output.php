<?php

require "../../phplib/genlibraries.php";
redirectOutside();

$wd  = $GLOBALS['dataDir'].$_SESSION['User']['id']."/.tmp/outputs_".$_REQUEST['project'];
$indexFile = $wd.'/index';

$results = file($indexFile);

$dir = basename(getAttr_fromGSFileId($_REQUEST['project'],'path'));

$pathTemp = 'files/'.$_SESSION['User']['id']."/.tmp/outputs_".$_REQUEST['project'];
//$pathPDB = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$dir;
$pathPDB = 'files/'.$_SESSION['User']['id']."/".$dir;

$createStrPNG = glob("$wd/create_str*png");
$createTrajPNG = glob("$wd/create_traj*png");

//
//var_dump($pathTemp);
//var_dump($pathPDB);
//
//var_dump($createStrPNG);
//var_dump($createTrajPNG);

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
                                  <span>Chromatin Dynamics</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Results
                            <small>Chromatin Dynamics</small>
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->

												<div class="row">
                  			    <div class="col-md-12">
                        				<p style="margin-top:0;">
																	General Statistics for <strong><?php echo basename($pathPDB); ?></strong> project.
                                </p>
												</div>
												<div class="col-md-12">
														<div class="note note-info" style="padding-bottom:7px;">
																<h4><a href="workspace/workspace.php?op=downloadFile&fn=<?php echo $results[0]; ?>" style="text-decoration:none;"><i class="fa fa-download"></i> Download all in a compressed tar.gz file </a></h4>
															</div>
														</div>
												</div>
												<?php if(sizeof($createStrPNG) > 0) { ?>
												<div class="row">
													<div class="col-md-12">
														<h2 class="font-green">Structure</h2>
													</div>
												</div>

												<div class="portlet light portlet-fit bordered">
													<div class="portlet-title">
														<div class="caption">
																<i class="icon-share font-red-sunglo hide"></i>
																<span class="caption-subject font-dark bold uppercase">OUTPUT STRUCTURE COMMENTS</span>
														</div>
													</div>
														<div class="portlet-body">
																<?php
																		if (($handle = file_get_contents($wd."/create_str.txt", FILE_USE_INCLUDE_PATH)) !== FALSE) {
																			$htmlval = $handle;
																		}else {
																			$htmlval = "Error reading data file";
																		}

																		echo $htmlval;
																?>
															</div>
												</div>


												<!-- BEGIN EXAMPLE TABLE PORTLET-->
													<div class="portlet light portlet-fit bordered">
														<div class="portlet-title">
															<div class="caption">
																	<i class="icon-share font-red-sunglo hide"></i>
																	<span class="caption-subject font-dark bold uppercase">3D VISUALIZATION</span>
															</div>
														</div>
															<div class="portlet-body">
																<div class="row">
																	<div class="col-md-12">
																			<div id="loading-viewport1" style="position:absolute;left:45%; top:200px;"><img src="/assets/layouts/layout/img/ring-alt.gif" /></div>
																			<script>
																					document.addEventListener( "DOMContentLoaded", function(){
																							stage1 = new NGL.Stage( "viewport1", {backgroundColor:"#ddd"} );
																							stage1.loadFile( "<?php echo $pathPDB; ?>/chromdyn_str.pdb", { defaultRepresentation: false } ).then( function( o ){
																								o.addRepresentation( "ball+stick", { sele: "#C",
																								radiusType: 'size', radius: 1.6, color:"residueindex", colorScale: "GnBu" } );
																								o.addRepresentation( "ball+stick", { sele: "#N",
																								radiusType: 'size', radius: 10, color:"uniform", colorValue:"#000000" } );
																								//stage1.setOrientation([[0,0,0],[0,1,0],[0,1,0]]);
																								stage1.centerView();
																								$("#loading-viewport1").hide();
																							} );
																					} );
																					function handleResize(){ if(typeof stage1 != 'undefined') stage1.handleResize(); }
																					window.addEventListener( "resize", handleResize, false );
																			</script>
																			<div id="viewport1" style="width:100%; height:500px;background:#ddd;"></div>
																		</div>
																</div>
															</div>
													</div>
													<!-- END EXAMPLE TABLE PORTLET-->
 
												<div class="portlet light portlet-fit bordered">
													<div class="portlet-title">
														<div class="caption">
																<i class="icon-share font-red-sunglo hide"></i>
																<span class="caption-subject font-dark bold uppercase">DISTANCE / DECAY</span>
														</div>
													</div>
														<div class="portlet-body">
															<div class="row">
																<?php foreach($createStrPNG as $a) { ?>
																	<div class="col-md-6">
																		<img src="<?php echo $pathTemp."/".basename($a); ?>" style="width:100%;" />
																	</div>
																<?php } ?>
															</div>
															</div>
												</div>

												<div class="portlet light portlet-fit bordered">
													<div class="portlet-title">
														<div class="caption">
																<i class="icon-share font-red-sunglo hide"></i>
																<span class="caption-subject font-dark bold uppercase">DATA TABLE</span>
														</div>
													</div>
														<div class="portlet-body">
																<?php
																		$row = 1;
																		if (($handle = fopen($wd."/create_str_out.csv", "r")) !== FALSE) {
																			$htmlval = '<table class="table table-striped">';
																			while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE)/* && ($row <= 51)*/) {
																																$num = count($data);
																				if($row == 1) {
																					$htmlval = $htmlval."<thead>";
																					$col = 'th';
																				}else{ 
																					if($row == 2) $htmlval = $htmlval."<tbody>";
																					$col = 'td';
																				}
																				$htmlval = $htmlval."<tr>";
																					for ($c=0; $c < $num; $c++) {
																							if($data[$c] != "" || trim($data[$c]) != " "){
																									$htmlval = $htmlval. '<'.$col.'>'.$data[$c].'</'.$col.'>';
																							}else{
																									$htmlval = $htmlval. '<'.$col.'>&nbsp;</'.$col.'>';
																							}
																					}
																					if($row == 1) $htmlval = $htmlval."</tr></thead>";
																					else $htmlval = $htmlval."</tr>";
																					$row++;
																			}
																			$htmlval = $htmlval."</tbody></table>";
																			fclose($handle);
																		}else {
																			$htmlval = "Error reading data file";
																		}

																		echo $htmlval;
																?>
															</div>
												</div>


	
											<?php } ?>

											<?php if(sizeof($createTrajPNG) > 0) { ?>

												<div class="row">
													<div class="col-md-12">
														<h2 class="font-green">Trajectory</h2>
													</div>
												</div>

												<div class="portlet light portlet-fit bordered">
													<div class="portlet-title">
														<div class="caption">
																<i class="icon-share font-red-sunglo hide"></i>
																<span class="caption-subject font-dark bold uppercase">OUTPUT STRUCTURE COMMENTS</span>
														</div>
													</div>
														<div class="portlet-body">
																<?php
																		if (($handle = file_get_contents($wd."/create_tra.txt", FILE_USE_INCLUDE_PATH)) !== FALSE) {
																			$htmlval = $handle;
																		}else {
																			$htmlval = "Error reading data file";
																		}

																		echo $htmlval;
																?>
															</div>
												</div>

											<!-- BEGIN EXAMPLE TABLE PORTLET-->
												<div class="portlet light portlet-fit bordered">
													<div class="portlet-title">
														<div class="caption">
																<i class="icon-share font-red-sunglo hide"></i>
																<span class="caption-subject font-dark bold uppercase">3D VISUALIZATION</span>
														</div>
													</div>
														<div class="portlet-body">
															<div class="row">
																<div class="col-md-12">
																		<div id="loading-viewport2" style="position:absolute;left:45%; top:200px;"><img src="/assets/layouts/layout/img/ring-alt.gif" /></div>
																		<div id="loading-trajectories" class="font-green" style="position:absolute; width:100%; top:470px;display:none; text-align:center;">
																			Loading trajectories <i class="fa-li fa fa-spinner fa-pulse fa-spin" style="position: relative;margin-left: 25px;top: 0;"></i>
																		</div>
																		<script>
																				document.addEventListener( "DOMContentLoaded", function(){
																						stage2 = new NGL.Stage( "viewport2", {backgroundColor:"#ddd"} );
																						stage2.loadFile( "<?php echo $pathPDB; ?>/chromdyn_start_str.pdb", { defaultRepresentation: false } )
																						.then( function( o ){
																							$("#loading-trajectories").show();
																							var framesPromise = NGL.autoLoad( "<?php echo $pathPDB; ?>/chromdyn_str.dcd")
																							.then( function( frames ){
																									var traj = o.addTrajectory( frames ).trajectory;
																									var player = new NGL.TrajectoryPlayer( traj, {
																											step: 2,
																											timeout: 200,
																											start: 0,
																											end: traj.frames.length,
																											interpolateType: "spline",
																											mode: "loop"
																									} );
																									player.end = traj.frames.length;
																									traj.setPlayer( player );
																									traj.player.play();
																									$("#loading-trajectories").hide();
																							} );
																							o.addRepresentation( "ball+stick", { sele: "#C",
																							radiusType: 'size', radius: 1.6, color:"residueindex", colorScale: "GnBu" } );
																							o.addRepresentation( "ball+stick", { sele: "#N",
																							radiusType: 'size', radius: 10, color:"uniform", colorValue:"#000000" } );
																							stage2.centerView();
																							$("#loading-viewport2").hide();
																						} );
																				} );
																				function handleResize(){ if(typeof stage2 != 'undefined') stage2.handleResize(); }
																				window.addEventListener( "resize", handleResize, false );
																		</script>
																		<div id="viewport2" style="width:100%; height:500px;background:#ddd;"></div>
																	</div>
															</div>
														</div>
												</div>
												<!-- END EXAMPLE TABLE PORTLET-->
 
												<div class="portlet light portlet-fit bordered">
													<div class="portlet-title">
														<div class="caption">
																<i class="icon-share font-red-sunglo hide"></i>
																<span class="caption-subject font-dark bold uppercase">DISTANCE / DECAY</span>
														</div>
													</div>
														<div class="portlet-body">
															<div class="row">
																<?php foreach($createTrajPNG as $a) { ?>
																	<div class="col-md-6">
																		<img src="<?php echo $pathTemp."/".basename($a); ?>" style="width:100%;" />
																	</div>
																<?php } ?>
															</div>
															</div>
												</div>

												<div class="portlet light portlet-fit bordered">
													<div class="portlet-title">
														<div class="caption">
																<i class="icon-share font-red-sunglo hide"></i>
																<span class="caption-subject font-dark bold uppercase">DATA TABLE</span>
														</div>
													</div>
														<div class="portlet-body">
																<?php
																		$row = 1;
																		if (($handle = fopen($wd."/create_traj_out.csv", "r")) !== FALSE) {
																			$htmlval = '<table class="table table-striped">';
																			while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE)/* && ($row <= 51)*/) {
																																$num = count($data);
																				if($row == 1) {
																					$htmlval = $htmlval."<thead>";
																					$col = 'th';
																				}else{ 
																					if($row == 2) $htmlval = $htmlval."<tbody>";
																					$col = 'td';
																				}
																				$htmlval = $htmlval."<tr>";
																					for ($c=0; $c < $num; $c++) {
																							if($data[$c] != "" || trim($data[$c]) != " "){
																									$htmlval = $htmlval. '<'.$col.'>'.$data[$c].'</'.$col.'>';
																							}else{
																									$htmlval = $htmlval. '<'.$col.'>&nbsp;</'.$col.'>';
																							}
																					}
																					if($row == 1) $htmlval = $htmlval."</tr></thead>";
																					else $htmlval = $htmlval."</tr>";
																					$row++;
																			}
																			$htmlval = $htmlval."</tbody></table>";
																			fclose($handle);
																		}else {
																			$htmlval = "Error reading data file";
																		}

																		echo $htmlval;
																?>
															</div>
												</div>

	
											<?php } ?>

											<div class="row" style="margin-top:30px;"></div>


                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../../htmlib/footer.inc.php"; 
require "../../htmlib/js.inc.php";

?>
