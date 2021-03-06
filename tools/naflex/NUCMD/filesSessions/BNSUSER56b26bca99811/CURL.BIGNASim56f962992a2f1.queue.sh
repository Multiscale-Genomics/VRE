#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56f962992a2f1
#$ -o CURL.BIGNASim56f962992a2f1.out
#$ -e CURL.BIGNASim56f962992a2f1.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"BNSUSER56b26bca99811","idTraj":"NAFlex_1tro","name":"BIGNASim56f962992a2f1-NAFlex_1tro-1_5000_1000","description":"Subtrajectory of NAFlex_1tro with 1_5000_1000 frames selected","mask":"name *","frames":"1:5000:1000","format":"pdb"}' http://ms2/download
