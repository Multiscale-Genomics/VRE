#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim565854b5e111c
#$ -o CURL.BIGNASim565854b5e111c.out
#$ -e CURL.BIGNASim565854b5e111c.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER565854911ddc6","idTraj":"NAFlex_DDD_bsc1","name":"BIGNASim565854b5e111c-NAFlex_DDD_bsc1-1_20_1","description":"Subtrajectory of NAFlex_DDD_bsc1 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"dcd"}' http://ms2/download
