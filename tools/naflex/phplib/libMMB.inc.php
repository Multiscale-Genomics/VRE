<?php
/*
* MDWeb
* libMMB.inc.php
* General layout
*/

function headerMMB($title, $menu=1) { # menu=1 top menu
    ob_start();
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?php echo $title?></title>
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['homeURL']?>css/estil.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['homeURL']?>css/slider.css" />
</head>
<body>
<div id="divbase">
  <div id="toplogo">
  <a id="top_box_inb_link" href="http://www.inab.org"><img src="<?php echo $GLOBALS['homeURL']?>/images/inb_logo_trans.png" height="90" border="0" alt="Instituto Nacional de Bioinformatica"></a>
    <?php if ($_SESSION['userData']['login']) {?>
    <div id="userId">
      <p><b>User:</b> <?php echo $_SESSION['userData']['name']." ".$_SESSION['userData']['surname']?></p>
    </div>
    <?php }?>
  </div>
  <?php if ($menu) {?>
  <div id="menu">
    <div class="itemmenu"><a href="<?=$GLOBALS['homeURL']?>/main.php">Home</a></div>
    <div class="itemmenu"><a href="<?=$GLOBALS['homeURL']?>/newProject.php">Start new project</a></div>
    <div class="itemmenu"><a href="<?=$GLOBALS['homeURL']?>/close.php">Close workspace</a></div>
    <div class="itemmenu" style='float:right'><a href="<?=$GLOBALS['homeURL']?>/help.php" target="_blank">Help</a></div>
    <div class="itemmenu" style='float:right'><a href="<?=$GLOBALS['homeURL']?>/help.php?id=tutorial" target="_blank">Setup Tutorial</a></div>
    <div class="itemmenu" style='float:right'><a href="<?=$GLOBALS['homeURL']?>/help.php?id=tutorialAnalysis" target="_blank">Analysis Tutorial</a></div>
  </div>
  <?php }?>
  <div id="content">
    <?php $txt = ob_get_contents();
                ob_end_clean();
                return $txt;
            };

function headerNA($title, $menu=1) { # menu=1 top menu
    ob_start();
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?php echo $title?></title>
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['homeURL']?>css/estil_NA.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['homeURL']?>css/slider.css" />
<link rel="icon" type="image/png" href="<?php echo $GLOBALS['homeURL']?>/images/DNA_extrusion.png" />
</head>
<body>
<script type="text/javascript">
        function checkUserDemo(){
                alert("User demo is not allowed to start new projects, it is just a read-only tutorial demo.\nSorry for the inconveniences.");
        }
</script>
<div id="divbase">
  <div id="toplogo">
  <a id="top_box_mdweb_link" href="<?php echo $GLOBALS['mmbURL']?>/MDWeb" target="_blank"><b>Powered by</b></p><img src="<?php echo $GLOBALS['homeURL']?>/images/LogoMDWEB.png" height="30" border="0" alt="Molecular Dynamics on Web"></a>
<!--  <a id="top_box_naflex_link" href="<?php echo $GLOBALS['mmbURL']?>/NAFlex2/"></a>-->
  <a id="top_box_inb_link" href="http://www.inab.org" target="_blank"><img src="<?php echo $GLOBALS['homeURL']?>/images/inb_logo_trans.png" height="90" border="0" alt="Instituto Nacional de Bioinformatica"></a>
    <?php if ($_SESSION['userData']['login']) {?>
    <div id="userId">
      <p><b>User:</b> <?php echo $_SESSION['userData']['name']." ".$_SESSION['userData']['surname']?></p>
    </div>
    <?php }?>
  </div>
  <?php if ($menu) {?>
  <div id="menu">
    <div class="itemmenu"><a href="<?=$GLOBALS['homeURL']?>/main.php">Home</a></div>
  <?php if ($_SESSION['userData']['login'] == 'demo') {?>
    <div class="itemmenu"><a href="javascript:checkUserDemo();" >Start new project</a></div>
  <?php } else {?>
    <div class="itemmenu"><a href="<?=$GLOBALS['homeURL']?>/newProject.php">Start new project</a></div>
  <?php }?>
    <div class="itemmenu"><a href="<?=$GLOBALS['homeURL']?>/close.php">Close workspace</a></div>
    <div class="itemmenu" style='float:right'><a href="<?=$GLOBALS['homeURL']?>/help.php" target="_blank">Help</a></div>
    <div class="itemmenu" style='float:right'><a href="<?=$GLOBALS['homeURL']?>/help.php?id=tutorialAnalysisNA" target="_blank">NAFlex Tutorial</a></div>
  </div>
  <?php }?>
  <div id="content">
    <?php $txt = ob_get_contents();
                ob_end_clean();
                return $txt;
};

function headerNA_ABC($title, $menu=1) { # menu=1 top menu
    ob_start();
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?php echo $title?></title>
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['homeURL']?>css/estil_NA.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['homeURL']?>css/slider.css" />
<link rel="icon" type="image/png" href="<?php echo $GLOBALS['homeURL']?>/images/DNA_extrusion.png" />
</head>
<body>
<script type="text/javascript">
        function checkUserDemo(){
                alert("User demo is not allowed to start new projects, it is just a read-only tutorial demo.\nSorry for the inconveniences.");
        }
</script>
<div id="divbase">
  <div id="toplogo">
  <a id="top_box_mdweb_link" href="<?php echo $GLOBALS['mmbURL'] ?>/MDWeb" target="_blank"><b>Powered by</b></p><img src="<?php echo $GLOBALS['homeURL']?>/images/LogoMDWEB.png" height="30" border="0" alt="Molecular Dynamics on Web"></a>
  <a id="top_box_abc_link" href="http://gbio-pbil.ibcp.fr/ABC/Welcome.html"><img src="<?php echo $GLOBALS['homeURL']?>/images/abc.png" height="60" width="120" border="0" alt="ABC Consortium"></a>
 <!-- <a id="top_box_naflex_link" href="<?php echo $GLOBALS['mmbURL']?>/NAFlex/ABC"></a>-->
  <a id="top_box_inb_link" href="http://www.inab.org"><img src="<?php echo $GLOBALS['homeURL']?>/images/inb_logo_trans.png" height="90" border="0" alt="Instituto Nacional de Bioinformatica" target="_blank"></a>
    <?php if ($_SESSION['userData']['login']) {?>
    <div id="userId">
      <p><b>User:</b> <?php echo $_SESSION['userData']['name']." ".$_SESSION['userData']['surname']?></p>
    </div>
    <?php }?>
  </div>
  <?php if ($menu) {?>
  <div id="menu">
    <div class="itemmenu"><a href="<?=$GLOBALS['homeURL']?>/mainABC.php">Home</a></div>
  <?php if ($_SESSION['userData']['login'] == 'demo') {?>
    <div class="itemmenu"><a href="javascript:checkUserDemo();" >Start new project</a></div>
  <?php } else {?>
    <div class="itemmenu"><a href="<?=$GLOBALS['homeURL']?>/newProjectABC.php">Start new project</a></div>
  <?php }?>
    <div class="itemmenu"><a href="<?=$GLOBALS['homeURL']?>/closeABC.php">Close workspace</a></div>
    <div class="itemmenu" style='float:right'><a href="<?=$GLOBALS['homeURL']?>/helpABC.php" target="_blank">NAFlex General Help</a></div>
    <div class="itemmenu" style='float:right'><a href="<?=$GLOBALS['homeURL']?>/helpABC.php?id=ABCtutorial" target="_blank">NAFlex ABC Tutorial</a></div>
  </div>
  <?php }?>
  <div id="content">
    <?php $txt = ob_get_contents();
                ob_end_clean();
                return $txt;
};

function footerMMB () {
                ob_start();
                ?>
  </div>
  <div id="bottomlogo">
    <div id="textBottom">  
     <a href="http://mmb.irbbarcelona.org"><img src="<?php echo $GLOBALS['homeURL']?>/images/mmb_logo_trans.png" height="50" border="0" alt="MMB group"></a>
     <br/> &nbsp;&nbsp;&nbsp;&nbsp;&copy; 2012. 
     <a href="mailto:mdweb@mmb.pcb.ub.es" target="_blank">Contact us.</a>
     <a id="bottom_box_scalalife_link" href="http://www.scalalife.eu"></a> 
     <a id="bottom_box_ub_link" href="http://www.ub.edu"></a> 
     <a id="bottom_box_bsc_link" href="http://www.bsc.es"></a> 
     <a id="bottom_box_isc3_link" href="http://www.isciii.es"></a> 
     <a id="bottom_box_irb_link" href="http://www.irbbarcelona.org"></a> 
     </div>
  </div>
</div>
<script type="text/javascript">
            var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
            document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
        </script>
<script type="text/javascript">
            try {
                var pageTracker = _gat._getTracker("UA-10694300-2");
                pageTracker._trackPageview();
            } catch(err) {}
        </script>
</body>
</html>
<?php
$txt =ob_get_contents();
ob_end_clean();
return $txt;
}

function footerNA () {
                ob_start();
                ?>
  </div>
  <div id="bottomlogo">
    <div id="textBottom">  
     <a id="mmbLogo" href="http://mmb.irbbarcelona.org" target="_blank"><img src="<?php echo $GLOBALS['homeURL']?>/images/mmb_logo_trans.png" height="50" border="0" alt="MMB group"></a>
     <a id="contactUs" href="mailto:naflex@mmb.pcb.ub.es">Contact us. &copy; 2012</a>
     <a id="bottom_box_scalalife_link" href="http://www.scalalife.eu" target="_blank"><img src="<?php echo $GLOBALS['homeURL']?>/images/scalalife.png" height="50" border="0" alt="Scalalife Project"></a> 
     <a id="bottom_box_ub_link" href="http://www.ub.edu" target="_blank"><img src="<?php echo $GLOBALS['homeURL']?>/images/logoUB.png" height="70" border="0" alt="Universitat de Barcelona"></a> 
     <a id="bottom_box_bsc_link" href="http://www.bsc.es" target="_blank"><img src="<?php echo $GLOBALS['homeURL']?>/images/BSC-Logo.png" height="80" border="0" alt="Barcelona Supercomputing Center"></a> 
     <a id="bottom_box_isc3_link" href="http://www.isciii.es" target="_blank"><img src="<?php echo $GLOBALS['homeURL']?>/images/isciii.png" height="80" border="0" alt="Instituto de Salud Carlos III"></a> 
     <a id="bottom_box_irb_link" href="http://www.irbbarcelona.org" target="_blank"><img src="<?php echo $GLOBALS['homeURL']?>/images/IRB_barcelona.png" height="80" border="0" alt="Institute for Research in Biomedicine"></a> 
     </div>
  </div>
</div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-48591078-2', 'irbbarcelona.org');
  ga('send', 'pageview');

</script>
</body>
</html>
<?php
$txt =ob_get_contents();
ob_end_clean();
return $txt;
}

function errorPage ($title, $text) {
return headerMMB($title).$text.footerMMB();
}

function formError ($idErr, $txtErr='') {
if ($_SESSION['errorData'][$idErr]) {
    return "<tr><td colspan=\"2\"><span style=\"color:red\">".$GLOBALS['errors']['formErrors'][$idErr]."</span></td></tr>";
} else
return '';
}
?>
