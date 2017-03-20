#!/usr/bin/perl
use strict;
use Data::Dumper;
#
print '<?xml version="1.0"?>
<!DOCTYPE rdf:RDF [
    <!ENTITY owl "http://www.w3.org/2002/07/owl#" >
    <!ENTITY xsd "http://www.w3.org/2001/XMLSchema#" >
    <!ENTITY rdfs "http://www.w3.org/2000/01/rdf-schema#" >
    <!ENTITY rdf "http://www.w3.org/1999/02/22-rdf-syntax-ns#" >
]>
<rdf:RDF xmlns="http://bsc.es/parm#"
     xml:base="http://bsc.es/parm"
     xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns:owl="http://www.w3.org/2002/07/owl#"
     xmlns:xsd="http://www.w3.org/2001/XMLSchema#"
     xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">
    <owl:Ontology rdf:about="http://bsc.es/parm">
        <rdf:langRange xml:lang="en">en</rdf:langRange>
    </owl:Ontology>
';
my $entries = {};
my $ids = [];
while (<>) {
    chomp;
    my ($id, $label, $descr) = split /\t/;
    $entries->{$id} = {'id'=>$id,'label'=>$label,'descr'=>$descr};
    if (length($id) > 2) {
        $entries->{$id}->{'parent'} = substr($id,0,length($id)-2);
    } else {
        $entries->{$id}->{'parent'} = '';
    }
    push @$ids, $id;
}
#print Dumper($entries);
#exit;
foreach my $i (@$ids) {
    print "\n<!-- http://bsc.es/parm#$entries->{$i}->{'label'}  id: $i--> 
<owl:Class rdf:about=\"http://bsc.es/parm#$entries->{$i}->{'label'}\">
";
    if ($entries->{$i}->{'parent'}) {
        print "<rdfs:subClassOf rdf:resource=\"http://bsc.es/parm#$entries->{$entries->{$i}->{'parent'}}->{'label'}\"/>\n";
    }
#    else {
#        print "<rdfs:subClassOf rdf:resource=\"http://bsc.es/parm#Thing\"/>\n";
#    }
    print "</owl:Class>\n";
}
print "</rdf:RDF>\n";