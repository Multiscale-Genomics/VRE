#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim5613c077f06ec
#$ -o CURL.BIGNASim5613c077f06ec.out
#$ -e CURL.BIGNASim5613c077f06ec.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER5613b733cc04a","idTraj":"NAFlex_rna1","name":"BIGNASim5613c077f06ec-NAFlex_rna1-1_20_1","description":"Subtrajectory of NAFlex_rna1 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download