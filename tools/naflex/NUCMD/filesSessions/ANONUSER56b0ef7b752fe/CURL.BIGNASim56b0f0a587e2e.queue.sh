#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56b0f0a587e2e
#$ -o CURL.BIGNASim56b0f0a587e2e.out
#$ -e CURL.BIGNASim56b0f0a587e2e.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56b0ef7b752fe","idTraj":"NAFlex_2dgc","name":"BIGNASim56b0f0a587e2e-NAFlex_2dgc-1_100000_100","description":"Subtrajectory of NAFlex_2dgc with 1_100000_100 frames selected","mask":"name *","frames":"1:100000:100","format":"xtc"}' http://ms2/download
