#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55ce5c0ae7103
#$ -o CURL.BIGNASim55ce5c0ae7103.out
#$ -e CURL.BIGNASim55ce5c0ae7103.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55ce59747460b","idTraj":"NAFlex_1iv6","name":"BIGNASim55ce5c0ae7103-NAFlex_1iv6-1_5000_1","description":"Subtrajectory of NAFlex_1iv6 with 1_5000_1 frames selected","mask":"name *","frames":"1:5000:1","format":"dcd"}' http://ms2/download
