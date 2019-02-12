<?php

require __DIR__ . '/../phplib/globals.inc.php';
require __DIR__ . '/../vendor/json-schema/vendor/autoload.php';

/*
    require __DIR__ . '/../vendor/json-schema2/vendor/autoload.php';

use Swaggest\JsonSchema\Structure\ClassStructure;
use Swaggest\JsonSchema\Schema;

$_REQUEST['json'] = <<<'JSON'
{
    "type": "object",
    "properties": {
        "id": {
            "type": "integer"
        },
        "name": {
            "type": "string"
        },
        "orders": {
            "type": "array",
            "items": {
                "$ref": "#/definitions/order"
            }
        }
    },
    "required":["id"],
    "definitions": {
        "order": {
            "type": "object",
            "properties": {
                "id": {
                    "type": "integer"
                },
                "price": {
                    "type": "number"
                },
                "updated": {
                    "type": "string",
                    "format": "date-time"
                }
            },
            "required":["id"]
        }
    }
}
JSON;

var_dump($_REQUEST['json']);

$schema = Schema::import(json_decode($_REQUEST['json']));

var_dump($schema);


//https://github.com/swaggest/php-json-schema

die();

*/


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
