#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim5751403fc3231
#$ -o CURL.BIGNASim5751403fc3231.out
#$ -e CURL.BIGNASim5751403fc3231.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER57512f8058af0","idTraj":"NAFlex_muAAAA","name":"BIGNASim5751403fc3231-NAFlex_muAAAA-1_20_1","description":"Subtrajectory of NAFlex_muAAAA with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://m002/download