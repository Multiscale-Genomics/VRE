#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim55eb38217b462
#$ -o CURL.BIGNASim55eb38217b462.out
#$ -e CURL.BIGNASim55eb38217b462.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"NAFlex55eaf7693adaa","name":"BIGNASim55eb38217b462-CG-1_5000_100-10trajs","description":"Metatrajectory of fragment CG, with frame selection: 1_5000_100, from 10 trajectories: NAFlex_v106t9,NAFlex_v106t10,NAFlex_v106R3t10,NAFlex_v106R2t10,NAFlex_v106Ct10,NAFlex_v106Bt10,NAFlex_v100t9,NAFlex_rna4,NAFlex_rna3,NAFlex_rna1","id_trajs":[4],"frames":"1:5000:100","format":"dcd"}' http://ms2/compose
