#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56e9a0b78f4eb
#$ -o CURL.BIGNASim56e9a0b78f4eb.out
#$ -e CURL.BIGNASim56e9a0b78f4eb.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56e99ecac1862","idTraj":"NAFlex_DTipDang015M","name":"BIGNASim56e9a0b78f4eb-NAFlex_DTipDang015M-1_5000_1","description":"Subtrajectory of NAFlex_DTipDang015M with 1_5000_1 frames selected","mask":"name *","frames":"1:5000:1","format":"xtc"}' http://ms2/download
