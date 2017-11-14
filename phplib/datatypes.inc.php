<?php

function getFiles_DataTypes($fn) {

	$fdt = $GLOBALS['filesMetaCol']->find(array('_id' => array('$in' => $fn)), array("_id" => false, "data_type" => true));

	$a = array();

	foreach($fdt as $v) $a[] = $v["data_type"];	

	return $a;

}


function getTools_DataTypes() {

	$dt = $GLOBALS['toolsCol']->find(array("external" => true), array("input_files_combinations_internal" => true));

	$array = array();

	$c = 0;

	foreach($dt as $tool) {

		foreach($tool["input_files_combinations_internal"] as $combination) {

			$array[$c]["id"] = $tool["_id"];
			$array[$c]["list1"] = array();
			$array[$c]["list2"] = array();
			
			foreach($combination as $single_c) {

				foreach($single_c as $k => $v) {

					if($v == 1) $array[$c]["list1"][] = $k;

					if($v == "+") {
						$array[$c]["list1"][] = $k;
						$array[$c]["list2"][] = $k;
					}

					if($v == "*") $array[$c]["list2"][] = $k;

				}

			}

			$c ++;

		}

	}

	return $array;

}

function getAvailableDTbyTool($tool) {

	$dt = $GLOBALS['toolsCol']->find(array("external" => true, "_id" => $tool), array("input_files_combinations_internal" => true));

	$array = array();

	$c = 0;

	foreach($dt as $tool) {

		foreach($tool["input_files_combinations_internal"] as $combination) {

			$array[$c]["id"] = $tool["_id"];

			foreach($combination as $single_c) {

				foreach($single_c as $k => $v) {

					$array[$c]["list"][] = $k;

				}

			}

		}

	}

	$array[0]["list"] = array_unique($array[0]["list"]);

	$array = $array[0];

	return $array;

}

function getTools_ByDT($toolsDT, $filesDT) {
	
	/*var_dump($filesDT);
	var_dump("**************", "CUT OFF", "**************");
	$c = 1;*/

	$toolsList = array();

	foreach($toolsDT as $tdt) {
		
		/*var_dump("iteration ".$c);
		$c ++;*/

		if(sizeof($tdt["list1"]) <= sizeof($filesDT)) {

			$list1 = $tdt["list1"];
			$list2 = $tdt["list2"];
			$listF = $filesDT;

			foreach($listF as $itemWS) {

				//var_dump($itemWS);

				if(in_array($itemWS, $list1)) {

					$key = array_search($itemWS, $list1);
					unset($list1[$key]);

					$key = array_search($itemWS, $listF);
					unset($listF[$key]);

				} else if(in_array($itemWS, $list2)) {

					$key = array_search($itemWS, $list2);
					unset($list2[$key]);

					$key = array_search($itemWS, $listF);
					unset($listF[$key]);

				} else {
				
					break;

				}

			}
			
			/*var_dump($tdt["id"]);
			var_dump($list1);
			var_dump($list2);
			var_dump($listF);
			if((sizeof($list1) == 0) && (sizeof($listF) == 0)) var_dump("MATCHING!!");*/
				//var_dump(sizeof($listF));

			if((sizeof($list1) == 0) && (sizeof($listF) == 0)) $toolsList[] = $tdt["id"];

		}

	}

	$toolsList = array_unique($toolsList);

	return $toolsList;

}

function getTools_ListByID($array, $status) {

	$tl = $GLOBALS['toolsCol']->find(array('_id' => array('$in' => $array), 'status' => $status), array("name" => true));

	return iterator_to_array($tl, false);

}

function getTools_Help() {

	$dt = $GLOBALS['toolsCol']->find(array("external" => true), array("input_files_combinations_internal" => true));

	$array = array();

	$c = 0;

	foreach($dt as $tool) {
        if (!isset($tool["input_files_combinations_internal"])){
            $_SESSION['errorData']['Error'][]="TOOL ".$tool['_id']." no internal comb";
            next;
        }
		foreach($tool["input_files_combinations_internal"] as $combination) {

			$array[$c]["id"] = $tool["_id"];
			$array[$c]["datatypes"] = array();
			
			foreach($combination as $single_c) {

				foreach($single_c as $k => $v) {

					$n = $GLOBALS['dataTypesCol']->find(array("_id" => $k), array("name" => true));
					$n  = iterator_to_array($n, true);

					if($v == 1) $array[$c]["datatypes"][] = "<strong>".$n[$k]["name"]."</strong> - one (mandatory)";

					if($v == "+") $array[$c]["datatypes"][] = "<strong>".$n[$k]["name"]."</strong> - at least one (mandatory)";

					if($v == "*") $array[$c]["datatypes"][] = "<strong>".$n[$k]["name"]."</strong> - multiple allowed (optional)";

				}

			}

			$c ++;

		}
	
	}

	return $array;

}

