#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56f96d2a95e9b
#$ -o CURL.BIGNASim56f96d2a95e9b.out
#$ -e CURL.BIGNASim56f96d2a95e9b.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"BNSUSER56b26bca99811","idTraj":"NAFlex_1tro","name":"BIGNASim56f96d2a95e9b-NAFlex_1tro-1_2_1","description":"Subtrajectory of NAFlex_1tro with 1_2_1 frames selected","mask":"name *","frames":"1:2:1","format":"pdb"}' http://ms2/download
