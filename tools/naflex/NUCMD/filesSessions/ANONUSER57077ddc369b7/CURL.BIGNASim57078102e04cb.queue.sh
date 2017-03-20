#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim57078102e04cb
#$ -o CURL.BIGNASim57078102e04cb.out
#$ -e CURL.BIGNASim57078102e04cb.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER57077ddc369b7","idTraj":"NAFlex_1tro","name":"BIGNASim57078102e04cb-NAFlex_1tro-1_20_1","description":"Subtrajectory of NAFlex_1tro with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
