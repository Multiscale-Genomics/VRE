<?php
/*
 *  globalvars.inc.php
 * Global Vars
 */
$GLOBALS['AppTitol']="BigNASim. Data Manager";
$GLOBALS['AppPrefix']="NAFlexData";
$GLOBALS['baseDir'] = pathinfo($_SERVER['SCRIPT_FILENAME'],PATHINFO_DIRNAME);
$GLOBALS['tmpDir'] = "/tmp";
$GLOBALS['baseDirBigASim'] = realpath($GLOBALS['baseDir'] . '/..');
$GLOBALS['tmpDirBigNASim'] = "filesSessions";
$GLOBALS['htmlib'] = $GLOBALS['baseDir']."/htmlib";
$GLOBALS['phplib'] = $GLOBALS['baseDir']."/phplib";
$GLOBALS['classlib'] = $GLOBALS['baseDir']."/phplib/classes";
$GLOBALS['idioma'] = "en";
$GLOBALS['homeURL'] = "dataManager";
$GLOBALS['days2expire'] = "90"; //days
#fs settings
$GLOBALS['dataDir'] = "/scratch/userData/";
$GLOBALS['fsDirPrefix'] = "pmesData";
$GLOBALS['passFile'] = "/scratch/.pass";
$GLOBALS['disklimit'] = 2*1024*1024*1024; // 2GB per user (2000000 KB)
$GLOBALS['limitFileSize'] = 100*1024*1024; // 100MB
$GLOBALS['logFile']="/scratch/uploader.log";
//$GLOBALS['ldapdn']="cn=admin,dc=bsc,dc=es";  // LDAP rdn or dn
//$GLOBALS['ldappass']='U.byPGNd';  // LDAP password

# SGE data
define ("QSUB", "source /usr/local/sge/environment/settings.sh; /usr/local/sge/bin/lx24-amd64/qsub -S /bin/bash -cwd -q www-services-fast.q@parmbsc1-naflex");
define ("QDEL", "source /usr/local/sge/environment/settings.sh; /usr/local/sge/bin/lx24-amd64/qdel ");
define ("QSTAT", "source /usr/local/sge/environment/settings.sh; /usr/local/sge/bin/lx24-amd64/qstat ");
define ("SGE_ROOT","/usr/local/sge");


?>
