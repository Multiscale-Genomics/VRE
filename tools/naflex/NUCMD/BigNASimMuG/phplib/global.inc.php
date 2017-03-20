<?php

$wwwBaseDir = pathinfo($_SERVER['SCRIPT_FILENAME'],PATHINFO_DIRNAME);
$homeURL = '/BigNASimMuG/';
$phplib = "$wwwBaseDir/phplib";
$htmlib = "$wwwBaseDir/htmlib";
$plots2D = "$wwwBaseDir/rnaViewImages";
$idioma="en";
define ("tmpDir", "/tmp");

require_once "$phplib/libraries.inc.php";

$GLOBALS['homeMMB'] = "http://mmb.irbbarcelona.org/";
$GLOBALS['homePDB'] = "http://mmb.irbbarcelona.org/pdb/";
$GLOBALS['homeURL'] = "http://mmb.irbbarcelona.org/NAFlex2/";
$GLOBALS['NAFlex'] = "http://mmb.irbbarcelona.org/NAFlex2/";
$GLOBALS['homeNUCMD_FULL'] = "http://mmb.irbbarcelona.org/BigNASimMuG";
$GLOBALS['homeNUCMD'] = ".";
$GLOBALS['listAnalysis'] = array('CURVES','STIFFNESS','STACKING_avg','HBs_avg','NMR_NOE_avg','NMR_JC_avg');
$GLOBALS['parmbsc1Dir'] ="NAFlex-Data/NAFlex_parmBSC1";
$GLOBALS['scriptsDir'] ="NAFlex-Data/NA_Analysis/scripts";
$GLOBALS['RNAView'] ="rnaViewImages";

# Log file
$GLOBALS['logFile'] = "/tmp/BIGNASim.log";

# Temporary dirs/log file
$GLOBALS['tmpDir'] = "filesSessions"; 

# SGE data
define ("QSUB", "source /usr/local/sge/environment/settings.sh; /usr/local/sge/bin/lx24-amd64/qsub -S /bin/bash -cwd -q www-services-fast.q@parmbsc1-naflex");
define ("QDEL", "source /usr/local/sge/environment/settings.sh; /usr/local/sge/bin/lx24-amd64/qdel ");
define ("QSTAT", "source /usr/local/sge/environment/settings.sh; /usr/local/sge/bin/lx24-amd64/qstat ");
define ("SGE_ROOT","/usr/local/sge");

$GLOBALS['large_seq_cutoff'] = 30;	# Large Oligos: > 30 nucleotides

$GLOBALS['project'] = 'BINASim';
$GLOBALS['AppTitol']="BIGNASim, database structure and analysis portal for nucleic acids simulation data";
$GLOBALS['AppPrefix']="BNS";
$GLOBALS['idioma'] = "en";
$GLOBALS['caduca'] = "40"; //days
$GLOBALS['disklimit'] = 2*1024*1024*1024; // 2GB per user (2000000 KB)
$GLOBALS['limitFileSize'] = 100*1024*1024; // 100MB 
if (CRYPT_MD5) define ("PASSWORD_SALT", '$1$kJunqdRT$');
# Paises
$GLOBALS['paisesDicc']['XX']='---';
#foreach (array_values(iterator_to_array($GLOBALS['paisesCol']->find(array(),array('country'=>1))->sort(array('country'=>1)))) as $v)
#        $GLOBALS['paisesDicc'][$v['_id']] = $v['country'];


#$limitsList = array('100' => 100, '500' => 500, '1000' => 1000, '0' => 'All');
$limitsList = array('10' => 10, '20' => 20, '50' => 50, '100' => 100, '1000000' => 'All');

$mapProperties = array();
$mapProperties['stC'] = "Crick Strand Stacking";
$mapProperties['stW'] = "Watson Strand Stacking";
$mapProperties['CS'] = "Crick Strand Stacking";
$mapProperties['WS'] = "Watson Strand Stacking";
$mapProperties['HB'] = "Hydrogen Bond Stacking";
#$mapProperties['HBs'] = "Hydrogen Bonds";
$mapProperties['axis_bp'] = "Axis Base Pairs";
$mapProperties['helical_bp'] = "Helical Base Pairs";
$mapProperties['backbone_torsions'] = "Backbone Torsions";
$mapProperties['BI_population'] = "BI / BII Population";
$mapProperties['canonical_alpha_gamma'] = "Canonical Alpha / Gamma";
$mapProperties['puckering'] = "Sugar Puckering";
$mapProperties['xdisp_avg'] = "X-displacement AVG";
$mapProperties['ydisp_avg'] = "Y-displacement AVG";
$mapProperties['inclin_avg'] = "Inclination AVG";
$mapProperties['xdisp'] = "X-displacement";
$mapProperties['ydisp'] = "Y-displacement";
$mapProperties['inclin'] = "Inclination";
$mapProperties['majd'] = "Major Groove Depth";
$mapProperties['majw'] = "Major Groove Width";
$mapProperties['mind'] = "Minor Groove Depth";
$mapProperties['minw'] = "Minor Groove Width";
$mapProperties['majd'] = "Major Groove Depth";
$mapProperties['FORCE_CTES'] = "Stiffness Force Constants";
$mapProperties['buckle_avg'] = "Buckle AVG";
$mapProperties['opening_avg'] = "Opening AVG";
$mapProperties['propel_avg'] = "Propeller AVG";
$mapProperties['shear_avg'] = "Shear AVG";
$mapProperties['stagger_avg'] = "Stagger AVG";
$mapProperties['stretch_avg'] = "Stretch AVG";
$mapProperties['majd_avg'] = "Major Groove Depth AVG";
$mapProperties['majw_avg'] = "Major Groove Width AVG";
$mapProperties['mind_avg'] = "Minor Groove Depth AVG";
$mapProperties['minw_avg'] = "Minor Groove Width AVG";
$mapProperties['rise_avg'] = "Rise AVG";
$mapProperties['roll_avg'] = "Roll AVG";
$mapProperties['shift_avg'] = "Shift AVG";
$mapProperties['slide_avg'] = "Slide AVG";
$mapProperties['tilt_avg'] = "Tilt AVG";
$mapProperties['twist_avg'] = "Twist AVG";

#Simple Templates
$templates = array(
    'getStruc' => array(
        'biounitsTempl' => '
        <tr>
        <td>##bunit##</td>
        <td><a href="rest.php?idPDB=##idPDB##&bunit=##bunit##&gzip=1">PDB File</a></td>
        <td><a href="showStruc.php?idPDB=##idPDB##&bunit=##bunit##">3D View</a></td>
        </tr>
        ',
        'hetatmTempl' => '
        <tr>
        <td><a href="getMonomers.php?idCode=##idCode##&limit=100&idMon=##_id##">##_id##</a></td>
        <td><a href="getMonomers.php?idCode=##idCode##&limit=100&idMon=##_id##">##nom##</a></td>
        </tr>
        ',
        'uniproKbTempl' => '
        <p><a href="http://www.uniprot.org/uniprot/##_id##" target="_blank">##_id##</a> ##header## (<i>##sourceTxt##</i>)</p>
        ',
        'clusterLineTempl' => '
        <tr>
        <td><a href="getCluster.php?cluster=##cl##&chCode=##ref##">##cl##</a></td>
        <td><a href="getStruc.php?idCode=##refidCode##">##ref##</a></td>
        </tr>
        ',
        'swpMapTempl' => '
        <p><b>Best SwissProt Hit:</b>
        <a href="http://www.uniprot.org/uniprot/##idHit##" target="_blank">##idHit##</a><br/>(##Hit_def##)<br>
        <b>E-Value:</b> ##Hsp_evalue##</p>
        '
    ),
    'getCluster' => array(
        'clusterLineTempl' =>
        '
         <tr>
             <td><a href="getStruc.php?idCode=##_id##">##_id##</a></td>
             <td>##chainTxt##</td>
             <td>##header##</td>
             <td>##compound##</td>
             <td>##resol##</td>
             <td>##hetatmTxt##</td>
         </tr>
        '
    ),
    'searchList' => array(
        'headerTempl' => '
            <thead>
            <tr>
	    <th></th>
            <th>Id.</th>
	    <th>PDB</th>
	    <th>Type</th>
	    <th>SubType</th>
	    <th>ForceField</th>
	    <th>Solvent</th>
	    <th>Description</th>
            <th>Time (ns)</th>
            <th>Sequence</th>
            </tr>
	    <tr id="headerSearch">
            <th style="background-color: #eee; text-align:center;"><input type="checkbox" name="bmarkAll" onChange="checkMarkAll()"></th>
            <th style="background-color: #eee; text-align:center;" class="inputSearch">Id.</th>
	    <th style="background-color: #eee; text-align:center;" class="selector">PDB</th>
	    <th style="background-color: #eee; text-align:center;" class="selector">Type</th>
	    <th style="background-color: #eee; text-align:center;" class="selector">SubType</th>
	    <th style="background-color: #eee; text-align:center;" class="selector">ForceField</th>
	    <th style="background-color: #eee; text-align:center;" class="selector">Solvent</th>
	    <th style="background-color: #eee; text-align:center;" class="inputSearch">Description</th>
            <th style="background-color: #eee; text-align:center;" class="inputSearch">Time (ns)</th>
            <th style="background-color: #eee; text-align:center;" class="inputSearch">Sequence</th>
            </tr>
	    </thead>
            ',
        'dataTempl' => '
            <tr style="display: table-row;" id="##_id##">
            <td><input type="checkbox" name="##_id##"></td>
            <td><a target="_blank" href="getStruc.php?idCode=##_id##">##_id##</a></td>
            <td>##PDB##</td>
            <td>##moleculeType##</td>
            <td>##SubType##</td>
            <td>##forceField##</td>
            <td>##Water##</td>
            <td>##description##</td>
            <td>##time##</td>
            <td style="word-break: break-all; -ms-word-break: break-all; max-width: 300px;">##sequenceWeb##</td>
            </tr>'
    ),
    'searchList1' => array(
        'headerTempl1' => '
            <thead>
            <tr>
	    <th></th>
            <th>Date</th>
	    <th>Size</th>
	    <th>Project</th>
	    <th>File</th>
	    <th>Format</th>
	    <th>Accions</th>
            </tr>
	    <tr id="headerSearch">
            <th style="background-color: #eee; text-align:center;"><input type="checkbox" name="bmarkAll" onChange="checkMarkAll()"></th>
            <th style="background-color: #eee; text-align:center;"></th>
	    <th style="background-color: #eee; text-align:center;"></th>
	    <th style="background-color: #eee; text-align:center;" class="selector">Project</th>
	    <th style="background-color: #eee; text-align:center;" class="inputSearch">File</th>
	    <th style="background-color: #eee; text-align:center;" class="selector">Format</th>
	    <th style="background-color: #eee; text-align:center;"></th>
            </tr>
	    </thead>
            ',
        'dataTempl1' => '
            <tr style="display: table-row;" id="##_id##">
            <td><input type="checkbox" name="##_id##"></td>
            <td>##mtime##</td>
            <td>##size##</td>
            <td>##parentDir##</td>

            <td>##_id##
		<a href="javascript:toggleVis(\"description##_id##\");"><img src="BNSdatamanager/images/more.png" style="height:12px" title="description"></a>
		<table id="description##_id##" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
                <tr>
                    <td>Reference genome:</td>
                    <td>ggg</td>
                </tr>
                <tr>
                    <td>Description:</td>
                    <td>ggg</td>
                </tr>
              </table>
	    </td>
            <td>##Format##</td>
            <td><a target="_blank" href="nuclR.php?id=##_id##">nuclR</a>
                <a target="_blank" href="datamanager/workspace.php?op=delete&fn=##project##/##filename##">delete</a>
                <a target="_blank" href="datamanager/workspace.php?op=zip&fn=##project##/##filename##">zip</a>
	    </td>
            </tr>'
    )
);

        # Ontology Hash Table
        # Allows easy searching for simulations in MongoDB through ontology id codes.
        $ontoHash = array();

        # Ontology 1 --> Nucleic Acid Type
        $ontoHash['NAType'] = 1;
                $ontoHash['Dna'] = 101;
                $ontoHash['Rna'] = 102;
                $ontoHash['Hybrid'] = 103;
                $ontoHash['Dna'] = 101;
                $ontoHash['OtherOnto1'] = 199;

        # Ontology 2 --> Structure
        $ontoHash['Structure'] = 2;
                $ontoHash['SingleStrand'] = 201;
                        $ontoHash['Unpaired'] = 20101;
                        $ontoHash['Hairpin'] = 20102;
                $ontoHash['Duplex'] = 202;
                        $ontoHash['Canonical'] = 20201;
                                $ontoHash['Linear'] = 2020101;
                                $ontoHash['Circular'] = 2020102;
                        $ontoHash['Hoogsteen'] = 20202;
                        $ontoHash['UnpairedEnds'] = 20203;
                $ontoHash['Triplex'] = 203;
                        $ontoHash['Parallel'] = 20301;
                        $ontoHash['Antiparallel'] = 20302;
                $ontoHash['Quadruplex'] = 204;
                        $ontoHash['Gloop'] = 20401;
                        $ontoHash['Parallel'] = 20402;
                        $ontoHash['Antiparallel'] = 20403;
                        $ontoHash['I-DNA'] = 20404;
                $ontoHash['HollidayJunction'] = 205;
                $ontoHash['3-WayJunction'] = 206;
                $ontoHash['Loop'] = 207;
                $ontoHash['OtherOnto2'] = 299;

        # Ontology 3 --> System Type
        $ontoHash['SystemType'] = 3;
                $ontoHash['Naked'] = 301;
                $ontoHash['Complex'] = 302;
                        $ontoHash['ProteinNuc'] = 30201;
                        $ontoHash['LigandNuc'] = 30202;
                $ontoHash['OtherOnto3'] = 399;

        # Ontology 4 --> Trajectory Type
        $ontoHash['TrajectoryType'] = 4;
                $ontoHash['Equilibrium'] = 401;
                $ontoHash['Folding'] = 402;
                $ontoHash['Transition'] = 403;
                $ontoHash['OtherOnto4'] = 499;

        # Ontology 5 --> Original Helical Conformation
        $ontoHash['OriginalHelicalConformation'] = 5;
                $ontoHash['A'] = 501;
                $ontoHash['B'] = 502;
                $ontoHash['Z'] = 503;
                $ontoHash['Hoogsteen'] = 504;
                $ontoHash['Mixed'] = 505;
                $ontoHash['OtherOnto5'] = 599;

        # Ontology 6 --> Sequence Modifications
        $ontoHash['SequenceModifications'] = 6;
                $ontoHash['ModifiedNucleotides'] = 601;
                $ontoHash['CrossLinked'] = 602;
                $ontoHash['EpigeneticVariants'] = 603;
                $ontoHash['OtherOnto6'] = 699;

        # Ontology 7 --> Sequence Features
        $ontoHash['SequenceFeatures'] = 7;
                $ontoHash['PolyA'] = 701;
                        $ontoHash['BrokenPolyA'] = 70101;
                $ontoHash['PolyG'] = 702;
                $ontoHash['DDD'] = 703;
                $ontoHash['Mismatch'] = 704;
                $ontoHash['OtherOnto7'] = 799;

        # Ontology 8 --> Simulation Conditions (UMM)
        $ontoHash['SimulationConditions'] = 8;
                $ontoHash['ForceField'] = 801;
                        $ontoHash['parmbsc1'] = 80101;
                        $ontoHash['parmbsc0'] = 80102;
                        $ontoHash['parm99'] = 80103;
                        $ontoHash['OL1'] = 80104;
                        $ontoHash['OL4'] = 80105;
                        $ontoHash['OL1+OL4'] = 80106;
                        $ontoHash['Cheng-Garcia'] = 80107;
                        $ontoHash['Charmm36'] = 80108;
                $ontoHash['MDLength'] = 802;
                        $ontoHash['NanosecondRange'] = 80201;
                        $ontoHash['MicrosecondRange'] = 80202;
                $ontoHash['Temperature'] = 803;
                        $ontoHash['PhysiologicalTemperature'] = 80301;
                        $ontoHash['NonPhysiologicalTemperature'] = 80302;
                $ontoHash['WaterType'] = 804;
                        $ontoHash['TIP3P'] = 80401;
                        $ontoHash['SPCE'] = 80402;
                        $ontoHash['Ethanol'] = 80403;
                $ontoHash['Charge'] = 805;
                        $ontoHash['Electroneutrality'] = 80501;
                        $ontoHash['AddedSalt'] = 80502;
                        $ontoHash['AddedSaltPhys'] = 8050201;
                        $ontoHash['AddedSaltNonPhys'] = 8050202;
                $ontoHash['IonsParameters'] = 806;
                        $ontoHash['Dang'] = 80601;
                        $ontoHash['Cheatham'] = 80602;

        # Ontology Hash Table Reverse
        # Allows easy searching for simulations in MongoDB through ontology id codes.
        $ontoHashRev = array();

        # Ontology 1 --> Nucleic Acid Type
        $ontoHashRev['1'] = NAType;
                $ontoHashRev['101'] = 'Dna';
                $ontoHashRev['102'] = 'Rna';
                $ontoHashRev['103'] = 'Hybrid';
                $ontoHashRev['199'] = 'Other';

        # Ontology 2 --> Structure
        $ontoHashRev['2'] = 'Structure';
                $ontoHashRev['201'] = 'Single Strand';
                        $ontoHashRev['20101'] = 'Unpaired';
                        $ontoHashRev['20102'] = 'Hairpin';
                $ontoHashRev['202'] = 'Duplex';
                        $ontoHashRev['20201'] = 'Canonical';
                                $ontoHashRev['2020101'] = 'Linear';
                                $ontoHashRev['2020102'] = 'Circular';
                        $ontoHashRev['20202'] = 'Hoogsteen';
                        $ontoHashRev['20203'] = 'UnpairedEnds';
                $ontoHashRev['203'] = 'Triplex';
                        $ontoHashRev['20301'] = 'Parallel';
                        $ontoHashRev['20302'] = 'Antiparallel';
                $ontoHashRev['204'] = 'Quadruplex';
                        $ontoHashRev['20401'] = 'Gloop';
                        $ontoHashRev['20402'] = 'Parallel';
                        $ontoHashRev['20403'] = 'Antiparallel';
                        $ontoHashRev['20404'] = 'I-DNA';
                $ontoHashRev['205'] = 'HollidayJunction';
                $ontoHashRev['206'] = '3-WayJunction';
                $ontoHashRev['207'] = 'Loop';
                $ontoHashRev['299'] = 'Other';

        # Ontology 3 --> System Type
        $ontoHashRev['3'] = 'System Type';
                $ontoHashRev['301'] = 'Naked';
                $ontoHashRev['302'] = 'Complex';
                        $ontoHashRev['30201'] = 'Protein-Nuc';
                        $ontoHashRev['30202'] = 'Ligand-Nuc';
                $ontoHashRev['399'] = 'Other';

        # Ontology 4 --> Trajectory Type
        $ontoHashRev['4'] = 'Trajectory Type';
                $ontoHashRev['401'] = 'Equilibrium';
                $ontoHashRev['402'] = 'Un/Folding';
                $ontoHashRev['403'] = 'Transition';
                $ontoHashRev['499'] = 'Other';

        # Ontology 5 --> Original Helical Conformation
        $ontoHashRev['5'] = 5;
                $ontoHashRev['501'] = 'A';
                $ontoHashRev['502'] = 'B';
                $ontoHashRev['503'] = 'Z';
                $ontoHashRev['504'] = 'Hoogsteen';
                $ontoHashRev['505'] = 'Mixed';
                $ontoHashRev['599'] = 'Other';

        # Ontology 6 --> Sequence Modifications
        $ontoHashRev['6'] = 'SequenceModifications';
                $ontoHashRev['601'] = 'ModifiedNucleotides';
                $ontoHashRev['602']= 'CrossLinked';
                $ontoHashRev['603'] = 'EpigeneticVariants';
                $ontoHashRev['699'] = 'Other';

        # Ontology 7 --> Sequence Features
        $ontoHashRev['7'] = 'SequenceFeatures';
                $ontoHashRev['701'] = 'PolyA';
                        $ontoHashRev['70101'] = 'BrokenPolyA';
                $ontoHashRev['702'] = 'PolyG';
                $ontoHashRev['703'] = 'DDD';
                $ontoHashRev['704'] = 'Mismatch';
                $ontoHashRev['799'] = 'Other';

        # Ontology 8 --> Simulation Conditions (UMM)
        $ontoHashRev['8'] = 'SimulationConditions';
                $ontoHashRev['801'] = 'ForceField';
                        $ontoHashRev['80101'] = 'ParmBSC1';
                        $ontoHashRev['80102'] = 'ParmBSC0';
                        $ontoHashRev['80103'] = 'Parm99';
                        $ontoHashRev['80104'] = 'OL1';
                        $ontoHashRev['80105'] = 'OL4';
                        $ontoHashRev['80106'] = 'OL1+OL4';
                        $ontoHashRev['80107'] = 'Cheng-Garcia';
                        $ontoHashRev['80108'] = 'Charmm36';
                $ontoHashRev['802'] = 'MD Length';
                        $ontoHashRev['80201'] = 'Nanosecond Range';
                        $ontoHashRev['80202'] = 'Microsecond Range';
                $ontoHashRev['803'] = 'Temperature';
                        $ontoHashRev['80301'] = 'PhysiologicalTemperature';
                        $ontoHashRev['80302'] = 'NonPhysiologicalTemperature';
                $ontoHashRev['804'] = 'WaterType';
                        $ontoHashRev['80401'] = 'TIP3P';
                        $ontoHashRev['80402'] = 'SPCE';
                        $ontoHashRev['80403'] = 'Ethanol';
                $ontoHashRev['805'] = 'Charge';
                        $ontoHashRev['80501'] = 'Electroneutrality';
                        $ontoHashRev['80502'] = 'Added Salt';
                        $ontoHashRev['8050201'] = 'AddedSalt-Physiological concentration';
                        $ontoHashRev['8050202'] = 'AddedSalt-Non Physiological concentration';
                $ontoHashRev['806'] = 'IonsParameters';
                        $ontoHashRev['80601'] = 'Dang';
                        $ontoHashRev['80602'] = 'Cheatham';


	$ontoHash2 = Array();
	$ontoHashRev2 = Array();
	#$ontoMongo = $GLOBALS['ontoCol']->find(); 
	#foreach ($ontoMongo as $r) {
        #	$id = $r['_id'];
	#        $feature = $r['feature'];
        #	$ontoHash2[$feature] = $id;
        #	$ontoHashRev2[$id] = $feature;
	##echo "ontoHashRev2[$feature] = $id<br/>";
	#}


?>
