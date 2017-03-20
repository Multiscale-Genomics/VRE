#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56f2ba72e6ef6
#$ -o CURL.BIGNASim56f2ba72e6ef6.out
#$ -e CURL.BIGNASim56f2ba72e6ef6.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56f2b9f06b464","idTraj":"NAFlex_1tro","name":"BIGNASim56f2ba72e6ef6-NAFlex_1tro-1_5000_1","description":"Subtrajectory of NAFlex_1tro with 1_5000_1 frames selected","mask":"name *","frames":"1:5000:1","format":"xtc"}' http://ms2/download
