<?php
require "phplib/global.inc.php";
print headerMMB("BIGNASim database structure and analysis portal for nucleic acids simulation data");

if (preg_match("/MuG/",$_SERVER[REQUEST_URI])){
echo "MuG BIGNASim: ($_SERVER[REQUEST_URI])";
}
?>
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
		<img src="images/pcazip_nuc.gif" alt="" style='padding:10px;text-align:left;box-shadow:  inset 0 0 10px #000000; width: 200px; height: 200px;'>
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

<?php
print footerMMB();
?>
