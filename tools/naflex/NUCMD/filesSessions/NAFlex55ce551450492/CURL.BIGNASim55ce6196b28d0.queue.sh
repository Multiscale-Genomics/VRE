#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55ce6196b28d0
#$ -o CURL.BIGNASim55ce6196b28d0.out
#$ -e CURL.BIGNASim55ce6196b28d0.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55ce551450492","name":"BIGNASim55ce6196b28d0-GGGG-1_5000_100-4trajs","description":"Metatrajectory of fragment GGGG, with frame selection: 1_5000_100, from 4 trajectories: NAFlex_1j5n,NAFlex_1vt5,NAFlex_440d,NAFlex_CTAG_8","id_trajs":[4,{"NAFlex_440d":[1,15]},{"NAFlex_CTAG_8":[1,27,2,26]}],"frames":"1:5000:100","format":"dcd"}' http://ms2/compose