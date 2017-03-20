#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56b0f97af1f5b
#$ -o CURL.BIGNASim56b0f97af1f5b.out
#$ -e CURL.BIGNASim56b0f97af1f5b.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56b0ef7b752fe","idTraj":"NAFlex_2dgc","name":"BIGNASim56b0f97af1f5b-NAFlex_2dgc-1_500000_100","description":"Subtrajectory of NAFlex_2dgc with 1_500000_100 frames selected","mask":"name *","frames":"1:500000:100","format":"xtc"}' http://ms2/download
