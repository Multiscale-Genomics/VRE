<?php

function getFiles_DataTypes($fn) {

	$fdt = $GLOBALS['filesMetaCol']->find(array('_id' => array('$in' => $fn)), array("_id" => false, "data_type" => true));

	$a = array();

	foreach($fdt as $v) $a[] = $v["data_type"];	

	return $a;

}


// aquí treure les múltiples llista1, llista2
// id = naflex
// llista1 = {}
// llista2 = {}
function getTools_DataTypes() {

	$dt = $GLOBALS['toolsCol']->find(array("external" => true), array("input_files_combinations_internal" => true));

	$array = array();

	foreach($dt as $tool) {

		/*$array[]["id"] = $tool["_id"];
		$array[]["list1"] = array();
		$array[]["list2"] = array();*/



		foreach($tool["input_files_combinations_internal"] as $combination) {
		
			foreach($combination as $single_c) {

				foreach($single_c as $k => $v) {

					if($v == 1) $array["list1"][] = $k;

					if($v == "+") {
						$array["list1"][] = $k;
						$array["list2"][] = $k;
					}

					if($v == "*") $array["list2"][] = $k;

					//echo $k." - ".$v;

				}

			}

		}

	}

	var_dump($array);

}

function getMinDT_Files($dt) {

		

}

// aquí hauria de fer el total de combinacions possibles de totes les tools (TENINT EN COMPTE * i +!!!!) i només treure els que 
// siguin = $numfiles
// NO SÉ COM FER QUE NAFLEX TINGUI 3 O 4+ POSSIBLES INPUTS :_______(

/*function getNumDT_Tools($datatype, $numfiles) {

	$a = array();

	// foreach datatype array 
	foreach($datatype as $dt) {

		$a[$dt["_id"]] = array();

		// foreach combination
		foreach($dt["input_files_combinations_internal"] as $ifci) {

			var_dump($ifci);

			$c = 0;

			foreach($ifci as $i) {

				foreach($i as $k => $v) {

					//if($v != "*") $c ++;
					$c ++;

				}

			}

			if($c == $numfiles) $a[$dt["_id"]][] = $c;

		}

	}

	foreach($a as $k => $v) if(empty($v)) unset($a[$k]);

	//var_dump($a);

	return $a;

}*/
