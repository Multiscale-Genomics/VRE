#!/usr/bin/perl -w

use strict;
use MongoDB;
use MongoDB::OID;
use Data::Dumper;

my $long=@ARGV;
if ($long != 0 ){
    print "Usage: perl $0\n";
    exit(0);
}
#my ($idSim) =@ARGV;

# Variables init.
#$idSim =~ s/\///;
#my $baseDir = "/mmb/NAFlex-Data/NAFlex_parmBSC1/$idSim";
my $GROUPS = {};
my $analTypes = {};

# Mongo Connection and collection definitions
#my $mconn = MongoDB::MongoClient->new(host=> '127.0.0.1');
my $mconn = MongoDB::MongoClient->new(host=> 'ms1.mmb.pcb.ub.es');
$mconn->authenticate("admin","dataLoader","mdbwany2015");
$mconn->query_timeout(1000000);
my $db = $mconn->get_database('NUCMDANAL');
my $simDataCol = $db->get_collection("simData");

my $st = $simDataCol->find({})->fields({'PDB'=>1,NucType=>1,Ligands=>1,moleculeType=>1,Chains=>1,SubType=>1,IonicConcentration=>1,sequence=>1});

my %hash;
my %count;
while (my $doc = $st->next) {
	#	{ "_id" : "NAFlex_TCGAmet", "sequence" : "CGCGTXGACGCG", "SubType" : "B", "Ligands" : "No", "IonicConcentration" : "Electroneutralidad", "Chains" : "duplex", "NucType" : "DNA", "moleculeType" : "Dna", "PDB" : "No" }
	#print Dumper($doc);
	
	#$VAR1 = {
        #  'sequence' => 'CGCGCGCGCG',
        #  '_id' => 'NAFlex_Z-DNA-4M',
        #  'Chains' => 'duplex',
        #  'IonicConcentration' => '4M',
        #  'NucType' => 'DNA',
        #  'moleculeType' => 'Dna',
        #  'SubType' => 'Z',
        #  'PDB' => '1I0T',
        #  'Ligands' => 'No'
        #};

	my $seq = $doc->{'sequence'};
	my $id = $doc->{'_id'};
	my $ch = $doc->{'Chains'};
	my $ion = $doc->{'IonicConcentration'};
	my $type1 = $doc->{'NucType'};
	my $type = $doc->{'moleculeType'};
	my $subtype = $doc->{'SubType'};
	my $pdb = $doc->{'PDB'};
	my $lig = $doc->{'Ligands'};

	$seq =~ s/\"/\\"/g;
	$id =~ s/\"/\\"/g;
	$ch =~ s/\"/\\"/g;
	$ion =~ s/\"/\\"/g;
	$type1 =~ s/\"/\\"/g;
	$type =~ s/\"/\\"/g;
	$subtype =~ s/\\"/\"/g;
	$pdb =~ s/\"/\\"/g;
	$lig =~ s/\"/\\"/g;

	# Ontology: 
	# Type (DNA/Complex/Hairpin/...)
	# |___	SubType (B/A/Z/...)
	# 	|____	Ionic Conc / BRRRRR.... :(
	#
	$hash{$type}->{$ch}->{$subtype}->{$ion}->{$pdb}->{$seq}->{$id} = 1;

	$count{"$type"}++;
	$count{"$type-$ch"}++;
	$count{"$type-$ch-$subtype"}++;
	$count{"$type-$ch-$subtype-$ion"}++;
	$count{"$type-$ch-$subtype-$ion-$pdb"}++;
	$count{"$type-$ch-$subtype-$ion-$pdb-$seq"}++;

	#print "$doc->{'sequence'}\n";

}

print "var gData=[";
foreach my $type (sort keys %hash){
	#print "$type\n";
	my $c = $count{"$type"};
        print "{\"data\":\"$type ($c)\",\"children\":[ \n";
	foreach my $ch (sort keys %{$hash{$type}}){
		#print "\t$ch\n";
		my $c = $count{"$type-$ch"};
                print "{\"data\":\"$ch ($c)\",\"children\":[ \n";
		foreach my $subtype (sort keys %{$hash{$type}->{$ch}}){
			#print "\t\t$subtype\n";
			my $c = $count{"$type-$ch-$subtype"};
                	print "{\"data\":\"$subtype ($c)\",\"children\":[ \n";
			foreach my $ion (sort keys %{$hash{$type}->{$ch}->{$subtype}}){
				#print "\t\t\t$ion\n";
				my $c = $count{"$type-$ch-$subtype-$ion"};
                		print "{\"data\":\"$ion ($c)\",\"children\":[ \n";
				foreach my $pdb (sort keys %{$hash{$type}->{$ch}->{$subtype}->{$ion}}){
					#print "\t\t\t\t$pdb\n";
					my $c = $count{"$type-$ch-$subtype-$ion-$pdb"};
                			print "{\"data\":\"$pdb ($c)\",\"children\":[ \n";
					foreach my $seq (sort keys %{$hash{$type}->{$ch}->{$subtype}->{$ion}->{$pdb}}){
						#print "\t\t\t\t\t$seq\n";
						my $c = $count{"$type-$ch-$subtype-$ion-$pdb-$seq"};
                				print "{\"data\":\"$seq ($c)\",\"children\":[ \n";
						foreach my $id (sort keys %{$hash{$type}->{$ch}->{$subtype}->{$ion}->{$pdb}->{$seq}}){
							#print "\t\t\t\t\t\t$id\n";
							print "{\"data\":\"<strong>$id</strong>\"},\n";
						}
						print " ] },\n";
					}
					print " ] },\n";
				}
				print " ] },\n";
			}
			print " ] },\n";
		}
		print " ] },\n";
	}
	print " ] },\n";
}	
print " ]\n";


print << "EOF"

function initiate_jstree() {

    \$("#tree_container").jstree({
        "core": {
            "data": getTree,
            "themes":{
                "icons":false
            },
        },
        "plugins": [ "themes", "ui" ,"types"]

    });

}
function makeTreeData(node){
    if(node.original && node.original.actualData){
        data=node.original.actualData;
    }else{
        data=gData;
    }
    var treeData=[];
    for(i=0;i<data.length;i++){
        var iter=data[i];
        var item={"text": iter.data};
        if(iter.children){
            item["children"]=true;
            item["actualData"]=iter.children;
        }
        treeData.push(item);
    }
    return treeData;
}
var getTree = function (obj, cb) {
//    console.log("called");
    data=makeTreeData(obj);
    cb.call(this,data);
}

EOF
