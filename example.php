<?php

spl_autoload_register(function ($class) {

	include ('lib/'.$class.'.php');
});

$width=300;
$height=300;
$filename="example.png";

imagealphablending($i, false);
imagesavealpha($i, true);
$i=imagecreatetruecolor($width, $height);

$s=new SpiralArc($i, 0, pi()/2, 5, 15);
$green=imagecolorallocate($i, 0, 255, 0);
$gray=imagecolorallocate($i, 128, 128, 128);
//$s->draw(128,128,128, true);
$s->draw();

// Delete old file, if it exists
if(file_exists($filename)) {

	unlink($filename);
}

// Create new file
$result=imagepng($i,$filename);
if($result) {

	print("<img src='example.png' width=$width height=$height style='border:1px solid gray'>");

} else {

	print ("Error");
}
imagedestroy($i);

?>
