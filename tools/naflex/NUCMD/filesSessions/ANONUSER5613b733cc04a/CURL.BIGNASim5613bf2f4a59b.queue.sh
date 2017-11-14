#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim5613bf2f4a59b
#$ -o CURL.BIGNASim5613bf2f4a59b.out
#$ -e CURL.BIGNASim5613bf2f4a59b.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER5613b733cc04a","idTraj":"NAFlex_2jwv","name":"BIGNASim5613bf2f4a59b-NAFlex_2jwv-1_20_1","description":"Subtrajectory of NAFlex_2jwv with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download