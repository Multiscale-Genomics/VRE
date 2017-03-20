#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56bb444cb8f99
#$ -o CURL.BIGNASim56bb444cb8f99.out
#$ -e CURL.BIGNASim56bb444cb8f99.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56bb4444565d3","idTraj":"NAFlex_D05M","name":"BIGNASim56bb444cb8f99-NAFlex_D05M-1_20_1","description":"Subtrajectory of NAFlex_D05M with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
