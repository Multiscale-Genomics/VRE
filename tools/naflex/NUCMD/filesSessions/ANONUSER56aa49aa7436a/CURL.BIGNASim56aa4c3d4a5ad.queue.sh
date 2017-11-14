#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56aa4c3d4a5ad
#$ -o CURL.BIGNASim56aa4c3d4a5ad.out
#$ -e CURL.BIGNASim56aa4c3d4a5ad.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56aa49aa7436a","idTraj":"NAFlex_2dgc","name":"BIGNASim56aa4c3d4a5ad-NAFlex_2dgc-1_20_1","description":"Subtrajectory of NAFlex_2dgc with 1_20_1 frames selected","mask":"protein or nucleic","frames":"1:20:1","format":"netcdf"}' http://ms2/download