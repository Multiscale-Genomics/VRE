#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56fabb565f0bf
#$ -o CURL.BIGNASim56fabb565f0bf.out
#$ -e CURL.BIGNASim56fabb565f0bf.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56faab047c1b7","idTraj":"NAFlex_1tro","name":"BIGNASim56fabb565f0bf-NAFlex_1tro-1_2_1","description":"Subtrajectory of NAFlex_1tro with 1_2_1 frames selected","mask":"name *","frames":"1:2:1","format":"pdb"}' http://ms2/download
