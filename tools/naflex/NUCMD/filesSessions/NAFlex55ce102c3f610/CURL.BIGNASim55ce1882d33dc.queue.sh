#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55ce1882d33dc
#$ -o CURL.BIGNASim55ce1882d33dc.out
#$ -e CURL.BIGNASim55ce1882d33dc.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55ce102c3f610","idTraj":"NAFlex_1dcw","name":"BIGNASim55ce1882d33dc-NAFlex_1dcw-1_20_1","description":"Subtrajectory of NAFlex_1dcw with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download