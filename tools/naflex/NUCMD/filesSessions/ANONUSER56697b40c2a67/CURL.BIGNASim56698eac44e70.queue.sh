#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56698eac44e70
#$ -o CURL.BIGNASim56698eac44e70.out
#$ -e CURL.BIGNASim56698eac44e70.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56697b40c2a67","idTraj":"NAFlex_HTQ","name":"BIGNASim56698eac44e70-NAFlex_HTQ-1_500000_100","description":"Subtrajectory of NAFlex_HTQ with 1_500000_100 frames selected","mask":"name *","frames":"1:500000:100","format":"pdb"}' http://ms2/download
