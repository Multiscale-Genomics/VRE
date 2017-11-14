#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim5613bd9a042f1
#$ -o CURL.BIGNASim5613bd9a042f1.out
#$ -e CURL.BIGNASim5613bd9a042f1.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER5613b733cc04a","idTraj":"NAFlex_2mhi","name":"BIGNASim5613bd9a042f1-NAFlex_2mhi-1_20_1","description":"Subtrajectory of NAFlex_2mhi with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download