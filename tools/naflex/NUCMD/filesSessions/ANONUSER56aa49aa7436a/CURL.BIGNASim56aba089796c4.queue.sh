#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56aba089796c4
#$ -o CURL.BIGNASim56aba089796c4.out
#$ -e CURL.BIGNASim56aba089796c4.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56aa49aa7436a","idTraj":"NAFlex_2dgc","name":"BIGNASim56aba089796c4-NAFlex_2dgc-1_5000_100","description":"Subtrajectory of NAFlex_2dgc with 1_5000_100 frames selected","mask":"name *","frames":"1:5000:100","format":"xtc"}' http://ms2/download
