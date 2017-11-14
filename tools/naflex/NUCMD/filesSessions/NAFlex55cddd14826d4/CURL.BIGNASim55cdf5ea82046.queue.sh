#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55cdf5ea82046
#$ -o CURL.BIGNASim55cdf5ea82046.out
#$ -e CURL.BIGNASim55cdf5ea82046.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55cddd14826d4","idTraj":"NAFlex_psGGC","name":"BIGNASim55cdf5ea82046-NAFlex_psGGC-1_20_1","description":"Subtrajectory of NAFlex_psGGC with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download