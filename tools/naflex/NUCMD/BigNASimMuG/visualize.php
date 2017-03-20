<?php
require "phplib/global.inc.php";
print headerMMB("BIGNASim database");

#$pdbJmol = "$GLOBALS[parmbsc1Dir]/NAFlex_lks1/CURVES/input.pdb";
#$code = "NAFlex_lks1";
$code = $_SESSION['idTraj'];
$pdbJmol = "$GLOBALS[parmbsc1Dir]/$code/INFO/structure.jsmol.pdb";

?>
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
    <?php 
print footerMMB();

