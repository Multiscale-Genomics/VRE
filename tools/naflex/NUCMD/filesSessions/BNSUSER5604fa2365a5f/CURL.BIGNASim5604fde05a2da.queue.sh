#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim5604fde05a2da
#$ -o CURL.BIGNASim5604fde05a2da.out
#$ -e CURL.BIGNASim5604fde05a2da.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"BNSUSER5604fa2365a5f","idTraj":"NAFlex_DDD_bsc1","name":"BIGNASim5604fde05a2da-NAFlex_DDD_bsc1-1_20_1","description":"Subtrajectory of NAFlex_DDD_bsc1 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download