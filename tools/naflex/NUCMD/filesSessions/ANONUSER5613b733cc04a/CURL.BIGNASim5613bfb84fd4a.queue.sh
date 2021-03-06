#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim5613bfb84fd4a
#$ -o CURL.BIGNASim5613bfb84fd4a.out
#$ -e CURL.BIGNASim5613bfb84fd4a.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER5613b733cc04a","idTraj":"NAFlex_rna3","name":"BIGNASim5613bfb84fd4a-NAFlex_rna3-1_20_1","description":"Subtrajectory of NAFlex_rna3 with 1_20_1 frames selected","mask":"name *","frames":"1:20:1","format":"mdcrd"}' http://ms2/download
