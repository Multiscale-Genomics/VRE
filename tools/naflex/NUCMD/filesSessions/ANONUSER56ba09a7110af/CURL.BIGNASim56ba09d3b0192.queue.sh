#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56ba09d3b0192
#$ -o CURL.BIGNASim56ba09d3b0192.out
#$ -e CURL.BIGNASim56ba09d3b0192.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56ba09a7110af","idTraj":"NAFlex_1p71","name":"BIGNASim56ba09d3b0192-NAFlex_1p71-1_20_1","description":"Subtrajectory of NAFlex_1p71 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
