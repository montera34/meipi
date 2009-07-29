<?php
	$skipIdMeipiCheck=TRUE;
	$configsPath = "../";
  require_once("../functions/meipi.php");
//from googlemaps api group: jpf...@gmail.com
header ("Content-type: image/png");

// String to print in the label
$string = $webTitle;

// Replace invalid characters
$string = str_replace("&rsquo;", "&#039;", $string);

// Decode string
$string = stripSlashes(htmlDecode($string));

$cat = $_GET["cat"];
$stand = $_GET["stand"];
$width = $_GET["width"];
$height = $_GET["height"];

$md5 = md5($string); //just so we don't convert valid text into a +
$string = str_replace("^+", $md5, $string); //replaces ^+ with long, unnatural string
$string = str_replace("+", " ", $string); //replaces + with space
$string = str_replace($md5, "+", $string); //replaces the long, unnatural string with +

$iconFontWithPath = $baseFolder.$iconFont;

// place icon into box
$posX = 4;
//$posY = $height - 5;
$posY = $height - 5 - $iconOffsetH;

$image = @imagecreate($width, $height);

switch($cat)
{
	case "1":
		$black = imagecolorallocatealpha($image, 153, 255, 153, 50); //background
		//$white = imagecolorallocatealpha($image, 0, 136, 0, 10);
		$white = imagecolorallocatealpha($image, 0, 136, 0, 0);
		//$shadow = imagecolorallocatealpha($image, 127, 127, 127, 10);
		$polygonColor = imagecolorallocatealpha($image, 153, 255, 153, 50); 
		break;
	case "2":
		$black = imagecolorallocatealpha($image, 255, 153, 153, 50); //background
		$white = imagecolorallocatealpha($image, 255, 0, 0, 0);
		//$shadow = imagecolorallocatealpha($image, 127, 127, 127, 10);
		$polygonColor = imagecolorallocatealpha($image, 255, 153, 153, 50); 
		break;
	case "3":
		//$black = imagecolorallocate($image, 255, 255, 153); //background
		//$white = imagecolorallocate($image, 136, 0, 0);
		$black = imagecolorallocatealpha($image, 255, 255, 153, 50); //background
		$white = imagecolorallocatealpha($image, 136, 0, 0, 0);
		//$shadow = imagecolorallocatealpha($image, 127, 127, 127, 10);
		$polygonColor = imagecolorallocatealpha($image, 255, 255, 153, 50); 
		break;
	case "4":
		$black = imagecolorallocatealpha($image, 153, 153, 255, 50); //background
		$white = imagecolorallocatealpha($image, 0, 0, 136, 0);
		//$shadow = imagecolorallocatealpha($image, 127, 127, 127, 10);
		$polygonColor = imagecolorallocatealpha($image, 153, 153, 255, 50); 
		break;
	case "5": //para probar
		$black = imagecolorallocatealpha($image, 153, 153, 255, 50); //background
		$white = imagecolorallocatealpha($image, 0, 0, 136, 0);
		//$shadow = imagecolorallocatealpha($image, 127, 127, 127, 10);
		break;
	default:
		$black = imagecolorallocate($image, 0, 0, 0); //background
		$white = imagecolorallocate($image, 255, 255, 255);
		//$shadow = imagecolorallocate($image, 127, 127, 127);
		break;
}

$realWhite = imagecolorallocate($image, 255, 255, 255);

$transparent = imagecolortransparent($image, $black); //si queremos el fondo transparente

$aPolygon = array(
	0, 0,
	$width-1, 0,
	$width-1, $height-5,
	$width-5, $height-5,
	$width-9, $height-1,
	$width-13, $height-5,
	0, $height-5
);
$iPoints = 7;

if ($stand=="1")
{
	imagefilledpolygon($image,$aPolygon,$iPoints,$white);
	imagepolygon($image,$aPolygon,$iPoints,$white);
	imagettftext($image, $iconFontSize, 0, $posX, $posY, $realWhite, $iconFontWithPath, $string);
}
else
{
	imagefilledpolygon($image,$aPolygon,$iPoints,$polygonColor);
	imagepolygon($image,$aPolygon,$iPoints,$white);
	imagettftext($image, $iconFontSize, 0, $posX, $posY, $white, $iconFontWithPath, $string);
}

//imageellipse($image, $width/2, $height-3, 5, 5, $white);
//imagestring($image, $font, 2, 2, $string, $shadow);
//imagestring($image, $font, 1, 1, $string, $white);
imagepng($image);
imagedestroy($image);
?> 
