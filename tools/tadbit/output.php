<?php

require "../../phplib/genlibraries.php";
redirectOutside();

$wd  = $GLOBALS['dataDir'].$_SESSION['User']['id']."/.tmp/outputs_".$_REQUEST['project'];
$indexFile = $wd.'/index';

$results = file($indexFile);

$dir = basename(getAttr_fromGSFileId($_REQUEST['project'],'path'));

//processing results files

$tmp_dir  = $GLOBALS['dataDir'].'/'.$_SESSION['User']['id']."/.tmp/outputs_".$_REQUEST['project'];
$pathTemp = 'files/'               .$_SESSION['User']['id']."/.tmp/outputs_".$_REQUEST['project'];

$PNGs = glob("$tmp_dir/*.png");
$PNGs = array_map('basename',$PNGs);

$QC_plots      = array_values(preg_grep("/^QC-plot/",$PNGs));
$hist_frag     = array_values(preg_grep("/^histogram_fragment_sizes_/",$PNGs));
$coverage      = array_values(preg_grep("/^genomic_coverage_/",$PNGs));
$ints_vs_coord = array_values(preg_grep("/^interactions_vs_genomic-coords/",$PNGs));
$map_raw       = array_values(preg_grep("/^genomic_maps_raw/",$PNGs));
$bad_cols      = array_values(preg_grep("/^bad_columns_/",$PNGs));
$map_nrm       = array_values(preg_grep("/^genomic_maps_nrm/",$PNGs));
$comparts      = array_values(preg_grep("/^chr.*(?<!summ.png)$/",$PNGs));
$comparts_sum  = array_values(preg_grep("/^chr.*summ.png$/",$PNGs));
/*
var_dump($PNGs);
var_dump($QC_plots);
var_dump($hist_frag);
var_dump($coverage);
var_dump($ints_vs_coord);
var_dump($map_raw);
var_dump($bad_cols);
var_dump($map_nrm);
var_dump($comparts);
var_dump($comparts_sum);
*/

$pathTGZ = 'files/'.$_SESSION['User']['id']."/".$dir;

//project folder ID
$dirName = basename(getAttr_fromGSFileId($_REQUEST['project'],'path'));

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
                                  <span>TADbit</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLize()-->
                        <h1 class="page-title"> Results
                            <small>TADbit</small>
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
                        <div class="row">
              		    <div class="col-md-12">
					<p style="margin-top:0;">
					Download a compressed version of the results that include all the images and files previsualized here.
       					</p>
               		    </div>
               		    <div class="col-md-12">
					<div class="note note-info">
					<h4><a href="workspace/workspace.php?op=downloadFile&fn=<?php echo $results[0]; ?>" style="text-decoration:none;"><i class="fa fa-download"></i> Download all in a compressed tar.gz file</a></h4>
				</div>
                  	    </div>
                        </div>
			<!-- QC_plots -->
                        <div class="row">
			    <?php if ($QC_plots){
			     for ($i=0;$i<count($QC_plots)-1; $i=$i+2){
				if (isset($QC_plots[$i+1])){?>
				    <div class="col-md-6"><img src="<?php echo $pathTemp."/".$QC_plots[$i]; ?>" style="width:100%;"/></div>
				    <div class="col-md-6"><img src="<?php echo $pathTemp."/".$QC_plots[$i+1]; ?>" style="width:100%;"/></div>

				<?php }else{ ?>
				    <div class="col-md-6"><img src="<?php echo $pathTemp."/".$QC_plots[$i]; ?>" style="width:100%;"/></div>
				    <div class="col-md-6">&nbsp;</div>
				<?php }
	
			    } } ?>
                        </div>
			<div class="row">&nbsp;</div>


			<!-- Histogram fragments -->
			<?php if ($hist_frag){ ?>
			<div class="row">
				<div class="col-md-12"><img src="<?php echo $pathTemp."/".$hist_frag[0]; ?>" /></div>
                       </div>
			<div class="row">&nbsp;</div>
			<?php } ?>

			<!-- Genomic Coverage -->
			<?php if ($coverage){ ?>
			<div class="row">
				<div class="col-md-12"><img src="<?php echo $pathTemp."/".$coverage[0]; ?>" style="width:100%;" /></div>
                        </div>
			<div class="row">&nbsp;</div>
			<?php } ?>


			<!-- Interactions versus coords & Genomic maps raw -->
			<?php if ($ints_vs_coord || $map_raw){  ?>
			<div class="row">
				<?php if ($ints_vs_coord){ ?>
				<div class="col-md-6"><img src="<?php echo $pathTemp."/".$ints_vs_coord[0]; ?>" style="width:100%;" /></div>
				<?php }else{ ?>
				<div class="col-md-6">&nbsp;</div>
				<?php }

				if ($map_raw){ ?>
				<div class="col-md-6"><img src="<?php echo $pathTemp."/".$map_raw[0]; ?>" style="width:100%;" /></div>
				<?php }else{ ?>
				<div class="col-md-6">&nbsp;</div>
				<?php }?>
                        </div>
			<div class="row">&nbsp;</div>
			<?php } ?>

			<!-- Bad columns & Genomic  maps nrm -->
			<?php if ($bad_cols || $map_nrm){  ?>
			<div class="row">
				<?php if ($bad_cols){ ?>
				<div class="col-md-6"><img src="<?php echo $pathTemp."/".$bad_cols[0]; ?>" style="width:100%;" /></div>
				<?php }else{ ?>
				<div class="col-md-6">&nbsp;</div>
				<?php }

				if ($map_nrm){ ?>
				<div class="col-md-6"><img src="<?php echo $pathTemp."/".$map_nrm[0]; ?>" style="width:100%;" /></div>
				<?php }else{ ?>
				<div class="col-md-6">&nbsp;</div>
				<?php }?>
                        </div>
			<div class="row">&nbsp;</div>
			<?php } ?>


			<!-- Chr compartments -->
			<?php if ($comparts){ ?>
			<div class="row">
				<div class="col-md-12"><img src="<?php echo $pathTemp."/".$comparts[0]; ?>" style="width:100%;" /></div>
                        </div>
			<div class="row">&nbsp;</div>
			<?php } ?>

			<!-- Chr compartments summary -->
			<?php if ($comparts_sum){ ?>
			<div class="row">
				<div class="col-md-12"><img src="<?php echo $pathTemp."/".$comparts_sum[0]; ?>" style="width:100%;" /></div>
                        </div>
			<div class="row">&nbsp;</div>
			<?php } ?>



												<div class="row">
                            <div class="col-md-12">
															<div class="portlet light portlet-fit bordered">
                                  <div class="portlet-title">
                                    <div class="caption">
                                        <i class="icon-share font-red-sunglo hide"></i>
																				<span class="caption-subject font-dark bold uppercase">Summary</span>

																				<!--<div class="actions">
                                  <button type="submit" class="btn blue" style="float:right;">
                                      <i class="fa fa-check"></i> Open summary in new tab</button>
																				</div>-->
																		</div>
																		<div class="actions">
                                  <button type="button" onClick="window.open('../../<?php echo $pathTemp; ?>/summary.txt')" class="btn blue" style="float:right;">
                                      <i class="fa fa-check"></i> Open summary in new tab</button>
																				</div>
																	</div>
                                  <div class="portlet-body" style="word-wrap: break-word;">
	
																		<?php 

																			$handle = file_get_contents("../../".$pathTemp."/summary.txt", FILE_USE_INCLUDE_PATH);

																			echo nl2br($handle);

																		?>


																	</div>

																</div>
															</div>
													</div>




                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../../htmlib/footer.inc.php"; 
require "../../htmlib/js.inc.php";

?>
