#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55cdde5988e6f
#$ -o CURL.BIGNASim55cdde5988e6f.out
#$ -e CURL.BIGNASim55cdde5988e6f.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55cddd14826d4","idTraj":"NAFlex_1fzx","name":"BIGNASim55cdde5988e6f-NAFlex_1fzx-1_20_1","description":"Subtrajectory of NAFlex_1fzx with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
