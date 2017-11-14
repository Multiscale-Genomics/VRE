#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55cde726488a7
#$ -o CURL.BIGNASim55cde726488a7.out
#$ -e CURL.BIGNASim55cde726488a7.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55cde6229ec5a","idTraj":"NAFlex_1fzx","name":"BIGNASim55cde726488a7-NAFlex_1fzx-1_20_1","description":"Subtrajectory of NAFlex_1fzx with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download