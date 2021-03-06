#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56697852555b6
#$ -o CURL.BIGNASim56697852555b6.out
#$ -e CURL.BIGNASim56697852555b6.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56697813b72ed","idTraj":"NAFlex_HTQ","name":"BIGNASim56697852555b6-NAFlex_HTQ-1_450000_1000","description":"Subtrajectory of NAFlex_HTQ with 1_450000_1000 frames selected","mask":"resid 1:22","frames":"1:450000:1000","format":"pdb"}' http://ms2/download
