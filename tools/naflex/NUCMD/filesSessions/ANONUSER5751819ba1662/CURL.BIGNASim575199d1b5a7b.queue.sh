#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim575199d1b5a7b
#$ -o CURL.BIGNASim575199d1b5a7b.out
#$ -e CURL.BIGNASim575199d1b5a7b.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER5751819ba1662","idTraj":"NAFlex_muAAAA","name":"BIGNASim575199d1b5a7b-NAFlex_muAAAA-1_20_1","description":"Subtrajectory of NAFlex_muAAAA with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://m002/download
