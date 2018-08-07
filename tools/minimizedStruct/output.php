<?php

require "../../phplib/genlibraries.php";
redirectOutside();

$wd  = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/".$GLOBALS['tmpUser_dir']."/outputs_".$_REQUEST['execution'];
$indexFile = $wd.'/index';

$results = file($indexFile);

$dir = basename(getAttr_fromGSFileId($_REQUEST['execution'],'path'));

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
                                  <span>MD Energy Refinement</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Results
                            <small>Structure Energy Refinement using Atomistic Molecular Dynamics</small>
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
                        <div class="row">
                  			    <div class="col-md-12">
                        				<p style="margin-top:0;">
																	PDB output for <strong><?php echo basename($pathPDB); ?></strong> project.
																</p>
														</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<h2 class="font-green">Refined Structure</h2>
													</div>
												</div>
                        <div class="row">
                            <div class="col-md-12">
                           		 <!-- BEGIN EXAMPLE TABLE PORTLET -->
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
																										stage1.loadFile( "<?php echo $pathPDB; ?>/refined_structure.pdb", { defaultRepresentation: true } ).then( function( o ){
                                                      /*o.addRepresentation( "licorice", {
                                                        sele: "not(water or ion)", scale: 1.5, aspectRatio: 1.5
                                                      } );
																											stage1.setOrientation([[0,1,0],[1,1,0],[0,1,0]]);
																											stage1.centerView();
																											stage1.viewer.zoom(200, true);*/
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
                               <!-- END EXAMPLE TABLE PORTLET -->
 
														</div>
												</div>
												
	
											
											<div class="row" style="margin-top:30px;"></div>
			

                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../../htmlib/footer.inc.php"; 
require "../../htmlib/js.inc.php";

?>
