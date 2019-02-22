<?php

require "../phplib/genlibraries.php";
require "../phplib/admin.inc.php";

//**************************
redirectToolDevOutside();
//**************************

// Set the content-type
//header('Content-Type: image/png');

$toolid = $_GET["toolid"];

generateLogo($toolid);

/*$result = $GLOBALS['toolsDevMetaCol']->findOne(array("_id" => $toolid));

$path = __DIR__ . "/../files/".$result["user_id"]."/.dev/".$toolid."/logo/";
if(!file_exists($path)) mkdir($path);

$text = $result["step3"]["tool_spec"]["name"];
$tsize = strlen($text);
if($tsize < 5) $tsize = 5;

// image size
$w = 600;
$h = 600;
// Create the image
$im = imagecreatetruecolor($w, $h);

// Create some colors
$background = imagecolorallocate($im, 255, 255, 255);
$color = imagecolorallocate($im, 0, 107, 143);

imagefilledrectangle($im, 0, 0, $w, $w, $background);

// Font size
$fsize = intval(1500/$tsize);
// The text to draw
$text = strtoupper($text);
// Replace path by your own font path
$font = __DIR__.'/../assets/global/fonts/Deutschlander.ttf';

// calculating x-position
$tb = imagettfbbox($fsize, 0, $font, $text);
$x = ceil(($w - $tb[2]) / 2); // lower left X coordinate for text

$y = ($h/2)+((abs($tb[5] - $tb[1]))/2);

// Add the text
imagettftext($im, $fsize, 0, $x, $y, $color, $font, $text);

// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im, $path.'logo.png');
imagedestroy($im);*/

redirect("/admin/myNewTools.php");
