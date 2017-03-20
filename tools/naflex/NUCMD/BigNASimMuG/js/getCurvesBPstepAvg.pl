#!/usr/bin/perl -w

use strict;
use MongoDB;
use MongoDB::OID;
use Data::Dumper;

my $long=@ARGV;
if ($long != 1 ){
    print "Usage: perl $0 <idSim>\n";
    exit(0);
}
my ($idSim) =@ARGV;

# Variables init.
$idSim =~ s/\///;
my $baseDir = "/mmb/NAFlex-Data/NAFlex_parmBSC1/$idSim";
my $GROUPS = {};
my $analTypes = {};

# Mongo Connection and collection definitions
my $mconn = MongoDB::MongoClient->new(host=> '127.0.0.1');
my $db = $mconn->get_database('NUCMDANAL');
my $analDataCol = $db->get_collection("analData");

# Getting Curvesness data
my %avgCurves;
my %avgCurvesPair;

my $st = $analDataCol->find({"_id.idSim" => qr/$idSim/, "_id.nSnap" => 0, "_id.nGroup" => { '$ne' => 0}})->fields({"CURVES.helical_bpstep" =>  "1"})->sort({'_id.nGroup' => 1});

while (my $doc = $st->next) {

	#$VAR1 = {
        #  'CURVES' => {
        #              'helical_bpstep' => {
        #                                  'twist_avg' => [
        #                                                 '36.77',
        #                                                 '4.6'
        #                                               ],
        #                                  'roll_avg' => [
        #                                                '2.23',
        #                                                '5.5'
        #                                              ],
        #                                  'tilt_avg' => [
        #                                                '-0.99',
        #                                                '4.1'
        #                                              ],
        #                                  'shift_avg' => [
        #                                                 '0.13',
        #                                                 '0.7'
        #                                               ],
        #                                  'slide_avg' => [
        #                                                 '-0.92',
        #                                                 '0.7'
        #                                               ],
        #                                  'rise_avg' => [
        #                                                '3.68',
        #                                                '0.3'
        #                                              ]
        #                                }
        #            },
        #  '_id' => {
        #           'nSnap' => 0,
        #           'idSim' => 'NAFlex_lks1',
        #           'nGroup' => 2,
        #           'idGroup' => 'CCGG'
        #         }
        #};


	if($doc->{'CURVES'}->{'helical_bpstep'}){
		my $id = $doc->{'_id'}->{'idSim'};
		my $nGroup = $doc->{'_id'}->{'nGroup'};
		my $idGroup = $doc->{'_id'}->{'idGroup'};

		foreach my $param (sort keys %{$doc->{'CURVES'}->{'helical_bpstep'}}){
			my $v = $doc->{'CURVES'}->{'helical_bpstep'}->{$param}[0];
			my $stdev = $doc->{'CURVES'}->{'helical_bpstep'}->{$param}[1];

			push @{$avgCurves{$idGroup}->{$param}->{'a'}},$v;
			$avgCurves{$idGroup}->{$param}->{'v'} += $v;
			$avgCurves{$idGroup}->{$param}->{'n'} ++;
	
			my $pair = substr($idGroup,0,2);
			push @{$avgCurvesPair{$pair}->{$param}->{'a'}},$v;
			$avgCurvesPair{$pair}->{$param}->{'v'} += $v;
			$avgCurvesPair{$pair}->{$param}->{'n'} ++;

			print "$id $idGroup $nGroup $param: $v\n";
		}
	}
}

print "########## AVG BP_Step ########### \n";
foreach my $f (sort keys %avgCurves){
	foreach my $param (sort keys %{$avgCurves{$f}}){
		my $n = $avgCurves{$f}->{$param}->{'n'};
		my $v = $avgCurves{$f}->{$param}->{'v'};
		my $a = $avgCurves{$f}->{$param}->{'a'};

		my $avg = $n?$v/$n:0;

		my $stdev = 0;
		foreach my $va (@{$a}){
	                my $add = $va - $avg;
        	        my $add2 = $add * $add;
                	$stdev += $add2;
	        }

	        $stdev /= $n if($n);
        	$stdev = sqrt($stdev);

		print "$f $v ($n) Avg: $avg ($stdev)\n";
	}
}

print "########## AVG Pair ########### \n";
foreach my $f (sort keys %avgCurvesPair){
	foreach my $param (sort keys %{$avgCurvesPair{$f}}){
		my $n = $avgCurvesPair{$f}->{$param}->{'n'};
		my $v = $avgCurvesPair{$f}->{$param}->{'v'};
		my $a = $avgCurvesPair{$f}->{$param}->{'a'};

		my $avg = $n?$v/$n:0;

		my $stdev = 0;
		foreach my $va (@{$a}){
	                my $add = $va - $avg;
        	        my $add2 = $add * $add;
                	$stdev += $add2;
	        }

        	$stdev /= $n if($n);
	        $stdev = sqrt($stdev);

		print "$f $v ($n) Avg: $avg ($stdev)\n";
	}
}

