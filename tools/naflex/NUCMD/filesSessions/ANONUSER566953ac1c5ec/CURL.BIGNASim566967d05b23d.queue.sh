#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim566967d05b23d
#$ -o CURL.BIGNASim566967d05b23d.out
#$ -e CURL.BIGNASim566967d05b23d.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER566953ac1c5ec","idTraj":"NAFlex_D05M","name":"BIGNASim566967d05b23d-NAFlex_D05M-1_20_1","description":"Subtrajectory of NAFlex_D05M with 1_20_1 frames selected","mask":"resid 2:11","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
