<?php

require __DIR__ . '/../vendor/json-schema/vendor/autoload.php';

$data = json_decode($_REQUEST['json']);

// Validate
$validator = new JsonSchema\Validator();
//$validator->check($data, (object) array('$ref' => 'file://' . realpath('tool_schema_dev.json')));
$validator->check($data, (object) array('$ref' => 'https://raw.githubusercontent.com/Multiscale-Genomics/VRE_tool_jsons/master/tool_specification/tool_schema_dev.json'));

if ($validator->isValid()) {
    echo "<p class='font-green bold'>The supplied JSON validates against the schema.</p>";
} else {
    echo "<p class='font-red bold'>JSON does not validate.</p><p>Violations:<p><ul>";
    foreach ($validator->getErrors() as $error) {
        echo sprintf("<li><span class='font-green bold'>%s</span>: %s</li>", $error['property'], $error['message']);
    }
		echo "</ul>";
}
