#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56b0cac9b4346
#$ -o CURL.BIGNASim56b0cac9b4346.out
#$ -e CURL.BIGNASim56b0cac9b4346.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56b06fd3207d4","idTraj":"NAFlex_1tro","name":"BIGNASim56b0cac9b4346-NAFlex_1tro-1_5000_1","description":"Subtrajectory of NAFlex_1tro with 1_5000_1 frames selected","mask":"name *","frames":"1:5000:1","format":"mdcrd"}' http://ms2/download
