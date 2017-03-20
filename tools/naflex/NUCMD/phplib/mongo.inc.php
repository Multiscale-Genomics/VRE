<?php

function printGSFile($col, $fn, $mime = '', $sendFn = False) {
    $file = $col->findOne(array('filename' => $fn));
    if (!$file->file['_id'])
        return 1;
    if ($mime)
        header('Content-type: ' . $mime);
    if ($sendFn)
        header('Content-Disposition: attachment; filename="' . $fn . '"');
    print($file->getBytes());
    return 0;
}

function getGSFile($col, $fn) {
    $file = $col->findOne(array('filename' => $fn));
    if (!$file->file['_id'])
        return '';
    else
        return $file->getBytes();
}

function getMongoStats($id,$trajs,$type="base"){

        $regexp = buildRegExp("_id.idGroup",$id,$type);

        $final = $regexp;
        if(!empty($trajs)){
                $in = array('_id.idSim' => array('$in' => $trajs));
                $regexp2[] = $regexp;
                $regexp2[] = $in;
                $final = array('$and' => $regexp2);
        }

        #print "<pre>";
        #print json_encode($final);
        #print "</pre>";
        #debug_to_console($final);

        return ($GLOBALS['groupDef']->count($final));
        #return ($GLOBALS['analData']->count($final));

}

/**
 * Send debug code to the Javascript console
 */
function debug_to_console($data) {
    if(is_array($data) || is_object($data))
        {
                echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
        } else {
                echo("<script>console.log('PHP: ".$data."');</script>");
        }
}

function buildRegExp($query,$id,$type="base")
{

        if($id == 'R'){
                $reg = new MongoRegex("/^[GA]$/");
                $regexp = array("$query" => $reg);
        }
        else if($id == 'Y'){
                $reg = new MongoRegex("/^[CT]$/");
                $regexp = array("$query" => $reg);
        }
        else if($id == 'N'){
                $reg = new MongoRegex("/^[ACGT]$/");
                $regexp = array("$query" => $reg);
        }
        else if($id == 'YR' and $type != "step"){
                $r1 = buildRegExp($query,"TA");
                $r2 = buildRegExp($query,"CG");
                $regexp = array( '$or' => array($r1,$r2));
        }
        else if($id == 'RY' and $type != "step"){
                $r1 = buildRegExp($query,"GC");
                $r2 = buildRegExp($query,"AT");
                $regexp = array( '$or' => array($r1,$r2));
        }
        else if($id == 'NN' and $type != "step"){
                $r1 = buildRegExp($query,"GC");
                $r2 = buildRegExp($query,"AT");
                $r3 = buildRegExp($query,"TA");
                $r4 = buildRegExp($query,"CG");
                $regexp = array( '$or' => array($r1,$r2,$r3,$r4));
        }
        else if($id == 'RY' and $type == "step"){
                $r1 = buildRegExp($query,"GC","step");
                $r2 = buildRegExp($query,"GT","step");
                $r3 = buildRegExp($query,"AC","step");
                $r4 = buildRegExp($query,"AT","step");
                $regexp = array( '$or' => array($r1,$r2,$r3,$r4));
        }
        else if($id == 'YR' and $type == "step"){
                $r1 = buildRegExp($query,"CG","step");
                $r2 = buildRegExp($query,"CA","step");
                $r3 = buildRegExp($query,"TG","step");
                $r4 = buildRegExp($query,"TA","step");
                $regexp = array( '$or' => array($r1,$r2,$r3,$r4));
        }
        else if($id == 'YY' and $type == "step"){
                $r1 = buildRegExp($query,"CC","step");
                $r2 = buildRegExp($query,"CT","step");
                $r3 = buildRegExp($query,"TT","step");
                $r4 = buildRegExp($query,"TC","step");
                $regexp = array( '$or' => array($r1,$r2,$r3,$r4));
        }
        else if($id == 'RR' and $type == "step"){
                $r1 = buildRegExp($query,"AA","step");
                $r2 = buildRegExp($query,"AG","step");
                $r3 = buildRegExp($query,"GG","step");
                $r4 = buildRegExp($query,"GA","step");
                $regexp = array( '$or' => array($r1,$r2,$r3,$r4));
        }
        else if($id == 'NN' and $type == "step"){
                $r1 = buildRegExp($query,"RY","step");
                $r2 = buildRegExp($query,"YR","step");
                $r3 = buildRegExp($query,"RR","step");
                $r4 = buildRegExp($query,"YY","step");
                $regexp = array( '$or' => array($r1,$r2,$r3,$r4));
        }
        else if(strlen($id) == 2 and $type != "step"){
                $arr = str_split($id);
                #$reg = new MongoRegex("/^$arr[0]$arr[1]$|^$arr[1]$arr[0]$/");
                $reg = new MongoRegex("/^[$arr[0]$arr[1]][$arr[0]$arr[1]]$/");
                $regexp = array("$query" => $reg);
        }
        else if(strlen($id) == 2 and $type == "step"){
                $string = $id."[ACGTUX][ACGTUX]";
                $reg = new MongoRegex("/^$string/");
                $regexp = array("$query" => $reg);
        }
        else{
                $reg = $id;
                $regexp = array($query => $reg);
        }
        #$in = array('_id.idSim' => array('$in' => array("NAFlex_1sk5","NAFlex_lks1")));
        #$regexp = array($query => $reg, '_id.idSim' => array('$in' => array("NAFlex_1sk5","NAFlex_lks1")));
         #$regexp2[] = $regexp;
         #$regexp2[] = $in;
         #$regexp3 = array('$and' => $regexp2);
         #return $regexp3;
        return $regexp;
}

?>
