#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim566a99333be83
#$ -o CURL.BIGNASim566a99333be83.out
#$ -e CURL.BIGNASim566a99333be83.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56697b40c2a67","idTraj":"NAFlex_HTQ","name":"BIGNASim566a99333be83-NAFlex_HTQ-200_4000_10","description":"Subtrajectory of NAFlex_HTQ with 200_4000_10 frames selected","mask":"name *","frames":"200:4000:10","format":"xtc"}' http://ms2/download
