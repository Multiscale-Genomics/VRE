<?php

require "../../phplib/genlibraries.php";
redirectOutside();

//$wd  = $GLOBALS['dataDir'].$_SESSION['User']['id']."/.tmp/outputs_".$_REQUEST['execution'];
$wd  = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/".$GLOBALS['tmpUser_dir']."/outputs_".$_REQUEST['execution'];
$indexFile = $wd.'/index';

$results = file($indexFile);

$dir = basename(getAttr_fromGSFileId($_REQUEST['execution'],'path'));

$pathTemp = 'files/'.$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/.tmp/outputs_".$_REQUEST['execution'];
//$pathPDB = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$dir;
//$pathPDB = 'files/'.$_SESSION['User']['id']."/".$dir;

//$createStrPNG = glob("$wd/create_str*png");
//$createTrajPNG = glob("$wd/create_traj*png");

//
//var_dump($pathTemp);
//var_dump($pathPDB);
//
//var_dump($createStrPNG);
//var_dump($createTrajPNG);

?>


<!DOCTYPE html>
<html lang="en">
<head>
		<title>Multiscale Complex Genomics | Virtual Research Environment</title>
		<base href="/" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
	<link href="assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />			
	<link href="assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
    <link rel="icon" href="http://vre.multiscalegenomics.eu/visualizers/ngl/assets/icon.png" sizes="32x32" />

    <!-- BEGIN PDI view CUSTOM SCRIPTS/CSS  -->
    <link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="tools/pdiview/assets/output/css/style.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>  
    <!-- BEGIN PDI view CUSTOM SCRIPTS/CSS  -->
    
</head>
<body>  

  <div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner ">
      <!-- BEGIN LOGO -->
      <div class="page-logo">
        <a href="workspace/">
          <img src="assets/layouts/layout/img/logo.png" alt="logo" class="logo-default" />
        </a>
      </div>
      <!-- END LOGO -->
    </div>
    <!-- END HEADER INNER -->
  </div>
  <!-- END HEADER -->

  <!-- BEGIN PDI View ------------------------------------------------- -->
  <div class="container">
    <div class="page">
      
      <div class="row">
        
        <div class="col-md-5">
          <!-- 1. Sequence logo -->
          <div id="seqlogo" class="carousel" data-ride="carousel" data-interval="">
            <div class="carousel-inner" role="listbox">
              <div class="item  active ">
							<img src="<?php echo $pathTemp; ?>/logo.png" alt="Twist">
              </div>
            </div>
          </div>

          <!-- 2. Phys logos -->
          <div id="logos" class="carousel helpar" data-ride="carousel" data-interval="">
            <div class="carousel-inner" role="listbox">
              
              <div class="item">
                <img src="<?php echo $pathTemp; ?>/twistL.png" alt="Twist">
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/minwL.png" alt="MinorGroove">
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/majwL.png" alt="MajorGroove">
              </div>
              
              <!-- <div class="item "> -->
              <!--   <img src="example/curvatureL.png" alt="Curvature"> -->
              <!-- </div> -->
              
              <!-- <div class="item "> -->
              <!--   <img src="example/radiusL.png" alt="Radius"> -->
              <!-- </div> -->
              
              <!-- <div class="item "> -->
              <!--   <img src="example/registerL.png" alt="Register"> -->
              <!-- </div> -->
              
              <!-- <div class="item "> -->
              <!--   <img src="example/ax-bendL.png" alt="Ax-bend"> -->
              <!-- </div> -->
              
              <div class="item active ">
                <img src="<?php echo $pathTemp; ?>/shearL.png" alt="Shear">
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/stretchL.png" alt="Stretch">
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/staggerL.png" alt="Stagger">
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/buckleL.png" alt="Buckle">
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/propellerL.png" alt="Propeller">
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/openingL.png" alt="Opening">
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/shiftL.png" alt="Shift">
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/slideL.png" alt="Slide">
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/riseL.png" alt="Rise">
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/tiltL.png" alt="Tilt">
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/rollL.png" alt="Roll">
              </div>
              
            </div>
          </div>

          <!-- 3. Plots -->
          <div id="plots" class="carousel helpar" data-ride="carousel" data-interval="">
            <div class="carousel-inner" role="listbox">
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/twist.png" alt="Twist">
                <!-- <div class="carousel-caption">Twist</div> -->
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/minw.png" alt="MinorGroove">
                <!-- <div class="carousel-caption">Width0</div> -->
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/majw.png" alt="MajorGroove">
                <!-- <div class="carousel-caption">Width1</div> -->
              </div>
              
              <!-- <div class="item "> -->
              <!--   <img src="example/curvature.png" alt="Curvature"> -->
              <!--   <\!-- <div class="carousel-caption">Curvature</div> -\-> -->
              <!-- </div> -->
              
              <!-- <div class="item "> -->
              <!--   <img src="example/radius.png" alt="Radius"> -->
              <!--   <\!-- <div class="carousel-caption">Radius</div> -\-> -->
              <!-- </div> -->
              
              <!-- <div class="item "> -->
              <!--   <img src="example/register.png" alt="Register"> -->
              <!--   <\!-- <div class="carousel-caption">Register</div> -\-> -->
              <!-- </div> -->
              
              <!-- <div class="item "> -->
              <!--   <img src="example/ax-bend.png" alt="Ax-bend"> -->
              <!--   <\!-- <div class="carousel-caption">Ax-bend</div> -\-> -->
              <!-- </div> -->
              
              <div class="item active ">
                <img src="<?php echo $pathTemp; ?>/shear.png" alt="Shear">
                <!-- <div class="carousel-caption">Shear</div> -->
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/stretch.png" alt="Stretch">
                <!-- <div class="carousel-caption">Stretch</div> -->
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/stagger.png" alt="Stagger">
                <!-- <div class="carousel-caption">Stagger</div> -->
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/buckle.png" alt="Buckle">
                <!-- <div class="carousel-caption">Buckle</div> -->
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/propeller.png" alt="Propeller">
                <!-- <div class="carousel-caption">Propeller</div> -->
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/opening.png" alt="Opening">
                <!-- <div class="carousel-caption">Opening</div> -->
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/shift.png" alt="Shift">
                <!-- <div class="carousel-caption">Shift</div> -->
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/slide.png" alt="Slide">
                <!-- <div class="carousel-caption">Slide</div> -->
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/rise.png" alt="Rise">
                <!-- <div class="carousel-caption">Rise</div> -->
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/tilt.png" alt="Tilt">
                <!-- <div class="carousel-caption">Tilt</div> -->
              </div>
              
              <div class="item ">
                <img src="<?php echo $pathTemp; ?>/roll.png" alt="Roll">
                <!-- <div class="carousel-caption">Roll</div> -->
              </div>
              
            </div>
            
            <!-- Controls -->
            <select id="plots_selector" class="ccontrol" data-target=".helpar">
              <!-- <option value="ax-bend" data-slide-to="3" >Ax-bend</option> -->
              <option value="shear" data-slide-to="3" selected >Shear</option>
              <option value="stretch" data-slide-to="4" >Stretch</option>
              <option value="stagger" data-slide-to="5" >Stagger</option>
              <option value="buckle" data-slide-to="6" >Buckle</option>
              <option value="propeller" data-slide-to="7" >Propeller</option>
              <option value="opening" data-slide-to="8" >Opening</option>
              <option value="shift" data-slide-to="9" >Shift</option>
              <option value="slide" data-slide-to="10" >Slide</option>
              <option value="rise" data-slide-to="11" >Rise</option>
              <option value="tilt" data-slide-to="12" >Tilt</option>
              <option value="roll" data-slide-to="13" >Roll</option>
              <option value="twist" data-slide-to="0" >Twist</option>
              <option value="minw" data-slide-to="1" >MinorGroove</option>
              <option value="majw" data-slide-to="2" >MajorGroove</option>
              <!-- <option value="curvature" data-slide-to="3" >Curvature</option> -->
              <!-- <option value="radius" data-slide-to="4" >Radius</option> -->
              <!-- <option value="register" data-slide-to="5" >Register</option> -->
            </select>
            <a class="l ccontrol" href=".helpar" role="button" data-slide="prev">
              <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="r ccontrol" href=".helpar" role="button" data-slide="next">
              <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
            </a>
          </div>
        </div>
        <div class="col-md-7">
          <!-- NGL for PDIView -->
          <!-- version 0.1 -->
          <script type="text/javascript" src="tools/pdiview/assets/output/js/vendor/ngl.js"></script>
          <script type="text/javascript" src="tools/pdiview/assets/output/js/representationgroup.js"></script>
          <script type="text/javascript" src="tools/pdiview/assets/output/js/pdiview.js"></script>
          <script type="text/javascript">
            $(document).ready(function() {
                ngl_viewer("<?php echo $pathTemp; ?>/output_X.pdb",
                           "<?php echo $pathTemp; ?>/output_B.pdb",
                           "<?php echo $pathTemp; ?>/output_R.pdb",
                           "<?php echo $pathTemp; ?>/output.pdb",
                           "<?php echo $pathTemp; ?>/pairings.dat",
                           "<?php echo $pathTemp; ?>/interactions.dat",
                           "<?php echo $pathTemp; ?>/sequence.dat");
            });
          </script>
          <div id="viewport" style="width:95%; height:470px; margin-top: 30px"></div>
          <div id="tooltip">Welcome!</div>
          <!-- /NGL -->
          
          <div class="row">
            <div class="col-md-12">
              <h4>Display interactions:</h4>
              <div id="sequence">
              </div>
            </div>
          </div>
          &nbsp;
          <div id="controls" class="controls row">
            <div class="col-md-12">
              <h4 id="controls-toggler-control">Viewer controls
                <span id="controls-openclose" class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>
              </h4>
              <div id="controls-toggler">
                <div id="lcontrols" class="lcontrols col-md-6"></div>
                <div id="rcontrols" class="rcontrols col-md-6"></div>
              </div>
            </div>
          </div>
        </div>        
      </div>
      
    </div>
  </div>
  <!-- END PDI View ------------------------------------------------- -->
  
  <!-- Footer -->
  <footer class="lopad">
    <div class="container">
      <div class="row">
        <div class="col-md-6 col-md-offset-3 text-center">
        </div>
      </div>
    </div>
  </footer>
  <!-- /Footer -->
  
</body>
</html>
