#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56a753d527024
#$ -o CURL.BIGNASim56a753d527024.out
#$ -e CURL.BIGNASim56a753d527024.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56a745d0964df","idTraj":"NAFlex_1pqt","name":"BIGNASim56a753d527024-NAFlex_1pqt-4998_5000_1","description":"Subtrajectory of NAFlex_1pqt with 4998_5000_1 frames selected","mask":"all","frames":"4998:5000:1","format":"pdb"}' http://ms2/download
