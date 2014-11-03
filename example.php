<?php
// Autoloader for finding classes
spl_autoload_register(function ($class) {

	include ('lib/'.$class.'.php');
});

// Parameters
$width=200;
$height=200;
$filename="example.png";

// Create a transparent image with alpha blending
$i=imagecreatetruecolor($width, $height);
imagealphablending($i, false);
$transparent=imagecolorallocatealpha($i, 0,0,0,127);
imagefilledrectangle($i, 0, 0, $width, $height, $transparent);
imagecolordeallocate($i, $transparent);
imagealphablending($i, true);

//$s=new SpiralArc($i, 1.5707963267949, 3.1415926535898, 5, 15);
//$s=new Spiral($i, 2.5*pi(), (2*pi())+2, 10, 10);
//$s=new SpiralArc($i, 0, (7*pi())+2, 10, 10);
// $green=imagecolorallocate($i, 0, 255, 0);
//$s->draw(128,128,128, true);
//$s->draw_corners();

// Draw three spirals
$s=new Spiral($i, 1, (3*pi())+3, 20, 0);
$s->draw(0,255,0);


$s=new Spiral($i, 3*pi()+3, 5*pi(), 20, 0);
$s->draw(255,0,0);


$s=new Spiral($i, 5*pi(), (7*pi())+1, 20, 0);
$s->draw(0,0,255);

// Delete old file, if it exists
if(file_exists($filename)) {

	unlink($filename);
}

// Create new file
imagesavealpha($i, true);
$result=imagepng($i,$filename);
if($result) {

	print("<img src='example.png' width=$width height=$height>");

} else {

	print ("Error");
}
imagedestroy($i);

?>
