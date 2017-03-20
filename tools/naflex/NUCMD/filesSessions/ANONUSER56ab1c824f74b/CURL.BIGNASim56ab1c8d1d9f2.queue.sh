#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56ab1c8d1d9f2
#$ -o CURL.BIGNASim56ab1c8d1d9f2.out
#$ -e CURL.BIGNASim56ab1c8d1d9f2.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56ab1c824f74b","idTraj":"NAFlex_1p71","name":"BIGNASim56ab1c8d1d9f2-NAFlex_1p71-1_20_1","description":"Subtrajectory of NAFlex_1p71 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
