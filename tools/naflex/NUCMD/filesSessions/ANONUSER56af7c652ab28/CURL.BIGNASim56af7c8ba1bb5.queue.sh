#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56af7c8ba1bb5
#$ -o CURL.BIGNASim56af7c8ba1bb5.out
#$ -e CURL.BIGNASim56af7c8ba1bb5.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56af7c652ab28","idTraj":"NAFlex_1p71","name":"BIGNASim56af7c8ba1bb5-NAFlex_1p71-1_20_1","description":"Subtrajectory of NAFlex_1p71 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
