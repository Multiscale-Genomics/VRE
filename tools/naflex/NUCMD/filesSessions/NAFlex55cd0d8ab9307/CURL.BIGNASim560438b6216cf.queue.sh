#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim560438b6216cf
#$ -o CURL.BIGNASim560438b6216cf.out
#$ -e CURL.BIGNASim560438b6216cf.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55cd0d8ab9307","idTraj":"NAFlex_DDD_bsc1","name":"BIGNASim560438b6216cf-NAFlex_DDD_bsc1-1_20_1","description":"Subtrajectory of NAFlex_DDD_bsc1 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
