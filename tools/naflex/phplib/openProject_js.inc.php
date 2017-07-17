<?php
/*
 * openProject_js.inc.php
 */
?>
<script type="text/javascript" src="js/libform.js"></script>
<script type="text/javascript">
var opsHTML = Array();
var helpHTML = Array();

<? foreach ($ops['ops'] as $o) {
	$fileInc = "htmlib/ops/".$o['idOperacio'].".inc.htm";
	if (file_exists($fileInc)) { ?>
		opsHTML['<?=$o['idOperacio']."-".$o['subtype']?>']= '<?=str_replace("\r","",str_replace("\n","'\n+'",str_replace("'","\'",str_replace('##subtype##',$o['subtype'], file_get_contents($fileInc)))))?>';
	<?} else {?>
		opsHTML['<?=$o['idOperacio']."-".$o['subtype']?>']=<?=getOpsSecondaryHTML($o)?>;
	<?}
}?>

<?  foreach ($ops['ops'] as $o) {
		$op = $o['idOperacio'];
		$desc = '';
		$query = "SELECT descrip FROM operacions WHERE idOperacio= '$op'";
//		logger("JS Query: $query");
		$rs = getRecordSet($query);
		while ($rsobj = mysql_fetch_array($rs))  {
		        $desc = $rsobj['descrip'];
		}        
		$help = 'ops';
		if($desc==''){
			$query = "SELECT s.Description FROM MobyLiteDB.Service s,MDWeb.operacions o WHERE o.WS=s.name and o.idOperacio= '$op'";
//			logger("JS Query: $query");
			$rs = getRecordSet($query);
			while ($rsobj = mysql_fetch_array($rs))  {
				    $desc = $rsobj['Description'];
			}
		}
		if($desc==''){
			$help = 'workflows';
 		}
		$query = "SELECT descrip FROM Workflows WHERE idOperacio = '$op'";
//		logger("JS Query: $query");
		$rs = getRecordSet($query);
		while ($rsobj = mysql_fetch_array($rs))  {
		        $desc = $rsobj['descrip'];
		}        
//		$desc = str_replace("\n","<br>",$desc);

		if($help == 'workflows'){
			$twoParts = Array();
			$twoParts = preg_split ("/-SPLIT-/",$desc);
			$desc = "<ul><i><b>".str_replace("\n","</li><li>",str_replace("\n\n","</b><br><br>\n",str_replace('"','&quot;',$twoParts[0])))."</i></ul><ol><b>".str_replace("\n","</li><li>",str_replace("\n\n","</b><br><br>\n",str_replace('"','&quot;',$twoParts[1])))."</ol>";
		}
		else{
			$desc = "<ul><b>".str_replace("\n","</li><li>",str_replace("\n\n","</b><br><br>\n",str_replace('"','&quot;',$desc)))."</ul>";
		}
//		logger("JS: FINAL: $desc");
		if($o['idOperacio'] == "nucleicAcidAnalysis" ){
		?>
		helpHTML['<?=$o['idOperacio']."-".$o['subtype']?>'] = '<a href="<?php echo $GLOBALS['homeURL']?>help.php?id=naFlex" onclick = "javascript:window.open(this.href, \'width=300,height=300\');return false"><img border="0" width="30" onmouseover="tooltip.show(\'<?=$desc?>\');" onmouseout="tooltip.hide();" src="images/questionMark2.png" alt="Operation Info"/>';
		<?php
		} else if(preg_match("/^runDNA/",$o['idOperacio'])) {
		?>
		helpHTML['<?=$o['idOperacio']."-".$o['subtype']?>'] = '<a href="<?php echo $GLOBALS['homeURL']?>help.php?id=cg" onclick = "javascript:window.open(this.href, \'width=300,height=300\');return false"><img border="0" width="30" onmouseover="tooltip.show(\'<?=$desc?>\');" onmouseout="tooltip.hide();" src="images/questionMark2.png" alt="Operation Info"/>';
		<?php
		} else if(preg_match("/^CG/",$o['idOperacio'])) {
		?>
		helpHTML['<?=$o['idOperacio']."-".$o['subtype']?>'] = '<a href="<?php echo $GLOBALS['homeURL']?>help.php?id=cg" onclick = "javascript:window.open(this.href, \'width=300,height=300\');return false"><img border="0" width="30" onmouseover="tooltip.show(\'<?=$desc?>\');" onmouseout="tooltip.hide();" src="images/questionMark2.png" alt="Operation Info"/>';
		<?php
		} else if(preg_match("/^ABC/",$o['idOperacio'])) {
		?>
		helpHTML['<?=$o['idOperacio']."-".$o['subtype']?>'] = '<a href="<?php echo $GLOBALS['homeURL']?>help.php?id=abc" onclick = "javascript:window.open(this.href, \'width=300,height=300\');return false"><img border="0" width="30" onmouseover="tooltip.show(\'<?=$desc?>\');" onmouseout="tooltip.hide();" src="images/questionMark2.png" alt="Operation Info"/>';
		<?php
		} else {
		?>
		helpHTML['<?=$o['idOperacio']."-".$o['subtype']?>'] = '<a href="<?php echo $GLOBALS['mmbURL']?>MDWeb/help.php?id=<?=$help?>#<?=$op?>" onclick = "javascript:window.open(this.href, \'width=300,height=300\');return false"><img border="0" width="30" onmouseover="tooltip.show(\'<?=$desc?>\');" onmouseout="tooltip.hide();" src="images/questionMark2.png" alt="Operation Info"/>';
		<?php
		}
	}
?>

function calculateSnaps() {
    conf=document.getElementById("conf");
    timestep=document.getElementById("timestep");
    time=document.getElementById("time");
    steps=document.getElementById("steps");
    text=document.getElementById("snapshots");
    text.innerHTML=snaps;
    if(!conf.checked){
      if(time.value > 100){
  	steps.value = 1000;
      }
      else if(time.value > 10){
	steps.value = 500;
      }
    }
    var snaps=((time.value * 1000) / steps.value) / timestep.value;
    text=document.getElementById("snapshots");
    text.innerHTML=snaps;
}


function setVis (leaf) {
	obj = document.getElementById(leaf+'Div');
	if (obj.style.visibility=='visible')
		obj.style.visibility='hidden';
	else
		obj.style.visibility='visible';
}

function anchor (leaf) {
	document.location.hash = leaf;
	window.scrollBy(0,-100); // Vertical scroll (up)
}

<?if ($ops[leaf]) {?>
	setVis ('<?=$ops[leaf]?>');
	anchor ('<?=$ops[leaf]?>');

<?}?>
</script>
