#!/usr/bin/perl -w

use strict;

#
# Generating CATH tree for TransAtlas Web Cath Browser.
# Using libTransAtlas.pl database configuration.
# Caution! It needs a lot of memory, don't execute it in a Desktop machine...
#   

require "libTransAtlas.pl";

my $long=@ARGV;
if ($long!=0){
    print "Usage: perl $0 \n";
    print "Caution! It needs a lot of memory, don't execute it in a Desktop machine...\n";
    exit(0);
}

#my ($file)=@ARGV;

my $dbh=&connectDB;

my %pdbDesc;
my $select = "select idCode,title from Pdb";
if (my $data=$dbh->selectall_hashref($select,'idCode')){

        foreach my $f (values %$data) {
                my $id = $$f{'idCode'};
                my $desc = $$f{'title'};
		$desc =~s/\"/\'/g;
		my $mid = lc($id);
                $pdbDesc{$mid} = $desc;
        }
}

print "END loading PDB descriptions...\n";

my %cathDesc;
$select = "select nodeId,Description from CathDescription;";
if (my $data=$dbh->selectall_hashref($select,'nodeId')){

        foreach my $f (values %$data) {
                my $id = $$f{'nodeId'};
                my $desc = $$f{'Description'};

		$cathDesc{$id} = $desc;
		#print "$id -> $desc \n";
	}
}

#print "END loading Cath descriptions...\n";

my %cathPdbs;
my %counts;
my %done1;
my %done2;
my %done3;
$select = "select idCode,class,architecture,topology from Cath c, Simulation s where (c.idCode = s.idCode_orig) or (c.idCode = s.idCode_target);";
if (my $data=$dbh->selectall_arrayref($select,{ Slice => {} })){

        foreach my $f (@$data) {
                my $idCode = $$f{'idCode'};
                my $class = $$f{'class'};
                my $arch = $$f{'architecture'};
                my $topl = $$f{'topology'};
		my $code = "$class.$arch.$topl";
		$cathPdbs{$code}->{$idCode} = 1;
		$counts{$class}++ if(!$done1{$class}->{$idCode});
		$counts{"$class.$arch"}++ if(!$done2{"$class.$arch"}->{$idCode});
		$counts{"$class.$arch.$topl"}++ if(!$done3{"$class.$arch.$topl"}->{$idCode});
		$done1{$class}->{$idCode} = 1;
		$done2{"$class.$arch"}->{$idCode} = 1;
		$done3{"$class.$arch.$topl"}->{$idCode} = 1;
		
	}
}

#print "END loading pdbs...\n";

my %hash4;
$select = "select distinct class,architecture,topology,homology from Cath c ,Simulation s where c.idCode=s.idCode_orig or c.idCode=s.idCode_target order by class,architecture,topology,homology";

if (my $data=$dbh->selectall_arrayref($select,{ Slice => {} })){

        foreach my $f (@$data) {
                my $class = $$f{'class'};
                my $arch = $$f{'architecture'};
                my $topl = $$f{'topology'};
                my $homl = $$f{'homology'};
		my $code = "$class.$arch.$topl.$homl";
		$hash4{$class}->{$arch}->{$topl}->{$homl} = 1;
	}
}

#print "END loading Cath tree...\n";


#print "Beggining HTML construction:\n";

#var gData=[{"data":"root1",
#            "children":[
#                        {
#                            "data":"node1",
#                            "children":[
#                                {
#                                    "data":"node2"
#                                },
#                                {
#                                    "data":"node3"
#                                }
#                            ]
#                        },

#print "<div id='treeViewDiv'>\n";

print "var gData=[";
#print "\t<ul>\n";

my $code;
foreach my $f (sort {$a <=> $b} keys %hash4){
	#print "$f - $cathDesc{$f}\n";
	my $c1 = $counts{$f};
	print "{\"data\":\"$cathDesc{$f} ($c1)\",\"children\":[ \n";
	foreach my $f2 (sort {$a <=> $b} keys %{$hash4{$f}}){
		$code = "$f.$f2";
		my $c2 = $counts{$code};
		print "{\"data\":\"$cathDesc{$code} ($c2)\",\"children\":[ \n";
		#print "$code - $cathDesc{$code}\n";
		#print "\t\t\t\t<li>$cathDesc{$code}\n";
		#print "\t\t\t\t\t<ul>\n";
		foreach my $f3 (sort {$a <=> $b} keys %{$hash4{$f}->{$f2}}){
			$code = "$f.$f2.$f3";
			my $c3 = $counts{$code};
			print "{\"data\":\"$cathDesc{$code} ($c3)\",\"children\":[ \n";
			#print "$code - $cathDesc{$code}\n";
			#print "\t\t\t\t\t\t<li>$cathDesc{$code}\n";
			#print "\t\t\t\t\t\t\t<ul>\n";

			foreach my $pdb (sort keys %{$cathPdbs{$code}}){
				#print "$pdb ";
				print "{\"data\":\"<strong>$pdb</strong> <i>$pdbDesc{$pdb}</i>\"},\n";
				#print "\t\t\t\t\t\t\t\t<li>$pdb</li>\n";
			}
			#print "\t\t\t\t\t\t\t</ul>\n";
			#print "\t\t\t\t\t\t</li>\n";
			print " ] },\n";		
			#print "\n";

			#foreach my $f4 (sort {$a <=> $b} keys %{$hash4{$f}->{$f2}->{$f3}}){
			#	$code = "$f.$f2.$f3.$f4";
			#	print "$code - $cathDesc{$code}\n";
			#}
		}
		print "] },\n";		
		#print "\t\t\t\t\t</ul>\n";
		#print "\t\t\t\t</li>\n";
	}
	print "] },\n";		
	#print "\t\t\t</ul>\n";
	#print "\t\t</li>\n";
}
print "]\n";		
#print "\t\t</ul>\n";
#print "\t</li>\n";
#print "</ul>\n";
#print "</div>\n";


$dbh->disconnect;
