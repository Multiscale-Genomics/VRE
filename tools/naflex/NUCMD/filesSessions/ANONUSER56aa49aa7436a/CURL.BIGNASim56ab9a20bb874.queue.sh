#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56ab9a20bb874
#$ -o CURL.BIGNASim56ab9a20bb874.out
#$ -e CURL.BIGNASim56ab9a20bb874.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56aa49aa7436a","idTraj":"NAFlex_2dgc","name":"BIGNASim56ab9a20bb874-NAFlex_2dgc-1_20_1","description":"Subtrajectory of NAFlex_2dgc with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"xtc"}' http://ms2/download
