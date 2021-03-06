#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56b0d8196024d
#$ -o CURL.BIGNASim56b0d8196024d.out
#$ -e CURL.BIGNASim56b0d8196024d.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56b0d73da3215","idTraj":"NAFlex_2dgc","name":"BIGNASim56b0d8196024d-NAFlex_2dgc-1_500000_100","description":"Subtrajectory of NAFlex_2dgc with 1_500000_100 frames selected","mask":"name *","frames":"1:500000:100","format":"xtc"}' http://ms2/download
