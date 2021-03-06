#!/bin/csh
# generated by BIGNASim metatrajectory generator
#$ -cwd
#$ -N BIGNaSim_curl_call_BIGNASim56966637c793c
#$ -o CURL.BIGNASim56966637c793c.out
#$ -e CURL.BIGNASim56966637c793c.err

# Launching CURL...

# CURL is calling a REST WS that generates the metatrajectory.
curl -i -H "Content-Type: application/json" -X GET -d '{"idSession":"ANONUSER569665be88710","idTraj":"NAFlex_A-Ethanol","name":"BIGNASim56966637c793c-NAFlex_A-Ethanol-1_10000_10","description":"Subtrajectory of NAFlex_A-Ethanol with 1_10000_10 frames selected","mask":"name *","frames":"1:10000:10","format":"netcdf"}' http://ms2/download
