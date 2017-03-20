#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim566a961b59737
#$ -o CURL.BIGNASim566a961b59737.out
#$ -e CURL.BIGNASim566a961b59737.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER56697b40c2a67","idTraj":"NAFlex_HTQ","name":"BIGNASim566a961b59737-NAFlex_HTQ-5001_9999_50","description":"Subtrajectory of NAFlex_HTQ with 5001_9999_50 frames selected","mask":"name *","frames":"5001:9999:50","format":"xtc"}' http://ms2/download
