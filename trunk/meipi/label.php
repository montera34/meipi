<?php
	$skipIdMeipiCheck=TRUE;
	$skipSession=TRUE;
	//$configsPath = "../";
	require_once("functions/meipi.php");

// cache headers based on http://lists.nyphp.org/pipermail/talk/2006-June/018479.html
$file_hash = md5($_SERVER["QUERY_STRING"]);
$file_time = "label.php";

#Possibly we could the file in the browsers cache
$headers = $_SERVER;//apache_request_headers();
$file_hash = md5_file($file_time);

if (isset($headers['HTTP_IF_NONE_MATCH']) && ereg($file_hash, $headers['HTTP_IF_NONE_MATCH']))
{
	header("HTTP/1.1 304 Not Modified");
	exit;
}

if (isset($headers['HTTP_IF_MODIFIED_SINCE']) && (strtotime($headers['HTTP_IF_MODIFIED_SINCE']) == filemtime($file_time)))
{
	#Etag should always hit first... but just in case
	header("HTTP/1.1 304 Not Modified");
	exit;
}

// No cached in browser -> set headers to cache for next time
header("Etag: \"$file_hash\"");
header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file_time)).' GMT');
header("Expires: ".gmdate('D, d M Y H:i:s', time()+(7 * 24 * 60 * 60)).' GMT');
header("Pragma: public");

//from googlemaps api group: jpf...@gmail.com
header ("Content-type: image/png");

// String to print in the label
$string = $_GET["text"];

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
		$black = imagecolorallocatealpha($image, 255, 204, 0, 50); //background
		//$white = imagecolorallocatealpha($image, 0, 136, 0, 10);
		$white = imagecolorallocatealpha($image, 255, 108, 0, 0);
		//$shadow = imagecolorallocatealpha($image, 127, 127, 127, 10);
		$polygonColor = imagecolorallocatealpha($image, 255, 204, 0, 50); 
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
		$black = imagecolorallocatealpha($image, 51, 173, 194, 50); //background
		$white = imagecolorallocatealpha($image, 0, 75, 118, 0);
		//$shadow = imagecolorallocatealpha($image, 127, 127, 127, 10);
		$polygonColor = imagecolorallocatealpha($image, 0, 171, 171, 50); 
		break;
	
	case "4":
		$black = imagecolorallocatealpha($image, 186, 232, 118, 50); //background
		$white = imagecolorallocatealpha($image, 85, 144, 0, 0);
		//$shadow = imagecolorallocatealpha($image, 127, 127, 127, 10);
		$polygonColor = imagecolorallocatealpha($image, 186, 232, 118, 50); 
		break;
	
	case "5": //para probar
		$black = imagecolorallocatealpha($image, 196, 214, 151, 50); //background
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

imagepng($image);
imagedestroy($image);
?> 
