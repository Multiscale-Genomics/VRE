#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim565e4621afaa0
#$ -o CURL.BIGNASim565e4621afaa0.out
#$ -e CURL.BIGNASim565e4621afaa0.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":null,"idTraj":null,"name":"BIGNASim565e4621afaa0--","description":"Subtrajectory of  with  frames selected","mask":null,"frames":null,"format":null}' http://ms2/download
