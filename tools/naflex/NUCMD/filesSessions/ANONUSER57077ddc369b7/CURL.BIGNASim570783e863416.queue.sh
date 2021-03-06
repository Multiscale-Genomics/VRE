#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim570783e863416
#$ -o CURL.BIGNASim570783e863416.out
#$ -e CURL.BIGNASim570783e863416.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER57077ddc369b7","idTraj":"NAFlex_1tro","name":"BIGNASim570783e863416-NAFlex_1tro-1_5_1","description":"Subtrajectory of NAFlex_1tro with 1_5_1 frames selected","mask":"name *","frames":"1:5:1","format":"mdcrd"}' http://ms2/download
