#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55ce6f7e43eaf
#$ -o CURL.BIGNASim55ce6f7e43eaf.out
#$ -e CURL.BIGNASim55ce6f7e43eaf.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55ce551450492","name":"BIGNASim55ce6f7e43eaf-ACGT-1_5000_100-3trajs","description":"Metatrajectory of fragment ACGT, with frame selection: 1_5000_100, from 3 trajectories: NAFlex_56merA,NAFlex_56merB,NAFlex_56merL","id_trajs":[4],"frames":"1:5000:100","format":"dcd"}' http://ms2/compose
