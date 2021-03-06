#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56d497158cf13
#$ -o CURL.BIGNASim56d497158cf13.out
#$ -e CURL.BIGNASim56d497158cf13.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56d48ee6b1048","idTraj":"NAFlex_v106Bt10","name":"BIGNASim56d497158cf13-NAFlex_v106Bt10-1_5000_500","description":"Subtrajectory of NAFlex_v106Bt10 with 1_5000_500 frames selected","mask":"name *","frames":"1:5000:500","format":"pdb"}' http://ms2/download
