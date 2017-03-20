#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim5707d48b857d0
#$ -o CURL.BIGNASim5707d48b857d0.out
#$ -e CURL.BIGNASim5707d48b857d0.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER5707d44d126ca","idTraj":"NAFlex_1tro","name":"BIGNASim5707d48b857d0-NAFlex_1tro-0_1_1","description":"Subtrajectory of NAFlex_1tro with 0_1_1 frames selected","mask":"name *","frames":"0:1:1","format":"pdb"}' http://ms2/download
