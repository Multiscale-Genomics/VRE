#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim57117e1a35f8b
#$ -o CURL.BIGNASim57117e1a35f8b.out
#$ -e CURL.BIGNASim57117e1a35f8b.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"BNSUSER56b26bca99811","idTraj":"NAFlex_1kx5","name":"BIGNASim57117e1a35f8b-NAFlex_1kx5-0_1_1","description":"Subtrajectory of NAFlex_1kx5 with 0_1_1 frames selected","mask":"name *","frames":"0:1:1","format":"pdb"}' http://ms2/download
