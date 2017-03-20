<?php
require "phplib/global.inc.php";
print headerMMB("PDB mirror", array(), False);
?>
<script>
    $(document).ready(function () {
        menuTabs("SupplMat");
    });
</script>
<style type="text/css">
    #ContentHelp P {text-align: justify; font-size: 1em; line-height:1.5em}
    #ContentHelp .titol {font-family: Georgia, serif; font-size: 1.5em}
    #ContentHelp h4 {border-bottom: 1px solid; font-size: 1.2em;}
    #ContentHelp pre {text-align: left}
</style>


<h3>BIGNASim database structure and analysis portal for nucleic acids simulation data</h3>

<div class="metaImageSection">
    <div class="metaImage" style="flex-wrap: nowrap;">
        <?php
        if (!$_REQUEST['id'])
            $_REQUEST['id'] = "base";
        $menu = Array(
            'base' => 'Supplementary Material',
            'database' => 'Database Structure',
            'ontology' => 'Ontology',
            'usecases' => 'Examples of use',
            'suppTables' => 'Supplementary Tables',
            'suppFigs' => 'Supplementary Figures',
            'references' => 'References',
        );
        $sep = Array(
            'usecases' => 1,
            'references' => 1,
        );
        $titols = Array(
            'base' => 'Supplementary Material',
            'database' => 'Database Structure',
            'ontology' => 'Ontology',
            'usecases' => 'Examples of use',
            'suppTables' => 'Supplementary Tables',
            'suppFigs' => 'Supplementary Figures',
            'references' => 'References',
            'usecase1' => 'Example 1. Obtaining information about the Drew-Dickerson dodecamer',
            'usecase2' =>'Example 2. Visualitzation of globals analysis based in xCGy fragments',
            'usecase3' => 'Example 3. Obtaining a Meta-trajectory (xCGy fragment)',
            'usecase4' => 'Example 4. Analysis using fragment hierarchy. Correlation between CpG twist and ζ torsions.',
            'usecase5' => 'Example 5. Combining Experimental and MD analysis'
            
        );
        ?>
        <div id="MenuHelp">
	    <a href="getFile.php?fileloc=dat/SupplMat.pdf&type=curves"> <p class="curvesDatText">Download <br/>Suppl. Mat. (pdf)</p></a>
            <?php
            foreach (array_keys($menu) as $id) {
                if ($sep[$id]) {
                    echo "----<br/>";
                }
                ?>
                <p><a class="itemMenu" href="SuppMaterial.php?id=<?php print $id ?>">
                        <?php
                        if ($_REQUEST['id'] == $id)
                            echo "<strong>" . $menu[$id] . "</strong>";
                        else
                            echo $menu[$id];
                        print "</a></p>";
                    }
                    ?>
        </div>
        <div id="ContentHelp">
          <br/> 
            <?php if (isset($titols[$_REQUEST['id']])) {?>
                <h4><?php print $titols[$_REQUEST['id']] ?></h4>
            <?php }
            if (file_exists("htmlib/SuppMaterial/$_REQUEST[id].inc.htm"))
                include "htmlib/SuppMaterial/$_REQUEST[id].inc.htm";
            ?>
        </div>
    </div>
</div>

<?php
print footerMMB();
?>
