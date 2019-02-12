<?php

require __DIR__ . '/../phplib/globals.inc.php';
#require __DIR__ . '/../vendor/json-schema/vendor/autoload.php';

require __DIR__ . '/../vendor/json-schema2/vendor/autoload.php';

use Swaggest\JsonSchema\Structure\ClassStructure;
use Swaggest\JsonSchema\Schema;

$_REQUEST['json'] = <<<'JSON'
{
    "_id": "nucldynwf_2",
    "input_files": [
        {
            "name": "MNaseSeq",
      
            "description": "MNase-seq reads",
            "help": "MNase aligned reads in which nucleosome analyses are to be carried out. Multiple files can be given here, and for each, the selected analyses are going to be performed.",
            "file_type": [
                "BAM"
            ],
            "data_type": [
                "data_mnase_seq"
            ],
            "required": true,
            "allow_multiple": true
        },
        {
            "name": "condition1",
            "description": "MNase-seq reference state (condition C1)",
            "help": "MNase data used to define the initial state  when comparing nucleosome positioning",
            "file_type": [
                "BAM"
            ],
            "data_type": [
                "data_mnase_seq"
            ],
            "required": false,
            "allow_multiple": false
        },
        {
            "name": "condition2",
            "description": "MNAse-seq final state (condition C2)",
            "help": "MNase data used to define the final state  when comparing nucleosome positioning",
            "file_type": [
                "BAM"
            ],
            "data_type": [
                "data_mnase_seq"
            ],
            "required": false,
            "allow_multiple": false
        }
    ],
    "input_files_combinations": [
        {
            "description": "Analyse MNase-seq data",
            "input_files": [
                "MNaseSeq"
            ],
            "input_files_public_dir": [
                "refGenome_chromSizes"
            ]
        }              
    ],
    "input_files_public_dir": [
        {
            "name": "refGenome_chromSizes",
            "description": "Folder where the information about the chromosome sizes of the reference genome is found",
            "help": "Reference genome Folder",
            "type": "string",
            "value": "refGenomes\/",
            "file_type": [
                "TXT"
            ],
            "data_type": [
                "configuration_file"
            ],
            "required": true,
            "allow_multiple": false
        }
    ],
    "arguments": [
        {
            "name": "nucleR",
            "description": "NucleR",
            "help": "NucleR finds nucleosome positions from MNase experiments using Fourier transform filtering and classifies nucleosomes according to their fuzziness",
            "type": "boolean",
            "required": true,
            "allow_multiple": false,
            "default": "true"
        },
        {
            "name": "gausfitting:range",
            "type": "string",
            "description": "Genomic range",
            "help": "Genomic region to be analyzed: whole genome ('all'), entire chromosome (chromosome name i.e. 'chrX'), or region of a chromosome ('chromosomeName:start-end).",
            "required": true,
            "allow_multiple": false,
            "default": "All"
        }
    ],
    "output_files": [
        {
            "name": "NR_gff",
            "required": false,
            "allow_multiple": true,
            "file": {
                "file_type": "GFF3",
                "data_type": "nucleosome_positioning",
                "compressed": "null",
                "meta_data": {
                    "description": "Nuclesome positions predicted by NucleR from MNase-seq data",
                    "tool": "nucldynwf",
                    "visible": true
                }
            }
        },
        {
            "name": "statistics",
            "required": true,
            "allow_multiple": false,
            "file": {
                "file_type": "TAR",
                "data_type": "tool_statistics",
                "compressed": "gzip",
                "meta_data": {
                    "description": "Statistical data from nucleosome analysis workflow",
                    "tool": "nucldynwf",
                    "visible": false
                }
            }
        }
    ]
}
JSON;
$data = json_decode($_REQUEST['json']);

var_dump($data);

print "\n<br/>############################";
print "\n<br/>############################";
print "\n<br/>############################";

#$schema = Schema::import(json_decode($_REQUEST['json']));
$schema = Schema::import('https://raw.githubusercontent.com/Multiscale-Genomics/VRE_tool_jsons/dev/tool_specification/tool_schema_io.json');

//$schema->in($data);


try {
    $schema->in($data); // exception for int
    print "bravo!!";

//} catch (InvalidValue $exception) {
} catch (ObjectException $exception) {
    $expected = <<<'TEXT'
Swaggest\JsonSchema\Exception\Error Object
(
    [error] => String expected, 1 received
    [schemaPointers] => Array
        (
            [0] => /properties/sample/$ref
            [1] => file://baseTypes.json#/stringFromOutside
        )
    [dataPointer] => /sample
    [processingPath] => #->properties:sample->$ref[file~2//baseTypes.json#/stringFromOutside]
    [subErrors] => 
)
TEXT;
    print $expected;
print "\n<br/>############################";
print "\n<br/>############################";
print "\n<br/>############################";
    var_dump($exception->inspect(), 1);
print "\n<br/>############################";
print "\n<br/>############################";
print "\n<br/>############################";
}



/*$schema->in(json_decode(<<<'JSON'
{
    "id": 1,
    "name":"John Doe",
    "orders":[
        {
            "id":1
        },
        {
            "price":1.0
        }
    ]
}
JSON
));
 */


print "\n<br/>############################";
print "\n<br/>############################";
print "\n<br/>############################";
print "\n<br/>############################";
var_dump($schema);


//https://github.com/swaggest/php-json-schema

die();


$data = json_decode($_REQUEST['json']);

// Validate
$validator = new JsonSchema\Validator();
//$validator->check($data, (object) array('$ref' => 'file://' . realpath('tool_schema_dev.json')));
$validator->check($data, (object) array('$ref' => 'file://'.$GLOBALS['tool_json_schema']));
//$validator->check($data, (object) array('$ref' => 'https://raw.githubusercontent.com/Multiscale-Genomics/VRE_tool_jsons/master/tool_specification/tool_schema_dev.json'));

if ($validator->isValid()) {
    echo '{"status":1, "msg":"<p class=\"font-green bold\">The supplied JSON validates against the schema. Please, click the submit button on the bottom of the form and go the next step (create test).</p>"}';
} else {
    echo '{"status":0, "msg":"<p class=\"font-red bold\">JSON does not validate.</p><p>Violations:<p><ul>';
    foreach ($validator->getErrors() as $error) {
        echo sprintf('<li><span class=\"font-green bold\">%s</span>: %s</li>', $error['property'], $error['message']);
    }
		echo '</ul>"}';
}
