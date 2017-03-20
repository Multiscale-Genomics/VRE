#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim565de45f56f29
#$ -o CURL.BIGNASim565de45f56f29.out
#$ -e CURL.BIGNASim565de45f56f29.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER565d5a4f33f01","idTraj":"NAFlex_DDD_bsc1","name":"BIGNASim565de45f56f29-NAFlex_DDD_bsc1-1_20_1","description":"Subtrajectory of NAFlex_DDD_bsc1 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
