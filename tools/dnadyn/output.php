<?php

require "../../phplib/genlibraries.php";
redirectOutside();

$wd  = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/".$GLOBALS['tmpUser_dir']."/outputs_".$_REQUEST['execution'];
$indexFile = $wd.'/index';

$results = file($indexFile);

$dir = basename(getAttr_fromGSFileId($_REQUEST['execution'],'path'));

$pathTemp = 'files/'.$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/.tmp/outputs_".$_REQUEST['execution'];
//$pathPDB = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$dir;
$pathPDB = 'files/'.$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/".$dir;

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
                                  <span>MC-DNA</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Results
                            <small>MC-DNA</small>
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
																<h4><a href="workspace/workspace.php?op=downloadFile&fn=<?php echo $results[0]; ?>" style="text-decoration:none;"><i class="fa fa-download"></i> Download all in a compressed tar.gz file</a></h4>
															</div>
														</div>
												</div>
												<?php if(sizeof($createStrPNG) > 0) { ?>
												<div class="row">
													<div class="col-md-12">
														<h2 class="font-green">Structure</h2>
													</div>
												</div>
                        <div class="row">
                            <div class="col-md-12">
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
																										stage1.loadFile( "<?php echo $pathPDB; ?>/mc_dna_eq_str.pdb", { defaultRepresentation: false } ).then( function( o ){
                                                      o.addRepresentation( "licorice", {
                                                        sele: "not(water or ion)", scale: 1.5, aspectRatio: 1.5
                                                      } );
																											stage1.setOrientation([[0,1,0],[1,1,0],[0,1,0]]);
																											stage1.centerView();
																											stage1.viewer.zoom(200, true);
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
 
														</div>
												</div>
												<div class="portlet light portlet-fit bordered">
													<div class="portlet-title">
														<div class="caption">
																<i class="icon-share font-red-sunglo hide"></i>
																<span class="caption-subject font-dark bold uppercase">BENDING</span>
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

	
											<?php } ?>

											<?php if(sizeof($createTrajPNG) > 0) { ?>

												<div class="row">
													<div class="col-md-12">
														<h2 class="font-green">Trajectory</h2>
													</div>
												</div>
                        <div class="row">
                            <div class="col-md-12">
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
																										stage2.loadFile( "<?php echo $pathPDB; ?>/mc_dna_str.pdb", { defaultRepresentation: false } )
																										.then( function( o ){
																											$("#loading-trajectories").show();
																											var framesPromise = NGL.autoLoad( "<?php echo $pathPDB; ?>/mc_dna_str.dcd")
																											.then( function( frames ){
																													var traj = o.addTrajectory( frames ).trajectory;
																													var player = new NGL.TrajectoryPlayer( traj, {
																															step: 2,
																															timeout: 100,
																															start: 0,
																															end: traj.frames.length,
																															interpolateType: "linear",
																															mode: "loop"
																													} );
																													player.end = traj.frames.length;
																													traj.setPlayer( player );
																													traj.player.play();
																													$("#loading-trajectories").hide();
																											} );
																											o.addRepresentation( "licorice", {
                                                        sele: "not(water or ion)", scale: 1.5, aspectRatio: 1.5
                                                      } );
																											stage2.setOrientation([[0,1,0],[1,1,0],[0,1,0]]);
																											stage2.centerView();
																											stage2.viewer.zoom(100, true);
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
 
														</div>
												</div>
												<div class="portlet light portlet-fit bordered">
													<div class="portlet-title">
														<div class="caption">
																<i class="icon-share font-red-sunglo hide"></i>
																<span class="caption-subject font-dark bold uppercase">BENDING</span>
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
