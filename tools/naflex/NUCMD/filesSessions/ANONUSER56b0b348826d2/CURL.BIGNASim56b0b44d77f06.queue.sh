#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56b0b44d77f06
#$ -o CURL.BIGNASim56b0b44d77f06.out
#$ -e CURL.BIGNASim56b0b44d77f06.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56b0b348826d2","idTraj":"NAFlex_1p71","name":"BIGNASim56b0b44d77f06-NAFlex_1p71-1_2000_1","description":"Subtrajectory of NAFlex_1p71 with 1_2000_1 frames selected","mask":"name *","frames":"1:2000:1","format":"mdcrd"}' http://ms2/download