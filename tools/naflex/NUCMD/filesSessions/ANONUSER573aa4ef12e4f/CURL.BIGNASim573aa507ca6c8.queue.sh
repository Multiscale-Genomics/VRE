#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim573aa507ca6c8
#$ -o CURL.BIGNASim573aa507ca6c8.out
#$ -e CURL.BIGNASim573aa507ca6c8.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER573aa4ef12e4f","idTraj":"NAFlex_DDD_bsc1","name":"BIGNASim573aa507ca6c8-NAFlex_DDD_bsc1-1_20_1","description":"Subtrajectory of NAFlex_DDD_bsc1 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://m002/download
