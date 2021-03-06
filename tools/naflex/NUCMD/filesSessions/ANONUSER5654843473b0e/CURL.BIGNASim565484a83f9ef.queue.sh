#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim565484a83f9ef
#$ -o CURL.BIGNASim565484a83f9ef.out
#$ -e CURL.BIGNASim565484a83f9ef.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER5654843473b0e","idTraj":"NAFlex_D05M","name":"BIGNASim565484a83f9ef-NAFlex_D05M-1_20_1","description":"Subtrajectory of NAFlex_D05M with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"dcd"}' http://ms2/download
