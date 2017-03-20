#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim566a95ef76de7
#$ -o CURL.BIGNASim566a95ef76de7.out
#$ -e CURL.BIGNASim566a95ef76de7.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56697b40c2a67","idTraj":"NAFlex_HTQ","name":"BIGNASim566a95ef76de7-NAFlex_HTQ-1_5000_50","description":"Subtrajectory of NAFlex_HTQ with 1_5000_50 frames selected","mask":"name *","frames":"1:5000:50","format":"xtc"}' http://ms2/download
