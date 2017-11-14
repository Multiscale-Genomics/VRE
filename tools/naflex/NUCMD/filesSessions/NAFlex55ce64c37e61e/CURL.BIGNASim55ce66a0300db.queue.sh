#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55ce66a0300db
#$ -o CURL.BIGNASim55ce66a0300db.out
#$ -e CURL.BIGNASim55ce66a0300db.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55ce64c37e61e","idTraj":"NAFlex_1g14","name":"BIGNASim55ce66a0300db-NAFlex_1g14-1_2000_1","description":"Subtrajectory of NAFlex_1g14 with 1_2000_1 frames selected","mask":"name *","frames":"1:2000:1","format":"dcd"}' http://ms2/download