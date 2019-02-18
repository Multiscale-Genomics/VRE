<?php

// TOOLS

function getFiles_DataTypes($fn) {

	$fdt = $GLOBALS['filesMetaCol']->find(array('_id' => array('$in' => $fn)), array("_id" => false, "data_type" => true));

	$a = array();

	foreach($fdt as $v) $a[] = $v["data_type"];	

	return $a;

}

function getDT_byFT($id) {

	$dt = $GLOBALS['toolsCol']->find(array("_id" => $id), array("input_files" => true));

	$arr_dt = [];

	foreach($dt as $tool) {

		foreach($tool["input_files"] as $t) {

			
			//if($t["required"]) $array["list1"] = $t["data_type"];


			$arr_dt = array_merge($arr_dt, $t["data_type"]);
//var_dump($arr_dt);
			/*var_dump($t["required"]);
			var_dump($t["allow_multiple"]);*/

		}

	}

	/*var_dump($id);
	var_dump($arr_dt);*/

	$arr_dt = array_unique($arr_dt);

	var_dump($arr_dt);

	$array = [];
	$array["id"] = $id;
	$array["list1"] = array();
	$array["list2"] = array();


	//var_dump($array);

}


function getTools_DataTypes() {

	$dt = $GLOBALS['toolsCol']->find(array("external" => true), array("input_files_combinations_internal" => true));

	$array = array();

	$c = 0;

	foreach($dt as $tool) {

		foreach($tool["input_files_combinations_internal"] as $combination) {

			// crear list 3 pels casos específics?????

			$array[$c]["id"] = $tool["_id"];
			$array[$c]["list1"] = array();
			$array[$c]["list2"] = array();
			$array[$c]["list3"] = array();

			foreach($combination as $single_c) {

				foreach($single_c as $k => $v) {

					// one item
					if($v == 1) $array[$c]["list1"][] = $k;

					// one or more (n mandatory)
					if($v == "+") {
						$array[$c]["list1"][] = $k;
						$array[$c]["list2"][] = $k;
					}

					// 0 or more (n non mandatory)
					if($v == "*") $array[$c]["list2"][] = $k;

					// special cases where no combinations internal are possible
					if($v == "-") $array[$c]["list3"][] = $k;

				}

			}

			$c ++;

		}

		/*if(empty($tool["input_files_combinations_internal"])) {
			$dts = getDT_byFT($tool["_id"]);
			//$array[]["id"] = $tool["_id"];
		}*/

	}

	//var_dump($array);

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

	// var_dump($toolsDT);
	// var_dump($filesDT);
	/*var_dump("**************", "CUT OFF", "**************");
	$c = 1;*/

	$toolsList = array();

	// foreach de totes les possible combinacions de tools
	foreach($toolsDT as $tdt) {
		
		/*var_dump("iteration ".$c);
		$c ++;*/

		// var_dump("tdt l1: ".sizeof($tdt["list1"]));
		// var_dump("filesDT: ".sizeof($filesDT));

		// només entrem si hi ha igual o més fitxers seleccionats que a list1 (obligatoris)
		if(sizeof($tdt["list1"]) <= sizeof($filesDT)) {

			$list1 = $tdt["list1"];
			$list2 = $tdt["list2"];
			$list3 = $tdt["list3"];
			$listF = $filesDT;

			//if($tdt["id"] == "naflex") var_dump($tdt["id"], $list1, $list2, $listF);

			// foreach de tots els fitxers seleccionats
			foreach($listF as $itemWS) {

				//var_dump($itemWS);

				// if item is in list1, unset
				if(in_array($itemWS, $list1)) {

					$key = array_search($itemWS, $list1);
					unset($list1[$key]);

					$key = array_search($itemWS, $listF);
					unset($listF[$key]);

				} /*else*/
				if(in_array($itemWS, $list2)) {
					// només que n'hi hagi un els esborrem tots???

					$key = array_search($itemWS, $list2);

					unset($list2[$key]);

					/*$key = array_search($itemWS, $listF);
					unset($listF[$key]);*/

					/*if (($key = array_search($itemWS, $listF)) !== false) {
						unset($listF[$key]);
					}*/

					$listF = array_diff($listF, [$itemWS]);

				} 
				if(in_array($itemWS, $list3)) {
					$toolsList[] = $tdt["id"];
				}
	
				/*else {
				
					break;

					}*/

				//if(!in_array($itemWS, $list1) && !in_array($itemWS, $list2)) break;

			}
			
			/*var_dump($tdt["id"]);
			var_dump($list1);
			var_dump($list2);
			var_dump($listF);
			if((sizeof($list1) == 0) && (sizeof($listF) == 0)) var_dump("MATCHING!!");*/
				//var_dump(sizeof($listF));

			// if no more items on both lists, it means matching!
			if((sizeof($list1) == 0) && (sizeof($list2) == 0) && (sizeof($listF) == 0)) $toolsList[] = $tdt["id"];

			//if($tdt["id"] == "naflex") var_dump($tdt["id"], $list1, $list2, $listF);

		}

	}

	$toolsList = array_unique($toolsList);

	return $toolsList;

}

function getTools_ListByID($array, $status) {

	$array = array_values($array);

	$tl = $GLOBALS['toolsCol']->find(array('_id' => array('$in' => $array), 'status' => array('$in' => [$status, 3])), array("name" => true, "status" => true));

	if($_SESSION['User']['Type'] == 1) {

		 $tools_list = iterator_to_array($tl, false);

		 foreach($tools_list as $key => $tool) {

			 if($tool["status"] == 3 && !in_array($tool["_id"], $_SESSION['User']["ToolsDev"])) {
				 unset($tools_list[$key]);
			 }

		 }

			return $tools_list;

	 } else {

		return iterator_to_array($tl, false);

	 }

}

function getSingleTool_Help($toolID, $op) {

	$tool = $GLOBALS['toolsCol']->findOne(array("external" => true, "_id" => $toolID), array("input_files_combinations" => true, "input_files" => true));

	$c = 0;

	if (!isset($tool["input_files_combinations"])){
		$_SESSION['errorData']['Error'][]="TOOL ".$tool['_id']." no internal comb";
		next;
	}

	foreach($tool["input_files_combinations"] as $combination) {

		if($c == $op) {

			$array[$c]["id"] = $tool["_id"];
			$array[$c]["operation"] = $combination["description"];
			$array[$c]["content"] = [];

			foreach($tool["input_files"] as $inputf) {

				if(in_array($inputf["name"], $combination["input_files"])) {
					//var_dump($inputf["description"]);
					$a = [];
					$a["description"] = $inputf["description"];

					if ($inputf["allow_multiple"] === true)	$a["description"] .= " <span title='Multiple files of this type accepted' style='color:#6d91b5;font-size:0.9em;'>(multiple)</span>";

					if ($inputf["required"] === true) $a["description"] .= " <span title='Mandatory file' style='color:#6d91b5;font-size:0.9em;'>(mandatory)</span>";
					else $a["description"] .= " <span title='Optional file' style='color:#6d91b5;font-size:0.9em;'>(optional)</span>";

					$a["format"] = $inputf["file_type"];

					$b = [];
					foreach($inputf["data_type"] as $dt) {

						$n = $GLOBALS['dataTypesCol']->find(array("_id" => $dt), array("name" => true));
						$n  = iterator_to_array($n, true);

						$b[] = $n[$dt]["name"];

					}

					$a["data_type"] = $b;

					$array[$c]["content"][] = $a;
				}

			}

		}

		$c ++;

	}

	return $array;

}

function getTools_Help() {

	$dt = $GLOBALS['toolsCol']->find(array("external" => true), array("input_files_combinations" => true, "input_files" => true));

	$array = array();

	$c = 0;

	foreach($dt as $tool) {

		if (!isset($tool["input_files_combinations"])){
			$_SESSION['errorData']['Error'][]="TOOL ".$tool['_id']." no internal comb";
			next;
		}

		foreach($tool["input_files_combinations"] as $combination) {

			$array[$c]["id"] = $tool["_id"];
			$array[$c]["operation"] = $combination["description"];
			$array[$c]["content"] = [];

			foreach($tool["input_files"] as $inputf) {

				if(in_array($inputf["name"], $combination["input_files"])) {
					//var_dump($inputf["description"]);
					$a = [];
					$a["description"] = $inputf["description"];

					if ($inputf["allow_multiple"] === true) $a["description"] .= " <span title='Multiple files of this type accepted' style='color:#6d91b5;font-size:0.9em;'>(multiple)</span>";

					if ($inputf["required"] === true) $a["description"] .= " <span title='Mandatory file' style='color:#6d91b5;font-size:0.9em;'>(mandatory)</span>";
					else $a["description"] .= " <span title='Optional file' style='color:#6d91b5;font-size:0.9em;'>(optional)</span>";

					$a["format"] = $inputf["file_type"];

					$b = [];
					foreach($inputf["data_type"] as $dt) {

						$n = $GLOBALS['dataTypesCol']->find(array("_id" => $dt), array("name" => true));
						$n  = iterator_to_array($n, true);

						$b[] = $n[$dt]["name"];

					}

					$a["data_type"] = $b;

					$array[$c]["content"][] = $a;
				}

			}

			$c ++;

		}

	}

	//var_dump($array);

	return $array;

}

// VISUALIZERS

function getFiles_FileTypes($fn) {

	$fdt = $GLOBALS['filesMetaCol']->find(array('_id' => array('$in' => $fn)), array("_id" => false, "format" => true));

	$a = array();

	foreach($fdt as $v) $a[] = $v["format"];	

	return $a;

}

function getVisualizers_FileTypes() {

	$dt = $GLOBALS['visualizersCol']->find(array("external" => true), array("accepted_file_types" => true));

	$array = array();

	foreach($dt as $viewer) {

		$array[] = $viewer;

	}

	return $array;

}

function getVisualizers_ByFT($visFT, $filesFT) {
	
	$visualizersList = array();

	foreach($visFT as $vft) {

		$accepted = false;

		if(count(array_intersect($filesFT, $vft["accepted_file_types"])) == count($filesFT)) {

			$visualizersList[] = $vft["_id"];

		}

	}

	return $visualizersList;

}

function getVisualizers_ListByID($array) {

	$tl = $GLOBALS['visualizersCol']->find(array('_id' => array('$in' => $array)), array("name" => true));

	return iterator_to_array($tl, false);

}
