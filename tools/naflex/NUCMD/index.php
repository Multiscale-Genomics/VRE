<?php
require "phplib/global.inc.php";

$external_ap = "bignasim";
require "../master/header.php";

print headerMMB("BIGNASim database structure and analysis portal for nucleic acids simulation data");
?>

<div id="pagewrap_vre">

<!--    <div class="tc-hot-crumble container" role="navigation"><div class="row"><div class="span12"><div class="breadcrumb-trail breadcrumbs" itemprop="breadcrumb"><span class="trail-begin"><a href="/MuGVRE" title="Virtual Research Environment" rel="home" class="trail-begin">Home</a></span> <span class="sep">&raquo;</span> <span class="trail-end">BigNASim</span></div></div></div></div>-->

<div class="breadcrumb-trail breadcrumbs" itemprop="breadcrumb"><span class="trail-begin"><a href="/MuGVRE" title="Virtual Research Environment" rel="home" class="trail-begin">Home</a></span> <span class="sep">&raquo;</span> <span class="trail-end">BigNASim</span></div>



<script>
$(document).ready( function() {

        menuTabs("Home");

});
</script>

<h3>BIGNASim database structure and analysis portal for nucleic acids simulation data</h3>

<!--<div class="metaImageSection" style="background:url(css/images/dnaPattern.png) no-repeat;>-->
<div class="metaImageSection">
    <div class="metaImage">
	<div>
<p style="font-size:1.2em;">
Here we present <strong>BIGNASim</strong>, a comprehensive platform including a database system and an analysis portal, aimed to be a general database for handling <strong>nucleic acids simulations</strong>.<br/>
	</div>
	<div>
		<a href="browse.php" id="DownloadTab"><img src="images/pcazip_nuc.gif" alt="" style='padding:10px;text-align:left;box-shadow:  inset 0 0 10px #000000; width: 200px; height: 200px;'></a>
		<!--<h3><a href="help.php?id=download" id="DownloadTab">ParmBSC1 Forcefield DOWNLOAD</a></h3>-->
		<h3><a href="browse.php" id="DownloadTab">Browse BIGNASim</a></h3>
		<h3><a href="help.php?id=download" id="DownloadTab">ParmBSC1 Forcefield DOWNLOAD</a></h3>
	</div>
	<div>
<p style="font-size:1.2em;">
<strong>BIGNASim</strong> allows direct access to <strong>individual trajectory data</strong> and <strong>pre-computed analyses</strong>, as well as <strong>global analyses</strong> performed on the whole database. <br/><br/>
New <strong>BigData</strong> technology, such as <strong>noSQL</strong> databases (<strong><a href="https://wwww.mongodb.org" target="_blank">mongoDB</a></strong> and <strong><a href="http://cassandra.apache.org/" target="_blank">cassandra</a></strong>), ensures an efficient storage, management and retrieval of important information from huge <strong>MD trajectory files</strong>.<br/><br/>
As an initial stage, <strong>BIGNASim</strong> has been populated with the trajectories prepared during the development and validation of the <strong>ParmBSC1 force-field</strong>. The <strong>force-field</strong> was tested for more than 3 years on <strong>100 different DNA systems (>100 &mu;seconds of aggregated simulation time)</strong>, and all the results can be easily accessed using <strong>BIGNASim</strong> interface.
</br><br/>
The technology used in <strong>BIGNASim</strong> platform assures its <strong>scalability</strong>, and the <strong>flexibility</strong> of the data layout puts no practical limits to the kind of analysis data to store. We expect it will become a <strong>reference site</strong> for groups working in MD simulations of nucleic acids.
</p>
	</div>
    </div>
</div>

</div><!-- end pagewrap_vre -->


<?php

require "../master/footer.php";

//print footerMMB();
?>
