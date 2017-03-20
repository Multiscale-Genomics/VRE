#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55ce5b2dde20f
#$ -o CURL.BIGNASim55ce5b2dde20f.out
#$ -e CURL.BIGNASim55ce5b2dde20f.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55ce551450492","name":"BIGNASim55ce5b2dde20f-AAAA-1_5000_100-4trajs","description":"Metatrajectory of fragment AAAA, with frame selection: 1_5000_100, from 4 trajectories: NAFlex_1j5n,NAFlex_1vt5,NAFlex_440d,NAFlex_CTAG_8","id_trajs":[4],"frames":"1:5000:100","format":"dcd"}' http://ms2/compose
