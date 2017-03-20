#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55ce1777a8762
#$ -o CURL.BIGNASim55ce1777a8762.out
#$ -e CURL.BIGNASim55ce1777a8762.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55ce160f93d3a","name":"BIGNASim55ce1777a8762--1_5000_100-2trajs","description":"Metatrajectory of fragment , with frame selection: 1_5000_100, from 2 trajectories: NAFlex_1yo5,NAFlex_2oue","id_trajs":[4,{"NAFlex_1yo5":[1,21]}],"frames":"1:5000:100","format":"dcd"}' http://ms2/compose
