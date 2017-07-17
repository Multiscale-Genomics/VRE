<?php
require "phplib/global.inc.php";

$external_ap = "bignasim";
require "../master/header.php";

print headerMMB("BIGNASim database");

#$pdbJmol = "$GLOBALS[parmbsc1Dir]/NAFlex_lks1/CURVES/input.pdb";
#$code = "NAFlex_lks1";
$code = $_SESSION['idTraj'];
$pdbJmol = "$GLOBALS[parmbsc1Dir]/$code/INFO/structure.jsmol.pdb";

?>

<div id="pagewrap_vre">

    <div class="breadcrumb-trail breadcrumbs" itemprop="breadcrumb"><span class="trail-begin"><a href="/MuGVRE" title="Virtual Research Environment" rel="home" class="trail-begin">Home</a></span> <span class="sep">&raquo;</span> <span class="trail-end">BigNASim</span></div>


<script>
$(document).ready( function() {

        menuTabs("Browse");
	loadJMol();
});
</script>
<h3>BIGNASim database structure and analysis portal for nucleic acids simulation data</h3>


    <article class="principal_article" id="Visualization">
      <header>
        <hgroup>
          <h1 style="text-align: center;">Jsmol Interactive Visualization for <?=$code?></h1>
        </hgroup>
      </header>
      <div id="contentJsmol">
<?php
        include "htmlib/jmol.inc.htm";
?>
      </div>
    </article>

</div>


    <?php 

require "../master/footer.php";

//print footerMMB();
?>
