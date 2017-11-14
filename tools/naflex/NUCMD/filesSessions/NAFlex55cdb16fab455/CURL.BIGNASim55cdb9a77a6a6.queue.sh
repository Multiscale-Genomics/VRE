#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55cdb9a77a6a6
#$ -o CURL.BIGNASim55cdb9a77a6a6.out
#$ -e CURL.BIGNASim55cdb9a77a6a6.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55cdb16fab455","name":"BIGNASim55cdb9a77a6a6-GTAC-1_5000_100-5trajs","description":"Metatrajectory of fragment GTAC, with frame selection: 1_5000_100, from 5 trajectories: NAFlex_1d11,NAFlex_1dcw,NAFlex_1tro,NAFlex_36merSPCE,NAFlex_36merTIP3P","id_trajs":[4,{"NAFlex_36merTIP3P":[16,52]},{"NAFlex_36merSPCE":[16,52]},{"NAFlex_1d11":[1,7]}],"frames":"1:5000:100","format":"mdcrd"}' http://ms2/compose