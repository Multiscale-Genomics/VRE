#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim566995ff890b8
#$ -o CURL.BIGNASim566995ff890b8.out
#$ -e CURL.BIGNASim566995ff890b8.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56697b40c2a67","idTraj":"NAFlex_HTQ","name":"BIGNASim566995ff890b8-NAFlex_HTQ-1_20_1","description":"Subtrajectory of NAFlex_HTQ with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"xtc"}' http://ms2/download
