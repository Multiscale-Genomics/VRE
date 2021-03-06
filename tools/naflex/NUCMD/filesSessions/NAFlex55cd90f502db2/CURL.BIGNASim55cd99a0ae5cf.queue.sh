#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55cd99a0ae5cf
#$ -o CURL.BIGNASim55cd99a0ae5cf.out
#$ -e CURL.BIGNASim55cd99a0ae5cf.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55cd90f502db2","idTraj":"NAFlex_1d11","name":"BIGNASim55cd99a0ae5cf-NAFlex_1d11-1_20_1","description":"Subtrajectory of NAFlex_1d11 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
