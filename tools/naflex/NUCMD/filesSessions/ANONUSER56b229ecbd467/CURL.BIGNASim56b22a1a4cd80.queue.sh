#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56b22a1a4cd80
#$ -o CURL.BIGNASim56b22a1a4cd80.out
#$ -e CURL.BIGNASim56b22a1a4cd80.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56b229ecbd467","idTraj":"NAFlex_1p71","name":"BIGNASim56b22a1a4cd80-NAFlex_1p71-1_2000_1","description":"Subtrajectory of NAFlex_1p71 with 1_2000_1 frames selected","mask":"name *","frames":"1:2000:1","format":"netcdf"}' http://ms2/download
