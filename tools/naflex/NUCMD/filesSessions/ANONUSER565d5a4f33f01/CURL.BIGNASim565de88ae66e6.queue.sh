#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim565de88ae66e6
#$ -o CURL.BIGNASim565de88ae66e6.out
#$ -e CURL.BIGNASim565de88ae66e6.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER565d5a4f33f01","idTraj":"NAFlex_DTipDang015M","name":"BIGNASim565de88ae66e6-NAFlex_DTipDang015M-1_20_1","description":"Subtrajectory of NAFlex_DTipDang015M with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"dcd"}' http://ms2/download
