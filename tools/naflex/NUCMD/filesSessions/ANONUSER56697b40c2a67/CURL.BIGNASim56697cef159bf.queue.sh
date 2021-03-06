#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56697cef159bf
#$ -o CURL.BIGNASim56697cef159bf.out
#$ -e CURL.BIGNASim56697cef159bf.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56697b40c2a67","idTraj":"NAFlex_HTQ","name":"BIGNASim56697cef159bf-NAFlex_HTQ-1_20_1","description":"Subtrajectory of NAFlex_HTQ with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"xtc"}' http://ms2/download
