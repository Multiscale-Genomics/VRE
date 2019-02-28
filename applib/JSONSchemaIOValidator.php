<?php

require "../phplib/genlibraries.php";

$data = json_decode($_REQUEST['json']);

// Validate
$validator = new JsonSchema\Validator();
//$validator->check($data, (object) array('$ref' => 'file://' . realpath('tool_schema_dev.json')));
$validator->check($data, (object) array('$ref' => 'file://'.$GLOBALS['tool_io_json_schema']));
//$validator->check($data, (object) array('$ref' => 'https://raw.githubusercontent.com/Multiscale-Genomics/VRE_tool_jsons/master/tool_specification/tool_schema_dev.json'));

if ($validator->isValid()) {
    echo '{"status":1, "msg":"<p class=\"font-green bold\">The supplied JSON validates against the schema. Please, click the submit button on the bottom of the form and go the next step (create test).</p>"}';
} else {
    echo '{"status":0, "msg":"<p class=\"font-red bold\">JSON does not validate.</p><p>Violations:<p><ul>';
    foreach ($validator->getErrors() as $error) {
        echo sprintf('<li style=\"word-wrap:break-word;\"><span class=\"font-green bold\">%s</span>: %s</li>', $error['property'], addslashes($error['message']));
    }
		echo '</ul>"}';
}
