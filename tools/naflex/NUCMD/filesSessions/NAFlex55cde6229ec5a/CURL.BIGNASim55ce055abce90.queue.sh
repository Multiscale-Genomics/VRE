#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55ce055abce90
#$ -o CURL.BIGNASim55ce055abce90.out
#$ -e CURL.BIGNASim55ce055abce90.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55cde6229ec5a","name":"BIGNASim55ce055abce90-AATTC-1_5000_100-5trajs","description":"Metatrajectory of fragment AATTC, with frame selection: 1_5000_100, from 5 trajectories: NAFlex_1d11,NAFlex_1dcw,NAFlex_1tro,NAFlex_36merSPCE,NAFlex_36merTIP3P","id_trajs":[5],"frames":"1:5000:100","format":"dcd"}' http://ms2/compose
