#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56b27383c10ea
#$ -o CURL.BIGNASim56b27383c10ea.out
#$ -e CURL.BIGNASim56b27383c10ea.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"BNSUSER56b26bca99811","idTraj":"NAFlex_v106t10","name":"BIGNASim56b27383c10ea-NAFlex_v106t10-1_2_1","description":"Subtrajectory of NAFlex_v106t10 with 1_2_1 frames selected","mask":"name *","frames":"1:2:1","format":"xtc"}' http://ms2/download
