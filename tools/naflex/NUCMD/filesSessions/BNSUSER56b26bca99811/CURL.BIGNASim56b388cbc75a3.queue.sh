#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56b388cbc75a3
#$ -o CURL.BIGNASim56b388cbc75a3.out
#$ -e CURL.BIGNASim56b388cbc75a3.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"BNSUSER56b26bca99811","idTraj":"NAFlex_DDD_II","name":"BIGNASim56b388cbc75a3-NAFlex_DDD_II-1_5000_1","description":"Subtrajectory of NAFlex_DDD_II with 1_5000_1 frames selected","mask":"name *","frames":"1:5000:1","format":"xtc"}' http://ms2/download