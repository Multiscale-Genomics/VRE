#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55d2fbb929d7d
#$ -o CURL.BIGNASim55d2fbb929d7d.out
#$ -e CURL.BIGNASim55d2fbb929d7d.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55d099baf125c","name":"BIGNASim55d2fbb929d7d-TATA-1_5000_100-3trajs","description":"Metatrajectory of fragment TATA, with frame selection: 1_5000_100, from 3 trajectories: NAFlex_CTAG_29,NAFlex_CTAG_28,NAFlex_CTAG_27","id_trajs":[4],"frames":"1:5000:100","format":"netcdf"}' http://ms2/compose
