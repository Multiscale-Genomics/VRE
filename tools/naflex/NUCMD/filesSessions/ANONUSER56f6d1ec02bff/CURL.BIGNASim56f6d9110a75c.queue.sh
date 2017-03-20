#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56f6d9110a75c
#$ -o CURL.BIGNASim56f6d9110a75c.out
#$ -e CURL.BIGNASim56f6d9110a75c.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56f6d1ec02bff","idTraj":"NAFlex_DDD_bsc1","name":"BIGNASim56f6d9110a75c-NAFlex_DDD_bsc1-1_20_1","description":"Subtrajectory of NAFlex_DDD_bsc1 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"pdb"}' http://ms2/download
