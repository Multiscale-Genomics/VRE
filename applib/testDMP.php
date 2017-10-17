<?php 

require "../phplib/genlibraries.php";
require "../phplib/mongoDMP.inc.php";

#$GLOBALS['dataDir'] = "/gpfs/MuG_userdata/"; 
$_SESSION['curDir']        = "MuGUSER59808d047dddf";
$_SESSION['User']['id']    = "MuGUSER59808d047dddf";
$_SESSION['User']['token'] = "eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJtcWlfZHE2NWxkaDdsOTNyUDBTenRaYUFZUnZBcXlsaXhabU9pN0IyUXc0In0.eyJqdGkiOiI0NzcyNTNjYy01MDRiLTRmMTItOTBjMy1kOGVkOTAyMzQzZjkiLCJleHAiOjE1MDgxNjI3MTUsIm5iZiI6MCwiaWF0IjoxNTA4MTYxODE1LCJpc3MiOiJodHRwczovL2luYi5ic2MuZXMvYXV0aC9yZWFsbXMvbXVnIiwiYXVkIjoibXVnIiwic3ViIjoiNzRiNWYyODktN2NkZi00MWUwLWIwMWQtYTI3OTQxMjYwZmZiIiwidHlwIjoiQmVhcmVyIiwiYXpwIjoibXVnIiwiYXV0aF90aW1lIjoxNTA4MTM2Mjc2LCJzZXNzaW9uX3N0YXRlIjoiZTNmMGFlYzItMWYyYy00Y2ZlLThlMDUtZDhmZDYwYjRlZWE0IiwiYWNyIjoiMCIsImFsbG93ZWQtb3JpZ2lucyI6W10sInJlc291cmNlX2FjY2VzcyI6e30sImxhc3ROYW1lIjoiQ29kw7MiLCJmaXJzdE5hbWUiOiJMYWlhIiwibmFtZSI6IkxhaWEgQ29kw7MiLCJlbWFpbCI6ImxhaWFjb2RvQGdtYWlsLmNvbSIsInVzZXJuYW1lIjoibGFpYWNvZG9AZ21haWwuY29tIn0.EfrIRCvKE7arxd2kvZB7JvjwqOcyXXPvc3uOUpBy-FfVLhjZToS96FrjB0OpwAfl3H_qb31VvVvC82AkgUScEKS7dzALGECZTMZ_daPOH2mZ5Diu92ViOagLrHbY_jllpwTvYO3mUdHcDtq2UB3tcLHEs2fQ5P3AMbuRasybcH-qT2QIYKUyn_b9I7Kpk4YI3x478JUQxE7XDmZHvSApX6YpsyjSXrDYbpPArshBXg8XrQXYH-DNSpk6szSpWM02w7y0Vr_79YndcwhXywFqu4L-Ye0T_tIK0zat5pgO3jpHbtDZ5SZrqRUyVOE0ClfxHmBzQrrwnSeETGTPj_Qamw";


#http://localhost:500/mug/api/dmp/track?file_id=59a81d658743651a977c98fc&user_id=test_user&chrom=1&start=1000&end=2000"

print "\n\n################ getGSFile_fromIdXXX\n";
$f = getGSFile_fromIdXXX("59a962598743651a977c991e");
print "\n";
var_dump($f);

print "\n\n################ getGSFileId_fromPathXXX\n";
$f = getGSFileId_fromPathXXX("MuGUSER59808d047dddf/testdir/test_file.json");
print "\n";
var_dump($f);

print "\n\n################ getGSFileId_fromPathXXX\n";
$f = getGSFilesFromDirXXX("59956f2bd9422a4c268719f9");
print "\n";
var_dump($f);
exit(0);


print "\n\n################ createGSDirBNSXXX\n";
$id = createGSDirBNSXXX("test_user/run000");
print "\n";
var_dump($id);

print "\n################ ERROR SSESION\n";
var_dump($_SESSION['errorData']);

