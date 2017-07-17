<?php
# libForm
# Gesti� de formularis
# versio PHP
#
# es mante per tenir les variables en una matriu si cal

function getInput ($nom,$m,$val,$size, $extra='') {
	//CF handy function to update the title
	logger("Nom: $nom, Valor: $val");
	if ($m) {
		if ("$nom" == "pdbcode") return "<input onchange='updateTitle();' onmousemove='updateTitle();' onkeyup='updateTitle();' name='$nom' id='$nom' $extra value='$val' size='$size' />";
		if ("$nom" == "titol") return "<input onchange='changeTitle();' onkeyup='changeTitle();' name='$nom' id='$nom' $extra value='$val' size='$size' />";
		else return "<input name='$nom' id='$nom' $extra value='$val' size='$size' />";
	} else { 
		return $val;
	}
}

function getTextArea ($nom,$m,$val,$c,$r, $extra='') {
	if ($m) 
		return "<textarea $extra name='$nom' rows='$r' cols='$c'>$val</textarea>";
//		return "<textarea $extra wrap='virtual' name='$nom' rows='$r' cols='$c'>$val</textarea>";
	else
		return $val;
}

function getSelect ($nom, $m, $dicc, $val, $mult = 0, $size = 5, $deftxt = "", $extra ='') {
        $outsel = "";
	if ($mult) {
		foreach ((array)$val as $i)
			$val0[$i]=1;
	}
	if ($m) {
                if ($extra)
                    $extra = " ".$extra;
		$outsel = "<select".$extra;

		if ($mult)  //multiple select
			$outsel .= " multiple size='$size' name='$nom"."[]'>";
		else
			$outsel .= " name=\"$nom\" id=\"$nom\"";
            if ($nom=="ffForm")
                $outsel .= " onchange='selectTrajTop();'";
            else if ($nom=="projType") {
                $outsel .= " onchange='disable()'";
            }
            $outsel .= ">";
            if ($deftxt) {   //default text
			$outsel .= "<option value='XX'";
			if (($val == "") or ($val == "XX"))
				$outsel .= " selected=\"selected\" ";
			$outsel .= ">$deftxt</option>";
		}
            if ($dicc) {
                foreach ($dicc as $k => $v) {
                    $outsel .= "<option value=\"$k\"";
                    if (($val == $k) or ($mult and $val0[$k]))
                        $outsel .= " selected=\"selected\" ";
                    $outsel .= ">$v</option>";
                }
            }
		$outsel .= "</select>";
	} else {
		if ($mult) {
			$vals=Array();
			foreach ((array)$val as $i)
				$vals[] = $dicc[$i];
			$outsel = join (", ", $vals);
		} else 
			$outsel = $dicc[$val];
	}
	return $outsel;
}

function getCBox ($nom,$m,$valor,$txt,$esq=0) {
	$tt = "";
	if ($m) {
		$tt = " <input name='$nom' type='checkbox' ";
 		if ($valor == "on") 
			$tt .= "checked";
		$tt .= "/> ";
		if ($esq) 
			return $tt.$txt;
		else
			return $txt.$tt;
	} else  
		if ($valor == "on") 
			return $txt;
}

function getCBoxBool ($nom, $m, $valor, $txt, $esq = 0) {
	return getCBox01 ($nom, $m, $valor, $txt, $esq);
}
#
# innecessaria function getCBoxBool (nom,m,valor,txt,esq)
#
function getCBox01 ($nom, $m, $valor, $txt, $esq = 0) {
	$tt = "";
	if ($m) {
		$tt = " <input name='$nom' type='checkbox' ";
 		if ($valor) 
			$tt .= "checked";
		$tt .= "/> ";
		if ($esq) 
			return $tt.$txt;
		else
			return $txt.$tt;
	} else {
		if ($valor and $txt)
			return $txt;
		if (!$txt)
			return $valor;
	}
}

function getRBut ($nom, $m, $valor, &$dicc, $vert = 0) {
	$tt = "";
	if ($m) {
		foreach ($dicc as $k => $v) {
			$tt .= "<input type='radio' name='$nom' value='$k' " ;
			if ($valor == $k)
				$tt .= " checked ";
			$tt .= "> $v";
			if ($vert)
				$tt .= "<br>";
		}
	} else
		$tt = $dicc[$valor];
	return $tt;
}

function get1RBut ($nom,$m,$valor,$k) {
	$tt = "";
	if ($m) {
		$tt .= "<input type='radio' name='$nom' value='$k' ";
		if ($valor==$k)
			$tt.= " checked ";
		$tt .= "> ";
	} else
		$tt = $valor;
	return $tt;
}

function getSiNo ($nom, $m, $valor, $vert = 0) {
	$dicc['on']=$_SESSION[config]['si'];
	$dicc['off']=$_SESSION[config]['no'];
	return getRBut ($nom, $m, $valor, $dicc, $vert);
}

function guardaTempDades (&$f, $nom) {
	$_SESSION[data][$nom] = $f;
}

function recTempDades ($nom, $del = 0) {
	if ($_SESSION[data][$nom]) 
		$f = $_SESSION[data][$nom];
	else 
		$f = 0;
	if ($del)
		unset ($_SESSION[data][$nom]);
	return $f;
}
#===========================================================
function getFormTemplate ($templTxt,$formData,$m) {
	$fields=preg_split('/(\[|\])/',$templTxt,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach (array_keys($fields) as $k) {
		if (preg_match('/#/',$fields[$k])) {
			$fields[$k-1]='';
			$fields[$k+1]='';
			$k1=str_replace('#','',$fields[$k]);
			$ff = split (',',$k1);
# filtro para comas en instrucciones javascript
			$ff[0] = str_replace(";",",",$ff[0]);
			switch ($ff[1]) {
				case 'D':
					$fields[$k]=getInput($ff[0],$m,$formData[$ff[0]],$ff[2]). "(aaaammdd)";
					break;
				case 'F':
				case 'E':
				case 'T':
					$fields[$k]=getInput($ff[0],$m,$formData[$ff[0]],$ff[2]);
					break;
				case 'S':
					loadTablaAuxiliar($ff[2]);
					$fields[$k]=getSelect($ff[0],$m,$GLOBALS[$ff[2]],$formData[$ff[0]],0,0,' ');
					break;
				case 'R':
					loadTablaAuxiliar($ff[2]);
					$fields[$k]=getRBut($ff[0],$m,$formData[$ff[0]],$GLOBALS[$ff[2]],0);
					break;
				case 'R1':
					$fields[$k]=getR1But($ff[0],$m,$ff[2],$formData[$ff[0]]);
					break;
				case 'C':
					$fields[$k]=getCBox01($ff[0],$m,$formData[$ff[0]],'',0);
					break;
				case 'L':
					$fields[$k]='';
					break;
				case 'L1':
					$fields[$k]=$ff[3];
					break;
			}
		}
		
	}
	return join ("",$fields);
}

?>
