<?php
/* NAFlex
 * globalvars.inc.php
 * Global Vars
 */

# BLAST data
//define ("BLAST", "/mmb/homes/soft/blast/bin/blastall");
//define ("BLAST", "/mmb/homes/soft/BLAST64/blast-2.2.9/bin/blastall");
define ("BLAST", "/opt/blast-2.2.9/bin/blastall");
//define ("BLASTDB", "/home/jboss/html/MDWeb/soft/blast/pdb");   //"home" instead of "home.local" if running on mmb2
define ("BLASTDB", "/var/www/NAFlex2/soft/blast/pdb");   //"home" instead of "home.local" if running on mmb2
//define ("BLASTDB", "soft/blast/pdb");   //"home" instead of "home.local" if running on mmb2
define ("BLASTMAXEVALUE", 0.00001);

# other useful tools
//define ("VMDDIR", "/usr/local/vmd/vmd-1.8.6");
define ("VMDDIR", "/opt/vmd/vmd-1.8.6");
//define ("VMDBIN", "/usr/local/bin/vmd");
define ("VMDBIN", "/usr/local/bin/vmd");
define ("STRIDEBIN", "/usr/local/bin/stride");

# SGE data
define ("QDEL", "/usr/local/sge/bin/lx24-amd64/qdel");
define ("SGE_ROOT","/usr/local/sge");

//CF security
if (CRYPT_MD5) define ("PASSWORD_SALT", "$1$kJunqdRT$");
else define("PASSWORD_SALT", "mR"); /* Arbitrary */

//$GLOBALS['appDir'] = "/home/jboss/html/MDWeb/";
//$GLOBALS['appDir'] = "/var/www/MDWeb/";
$GLOBALS['webDir'] = "/var/www/html/tools/naflex/";
//$GLOBALS['webDir'] = "";
$GLOBALS['appDir'] = "/mmb/MDWeb-Data/Web";
$GLOBALS['codiAdmin'] = "admin";
$GLOBALS['baseDir'] =$GLOBALS['appDir']."/userData";
#$GLOBALS['parmbsc1Dir'] ="NAFlex-Data/NA_Analysis/scripts";
$GLOBALS['parmbsc1Dir'] ="NAFlex-Data/NAFlex_parmBSC1";
$GLOBALS['pdbDir'] =$GLOBALS['webDir']."/mirror/data/structures/all/pdb";   //mmb2
//$GLOBALS['pdbDir'] = "/home.local/jboss/html/mirror/data/structures/all/pdb";   //pc64
$GLOBALS['softDir'] = $GLOBALS['webDir']."/soft";
$GLOBALS['projectDumpDir'] = $GLOBALS['appDir']."/userData/dump";
$GLOBALS['interactiveDemo'] = $GLOBALS['webDir']."/intDemo";


//$GLOBALS['tmpDir'] = "/mmb/data/MDWeb/MDWebTemp";
//$GLOBALS['tmpDir'] = "/usr/tmp";
//$GLOBALS['tmpDir'] = "/var/www/MDWeb/temp";
//$GLOBALS['tmpDir'] = "/mmb/MDWeb-Data/MDWeb_log";
$GLOBALS['tmpDir'] = "/tmp";
//$GLOBALS['asyncDir'] = "/mmb/data/MDWeb/WebServicesPersistence";
//$GLOBALS['servicesTmpDir'] = "/mmb/data/MDWeb";
$GLOBALS['asyncDir'] = "/mmb/MDWeb-Data/WebServices/WebServicesPersistence";
$GLOBALS['servicesTmpDir'] = "/mmb/MDWeb-Data/WebServices";
$GLOBALS['nucTmpDir'] = "/mmb/MDWeb-Data/NucServices";
$GLOBALS['persTrajDir'] = "/mmb/MDWeb-Data/WebServices/MDWebTrajInput";

# FlexServ shared Directory, is the only nfs exported folder of the entire project.
$GLOBALS['flexServDir'] = "/mmb/FlexServ";	

//$GLOBALS['tmpDir'] = "/usr/tmp";
//$GLOBALS['asyncDir'] = "/usr/tmp";

$GLOBALS['scriptDir'] =  $GLOBALS['webDir']."/scripts";
//CF to fix a wrong DNS resolution
//$dir_name = dirname($_SERVER['PHP_SELF']);
$dir_name = "NAFlex2";
//$GLOBALS['homeURL'] = "http://mmb.pcb.ub.es/orozco11/$dir_name/";
//$GLOBALS['homeURL'] = "http://inb238.mmb.pcb.ub.es/$dir_name/";
//$GLOBALS['homeURL'] = "http://mmb.pcb.ub.es/".$dir_name."-inb238/";
//$GLOBALS['homeURL'] = "http://mmb3.mmb.pcb.ub.es/$dir_name/";
//$GLOBALS['homeURL'] = "http://mmb.pcb.ub.es/mmb3-dev/$dir_name/";
$GLOBALS['homeURL'] = "http://mmb.irbbarcelona.org/$dir_name/";
$GLOBALS['mmbURL'] = "http://mmb.irbbarcelona.org/";

$GLOBALS['projectDumpURL'] = $GLOBALS['homeURL']."userData/dump";
$GLOBALS['logFile'] = $GLOBALS['tmpDir']."/mdweb.log";
$GLOBALS['caduca'] = "40"; //days
$GLOBALS['disklimit'] = "2000000"; // 2GB per user (2000000 KB)
#$GLOBALS['disklimit'] = "5000000"; // 3GB per user (3000000 KB)
$GLOBALS['maxAtoms'] = "20000"; // 20000 Atoms x PDB (limit titrate program 40000 ats: 20000 ats + hyd =~ 40000 ats)
$GLOBALS['limitFileSize'] = "100000"; // 100MB x Input File (Trajectory files mostly)
$GLOBALS['maxTrajSize'] = "50"; // 50MB x Trajectory (JMol limit)
$GLOBALS['database'] = "MDWeb";


$GLOBALS['maxResCG'] = 1000; // Maximum number of residues for Coarse-Grained dynamics algorithms: 1000 residues (Calpha carbons).
$GLOBALS['MaxSeqLength'] = 500; // Maximum number of bases in sequence input.
$GLOBALS['MaxCGSeqLength'] = 2500; // Maximum number of bases in Coarse-Grained sequence input.
$GLOBALS['MaxWLCBeads'] = 100; // Maximum number of beads in Coarse-Grained WLC Resolution.

$GLOBALS['toolbox'] =

Array (
'PDB-Text' => Array('newop', 'simulop', 'rasmol', 'jmol', 'downl', 'trash'),
'NAMD_MD_Structure' => Array('newop', 'simulop', 'rasmol', 'jmol', 'readLog', 'downl', 'trash'),
'AMBER_MD_Structure' => Array('newop', 'simulop', 'rasmol', 'jmol', 'readLog', 'downl', 'trash'),
'GROMACS_MD_Structure' => Array('newop', 'simulop', 'rasmol', 'jmol', 'readLog', 'downl', 'trash'),
'NAMD_Output' => Array('newop', 'simulop', 'rasmol', 'jmol', 'readLog', 'downl', 'readlog', 'trash'),
'MD_Trajectory' => Array('rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'MD_Trajectory_anal' => Array('rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'MD_TrajectoryCRD' => Array('simulop', 'analop', 'rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'MD_TrajectoryCRD_anal' => Array('analop', 'rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'MD_TrajectoryBINPOS' => Array('simulop', 'analop', 'rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'MD_TrajectoryBINPOS_anal' => Array('analop', 'rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'MD_TrajectoryDCD' => Array('simulop', 'analop', 'rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'MD_TrajectoryDCD_anal' => Array('analop', 'rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'MD_TrajectoryNetCDF' => Array('simulop', 'analop', 'rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'MD_TrajectoryNetCDF_anal' => Array('analop', 'rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'MD_TrajectoryXTC' => Array('simulop', 'analop', 'rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'MD_TrajectoryXTC_anal' => Array('analop', 'rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'PDB_Collection' => Array('rasmol', 'jmolTraj', 'readLog','downl', 'trash'),
'MD_Compressed_Trajectory' => Array('analop', 'rasmol', 'jmolTraj', 'readLog', 'downl', 'trash'),
'MD_Compressed_Trajectory_anal' => Array('analop', 'rasmol', 'jmolTraj', 'readLog', 'downl', 'trash'),
'FeatureAASequence' => Array('view','downl','trash'),
'ArrayFloat' => Array('view','downl','trash'),
'DNA' => Array('view','readLog','downl','trash'),
'NAMD_Conf_Text' => Array('trash'),
'Config' => Array('downl','trash'),
'ABC' => Array('abc', 'jmol', 'readLog', 'downl', 'trash'),
'ABC_res' => Array('jmol', 'readLog', 'downl', 'trash'),
'' => Array('trash')
);
$GLOBALS['projTypes'] = Array("sim" => "Simulation (Single structure)", "anal" => "Analysis (MD Trajectory)", "upload" => "Upload past NAFlex project", "seq" => "DNA/RNA Simulation From Sequence");
$GLOBALS['projTypes_ABC'] = Array("seq" => "DNA/RNA Simulation From Sequence", "sim" => "Simulation (Single structure)");
$GLOBALS['ffFormats'] = Array("namd" => "NAMD/AMBER","gromacs"=>"GROMACS");
$GLOBALS['trajFormats'] = Array("crd" => "CRD","dcd"=>"DCD","binpos"=>"BINPOS","netcdf"=>"NetCDF","xtc"=>"XTC","pcazip"=>"PCAZip");
$GLOBALS['topFormats'] = Array("psf" => "NAMD PSF","amber" => "PDB or PRMTOP", "gromacs"=>"GROMACS TOP");

$GLOBALS['nucTypes'] = Array( "abdna" => "Right Handed B-DNA (Arnott)", "lbdna" => "Right Handed B-DNA (Langridge)", "sbdna" => "Left Handed B-DNA (Sasisekharan)", "adna" => "Right Handed A-DNA (Arnott)", "arna" => "Right Handed A-RNA (Arnott)", "aprna" => "Right Handed A'-RNA (Arnott)", "DNAuser" => "User Defined DNA", "RNAuser" => "User Defined RNA", "ABC_DNA" => "DNA generated using ABC Average Helical Parameters Values", "X-ray_DNA" => "DNA generated using Experimental X-ray Average Helical Parameters Values", "DNAlive" => "Coarse-Grained DNA Elastic Mesoscopic Model (Base level)", "WLC" => "Coarse-Grained DNA Worm-like Chain Model");

$GLOBALS['nucTypesABC'] = Array( "abdna" => "Right Handed B-DNA (Arnott)", "lbdna" => "Right Handed B-DNA (Langridge)", "sbdna" => "Left Handed B-DNA (Sasisekharan)", "adna" => "Right Handed A-DNA (Arnott)", "arna" => "Right Handed A-RNA (Arnott)", "aprna" => "Right Handed A'-RNA (Arnott)", "DNAuser" => "User Defined DNA", "RNAuser" => "User Defined RNA", "ABC_DNA" => "DNA generated using ABC Average Helical Parameters Values", "X-ray_DNA" => "DNA generated using Experimental X-ray Average Helical Parameters Values");

$GLOBALS['gmxff_old'] = Array(
0 => "G43a1",			//#  0: GROMOS96 43a1 force field 
1 => "G43a2",			//#  1: GROMOS96 43a2 force field (improved alkane dihedrals)
2 => "G45a3",			//#  2: GROMOS96 45a3 force field (Schuler JCC 2001 22 1205)
3 => "G53a5", 		//#  3: GROMOS96 53a5 force field (JCC 2004 vol 25 pag 1656) 
4 => "G53a6", 		//#  4: GROMOS96 53a6 force field (JCC 2004 vol 25 pag 1656) 
5 => "oplsaa", 		//#  5: OPLS-AA/L all-atom force field (2001 aminoacid dihedrals)
6 => "gmx", 			//#  6: [DEPRECATED] Gromacs force field (see manual)
7 => "gmx2", 			//#  7: [DEPRECATED] Gromacs force field with hydrogens for NMR
8 => "encads", 		//#  8: Encad all-atom force field, using scaled-down vacuum charges
9 => "encadv", 		//#  9: Encad all-atom force field, using full solvent charges    
10 => "amber94", 		//# 10: AMBER-94 force field
11 => "amber96", 		//# 11: AMBER-96 force field
12 => "amber99",		//# 12: AMBER-99 force field
13 => "amber03",		//# 13: AMBER-03 force field (all-atom only, does not include the AMBER-03ua united-atom potential)
14 => "amberGS",		//# 14: AMBER-GS force field
15 => "amberGSs",		//# 15: AMBER-GS-S force field (no 1-4 vdW scaling)
16 => "amber99p",		//# 16: AMBER-99f force field
17 => "amber99sb", 		//# 17: AMBER-99SB force field
18 => "bsc0",  		//# 18: AMBER-BSC0 force field
19 => "amber99sbstar",	//# 19: AMBER-99SB* force field
20 => "amber03star"		//# 20: AMBER-03* force field
);

$GLOBALS['gmxff'] = Array(
1 => "AMBER03", 		#  1: AMBER03 protein, nucleic AMBER94 (Duan et al., J. Comp. Chem. 24, 1999-2012, 2003)
2 => "AMBER94", 		#  2: AMBER94 force field (Cornell et al., JACS 117, 5179-5197, 1995)
3 => "AMBER96", 		#  3: AMBER96 protein, nucleic AMBER94 (Kollman et al., Acc. Chem. Res. 29, 461-469, 1996)
4 => "AMBER99", 		#  4: AMBER99 protein, nucleic AMBER94 (Wang et al., J. Comp. Chem. 21, 1049-1074, 2000)
5 => "AMBER99SB", 		#  5: AMBER99SB protein, nucleic AMBER94 (Hornak et al., Proteins 65, 712-725, 2006)
6 => "AMBER99SB-ILDN",		#  6: AMBER99SB-ILDN protein, nucleic AMBER94 (Lindorff-Larsen et al., Proteins 78, 1950-58, 2010)
7 => "AMBERGS", 		#  7: AMBERGS force field (Garcia & Sanbonmatsu, PNAS 99, 2782-2787, 2002)
8 => "CHARMM27", 		#  8: CHARMM27 all-atom force field (with CMAP) - version 2.0
9 => "CHARMM36", 		#  9: CHARMM36 all-atom lipid force field (with CMAP)
10 => "GROMOS96 43a1",		#  10: GROMOS96 43a1 force field
11 =>  "GROMOS96 43a2",       	#  11: GROMOS96 43a2 force field (improved alkane dihedrals)
12 =>  "GROMOS96 45a3",	        #  12: GROMOS96 45a3 force field (Schuler JCC 2001 22 1205)
13 =>  "GROMOS96 53a5",       	#  13: GROMOS96 53a5 force field (JCC 2004 vol 25 pag 1656)
14 =>  "GROMOS96 53a6",       	#  14: GROMOS96 53a6 force field (JCC 2004 vol 25 pag 1656)
15 =>  "OPLS-AA/L",        	#  15: OPLS-AA/L all-atom force field (2001 aminoacid dihedrals)
16 =>  "PARMBSC0",		#  16: PARMBSC0 mod. of parm99 force field (Perez et al., Biophys. J. 92, 2007 )
17 =>  "Encad-AA Full Solvent", #  17: [DEPRECATED] Encad all-atom force field, using full solvent charges
18 =>  "Encad-AA SD Vacuum",	#  18: [DEPRECATED] Encad all-atom force field, using scaled-down vacuum charges
19 =>  "Gromacs FF",        	#  19: [DEPRECATED] Gromacs force field (see manual)
20 =>  "Gromacs FF NMR"        	#  20: [DEPRECATED] Gromacs force field with hydrogens for NMR
);

# GROMACS Water Type (new Gromacs4.5)
$GLOBALS['gmxWat'] = Array(
1 => "tip3p", 		# 1: TIP3P     TIP 3-point, recommended
2 => "tips3p",		# 2: TIPS3P    CHARMM TIP 3-point with LJ on H's (note: twice as slow in GROMACS)
3 => "tip4p", 		# 3: TIP4P     TIP 4-point
4 => "tip4p-Ew",        # 4: TIP4P-Ew  TIP 4-point optimized with Ewald
5 => "tip5p", 		# 5: TIP5P     TIP 5-point 
6 => "spc", 		# 6: SPC       simple point charge
7 => "spce",		# 7: SPC/E     extended simple point charge
8 => "f3c",             # 8: F3C       flexible three-centered water model
9 => "none" 		# 9: None
);

$GLOBALS['residue_codes_std'] = Array( 
 'ALA' => 1,
 'ARG' => 1,
 'ASN' => 1,
 'ASP' => 1,
 'CYS' => 1,
 'GLN' => 1,
 'GLU' => 1,
 'GLY' => 1,
 'HIS' => 1,
 'ILE' => 1,
 'LEU' => 1,
 'LYS' => 1,
 'MET' => 1,
 'PHE' => 1,
 'PRO' => 1,
 'SER' => 1,
 'THR' => 1,
 'TRP' => 1,
 'TYR' => 1,
 'VAL' => 1
);

$GLOBALS['nucleic_codes_std'] = Array( 
	'A' => 1,
	'T' => 1,
	'C' => 1,
	'G' => 1,
	'U' => 1,
	'DA' => 1,
	'DT' => 1,
	'DC' => 1,
	'DG' => 1,
	'RU' => 1,
	'RC' => 1,
	'RA' => 1,
	'RG' => 1,
	'ADE' =>1,
	'THY' =>1,
	'CYT' => 1,
	'GUA' =>1,
	'URA' => 1
);

?>
