#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim5613beae9eb0e
#$ -o CURL.BIGNASim5613beae9eb0e.out
#$ -e CURL.BIGNASim5613beae9eb0e.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER5613b733cc04a","idTraj":"NAFlex_2y95","name":"BIGNASim5613beae9eb0e-NAFlex_2y95-1_20_1","description":"Subtrajectory of NAFlex_2y95 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
