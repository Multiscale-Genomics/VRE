#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56c153bccdefa
#$ -o CURL.BIGNASim56c153bccdefa.out
#$ -e CURL.BIGNASim56c153bccdefa.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56c15372debd8","idTraj":"NAFlex_CTAG_9","name":"BIGNASim56c153bccdefa-NAFlex_CTAG_9-1_20_1","description":"Subtrajectory of NAFlex_CTAG_9 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
