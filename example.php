<?php

spl_autoload_register(function ($class) {

	include ('lib/'.$class.'.php');
});

$width=300;
$height=300;
$filename="example.png";

$i=imagecreatetruecolor($width, $height);
//imagealphablending($i, true);
imagesavealpha($i, true);

//$s=new SpiralArc($i, 1.5707963267949, 3.1415926535898, 5, 15);
//$s=new Spiral($i, 2.5*pi(), (2*pi())+2, 10, 10);
$s=new Spiral($i, 1, (7*pi())+1, 20, 0);
//$s=new SpiralArc($i, 0, (7*pi())+2, 10, 10);
// $green=imagecolorallocate($i, 0, 255, 0);
// $gray=imagecolorallocate($i, 128, 128, 128);
//$s->draw(128,128,128, true);
$s->draw();
//$s->draw_corners();

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
