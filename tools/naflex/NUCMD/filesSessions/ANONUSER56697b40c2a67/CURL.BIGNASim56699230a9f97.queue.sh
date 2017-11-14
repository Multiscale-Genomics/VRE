#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56699230a9f97
#$ -o CURL.BIGNASim56699230a9f97.out
#$ -e CURL.BIGNASim56699230a9f97.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56697b40c2a67","idTraj":"NAFlex_HTQ","name":"BIGNASim56699230a9f97-NAFlex_HTQ-5000_8000_1","description":"Subtrajectory of NAFlex_HTQ with 5000_8000_1 frames selected","mask":"name *","frames":"5000:8000:1","format":"xtc"}' http://ms2/download