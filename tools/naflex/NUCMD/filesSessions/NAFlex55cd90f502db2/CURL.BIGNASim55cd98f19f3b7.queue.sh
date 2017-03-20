#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55cd98f19f3b7
#$ -o CURL.BIGNASim55cd98f19f3b7.out
#$ -e CURL.BIGNASim55cd98f19f3b7.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55cd90f502db2","idTraj":"NAFlex_1dcw","name":"BIGNASim55cd98f19f3b7-NAFlex_1dcw-1_20_1","description":"Subtrajectory of NAFlex_1dcw with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
