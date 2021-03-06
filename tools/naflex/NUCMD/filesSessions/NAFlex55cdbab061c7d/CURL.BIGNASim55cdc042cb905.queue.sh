#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55cdc042cb905
#$ -o CURL.BIGNASim55cdc042cb905.out
#$ -e CURL.BIGNASim55cdc042cb905.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55cdbab061c7d","idTraj":"NAFlex_1d11","name":"BIGNASim55cdc042cb905-NAFlex_1d11-1_20_1","description":"Subtrajectory of NAFlex_1d11 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
