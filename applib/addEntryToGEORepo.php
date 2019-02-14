<?php
/*
 *  Add new study into GEO repository
 *  from GEO Accession Number
 */

require "phplib/genlibraries.php";


$study_acc = "GSE96107";
print "GEO STUDY ACCESSION NUM.: $study_acc\n";


# Get GEO study UID

$url = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=gds&term=".$study_acc."[Accession]+AND+gse[Filter]&retmode=json";
list($result,$info) = get($url);
$result = json_decode($result,TRUE);

if (!isset($result['esearchresult']['idlist']) || count($result['esearchresult']['idlist']) == 0){
    print "Error: Accession number $study_acc returns no GEO study";
    var_dump($result);
    exit(1);
}

$study_uid = $result['esearchresult']['idlist'][0];
print "GEO STUDY UID: $study_uid\n";


# Get GEO study metadata

$url="https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=gds&version=2.0&id=".$study_uid."&retmode=json";
list($result,$info) = get($url);
$result = json_decode($result,TRUE);

if (!isset($result['result'][$study_uid])){
    print "Error: GEO study UID $study_uid (from Accession Num $study_acc) returns no study metadata";
    var_dump($result);
    exit(1);
}

$study_meta = $result['result'][$study_uid];
var_dump($study_meta);

