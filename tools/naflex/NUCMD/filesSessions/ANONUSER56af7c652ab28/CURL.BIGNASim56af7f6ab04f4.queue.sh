#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56af7f6ab04f4
#$ -o CURL.BIGNASim56af7f6ab04f4.out
#$ -e CURL.BIGNASim56af7f6ab04f4.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56af7c652ab28","idTraj":"NAFlex_1p71B","name":"BIGNASim56af7f6ab04f4-NAFlex_1p71B-1_20_1","description":"Subtrajectory of NAFlex_1p71B with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
