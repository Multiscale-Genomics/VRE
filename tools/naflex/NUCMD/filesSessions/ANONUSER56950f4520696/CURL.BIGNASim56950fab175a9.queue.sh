#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56950fab175a9
#$ -o CURL.BIGNASim56950fab175a9.out
#$ -e CURL.BIGNASim56950fab175a9.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56950f4520696","idTraj":"NAFlex_A-Ethanol","name":"BIGNASim56950fab175a9-NAFlex_A-Ethanol-1_10000_10","description":"Subtrajectory of NAFlex_A-Ethanol with 1_10000_10 frames selected","mask":"name *","frames":"1:10000:10","format":"gro"}' http://ms2/download
