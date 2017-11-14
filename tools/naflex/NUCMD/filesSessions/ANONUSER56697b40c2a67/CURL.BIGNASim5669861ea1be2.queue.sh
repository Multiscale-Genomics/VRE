#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim5669861ea1be2
#$ -o CURL.BIGNASim5669861ea1be2.out
#$ -e CURL.BIGNASim5669861ea1be2.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56697b40c2a67","idTraj":"NAFlex_HTQ","name":"BIGNASim5669861ea1be2-NAFlex_HTQ-1_500000_100","description":"Subtrajectory of NAFlex_HTQ with 1_500000_100 frames selected","mask":"name *","frames":"1:500000:100","format":"xtc"}' http://ms2/download