<?php

	$baseDir = getcwd();
	$softDir = "$baseDir/soft";

	define ("QSUB", "source /usr/local/sge/environment/settings.sh; /usr/local/sge/bin/lx24-amd64/qsub -S /bin/bash -cwd -q www.q ");
	define ("QDEL", "source /usr/local/sge/environment/settings.sh; /usr/local/sge/bin/lx24-amd64/qdel ");

	#Use of RAM Disk for trajectories
	define ("USERAMDISK", 1);
	define ("RAMDISKPATH", "/dev/shm");

	# Application location
	define ("APPLOCATION", "/FlexServ");
	define ("MDWEBDIR", "/mmb/data/MDWeb/MDWeb");
	
	# Path to binaries
	define ("PERLBIN", "/usr/bin/perl");
	define ("NACCESS", "$softDir/naccess/naccess");
	define ("VMDDIR", "/usr/local/vmd/vmd-1.8.6");
	define ("VMDBIN", "/usr/local/bin/vmd");
	define ("STRIDEBIN", "/usr/local/bin/stride");
	define ("PROCESSHINGES", "$softDir/perl/processHinges.pl");
	define ("STDPLOT", "$softDir/perl/stdPlot.pl");
	define ("PLOT", "$softDir/perl/plot.pl");
	define ("PLOTEVECS", "$softDir/perl/plotevecs.pl");
	define ("GENTOP", "$softDir/perl/genTop.pl");
	define ("CORRELATION", "$softDir/perl/correl.sh");
	define ("MATRIXPLOT", "$softDir/perl/matrixPlot.sh");
	define ("SENDMAIL", "$softDir/perl/sm.pl");
	define ("PROCMODRES", "$softDir/perl/processModRes.pl");

	#Modified res
	define ("MODRESLIST", "$softDir/perl/modResList.dat");

	# Input limits
	define ("MAXRESIDUES", 1400);
	define ("MAXSNAPSHOTS", 1000);
	
	# BLAST data
	define ("BLAST", "/mmb/homes/soft/blast/bin/blastall");
	define ("BLASTDB", "/home/jboss/html/FlexServ/soft/blast/pdb");
	define ("BLASTMAXEVALUE", 0.00001);
	
	# Default values
	define ("MINSEQUENCESEPARATION", 5);
	define ("MINDISTANCESEPARATION", 8.0);
	define ("DEFAULTACCURACY", 90);
	define ("ZSCORETHRESHOLD", 2);
	
	
	# Frequency of page refreshing when waiting for resultsn
	define ("REFRESHPERIOD", 20);

	# Define if we require a mail address to make the calculus
	define ("FORCEEMAIL", false);
	
	# Liquid/solid Lindemann coefficient threshold
	define ("LINDEMANNTHRESHOLD", 0.15);

	# Maximum simulation length
	define ("MAXBDSTEPS", 10000000); # 10Msteps
	define ("MAXBDTIME", 0.000000001); # 1ns
	define ("MAXBDFRAMES", 10000); # 10000frames
	
	# Maximum time the data is allowed to live on disk
	define ("MINUTE", 60);
	define ("HOUR", 60*MINUTE);
	define ("DAY", 24*HOUR);
	define ("WEEK", 7*DAY);
	define ("DAYSTOSTOREDATA", 7);
	define ("SESSIONEXPIRYTIME", DAYSTOSTOREDATA*DAY);

	# Number of modes that will be shown to the user
	define ("SHOWNMODES", 10);

	# Number of Residues Per Row while showing residue sequences
	define ("RPR", 50);

	# PDB data
	define ("PDBDIR", "/home/jboss/html/pdb/mirror/data/structures/all/pdb");
	
	# MoDEL data
	define ("MODELDIR", "/mmb/raid7/projects/MoDEL");
	define ("MODELANALYSISTYPE", "_AMBER8_P99-T3P_0");

	# Manu's code (NMA)
	define ("NMAHESSIAN", "$softDir/nma/nmanu.pl");
	define ("NMADIAGONALIZE", "$softDir/nma/diaghess/diaghess");
	define ("NMAPROJECT", "$softDir/nma/mc-eigen.pl");
	define ("NMATRAJECTORY", "$softDir/nma/pca_anim_mc.pl");
	define ("NMAFCTELINEAR", 10);
	define ("NMAFCTEKOVACS", 40);

	# Agusti's code (DMD)
	define ("DMDBIN", "$baseDir/soft/dmd/dmdgoopt");

	# Oliver's code (BD)
	define ("BDBIN", "$baseDir/soft/bd/bd");

	# PCAsuite binaries
	$pcasuiteDir="$softDir/pcasuite";
	define ("PCAZIP", "$pcasuiteDir/pcazip");
	define ("PCZDUMP", "$pcasuiteDir/pczdump");
	define ("PCAUNZIP", "$pcasuiteDir/pcaunzip");
	define ("GENPCZ", "$pcasuiteDir/genpcz");

	# RMSd types
	define ("STDRMS", 1);
	define ("GAUSSRMS", 2);

	# Mathematic and physic ctonstants
	define ("kB", 1.3806503e-23);
	define ("kA", 6.0221415e23);
	define ("facJCal", 1.0/4186.0);

	# TIM's MoDEL Ligand Library
	# define ("LIBLIG", "/mmb/raid7/projects/PREPARATION/LIBs/ligands");

	# MoDEL Ligand Library with Atom Names modified (not beggining with digit)
	define ("LIBLIG", "/var/www/MDWeb/scripts/lib/ligandDB");

?>
