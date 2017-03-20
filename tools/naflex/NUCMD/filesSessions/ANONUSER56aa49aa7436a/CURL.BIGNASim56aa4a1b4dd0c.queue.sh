#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56aa4a1b4dd0c
#$ -o CURL.BIGNASim56aa4a1b4dd0c.out
#$ -e CURL.BIGNASim56aa4a1b4dd0c.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56aa49aa7436a","idTraj":"NAFlex_1kx5","name":"BIGNASim56aa4a1b4dd0c-NAFlex_1kx5-1_20_1","description":"Subtrajectory of NAFlex_1kx5 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"xtc"}' http://ms2/download
