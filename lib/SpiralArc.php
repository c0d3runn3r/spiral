
<?php

/**
 * SpiralArc
 * 
 * Represent a segment of a spiral, with both endpoints in the same cartesian quadrant 0 <= t1, t2 <= 2PI
 * This allows us to enforce certain assumptions (monotonicity in each dimension) 
 * To create a complete spiral, we will need to join several SpiralArcs together (see the Spiral class)
 *
 * @author c0d3runn3r
 */
class SpiralArc extends SpiralBase {
	

	/**
	 * Constructor
	 *
	 * @arg $i the GD image
	 * @arg $t1 theta1, the first angle
	 * @arg $t2 theta2, the second angle
	 * @arg $width the width of the spiral segment in pixels
	 * @arg $r0 the base (core) radius of the spiral when $t1==0
	 * @throw Exception on invalid args
	 */
	public function __construct($i, $t1, $t2, $width, $r0) {

		parent::__construct($i, $t1, $t2, $width, $r0);

		// Both thetas must be in same quadrant
		if(!$this->shared_quadrant($t1, $t2)) {

			throw new InvalidArgumentException("t1 and t2 must be in the same (I,II,III,IV) quadrant");			
		}

		// Both thetas must be <= 2PI
		if($t1 > 2*pi() || $t2 > 2*pi()) {

			throw new InvalidArgumentException("t1 and t2 must be <= 2PI");			
		}

	}


	/**
	 * Draw the arc
	 *
	 * @param $red [0..255]
	 * @param $green [0..255]
	 * @param $blue [0..255]
	 * @param $bounding_box if true, draws a bounding box instead of the arc
	 */
	public function draw($red=0, $green=255, $blue=0, $bounding_box=false) {

		if(gettype($this->i) != "resource") {

			throw new InvalidArgumentException("This SpiralArc does not have a valid GD image");			
		}

		// if(gettype($color) != "integer") {

		// 	throw new InvalidArgumentException("Please pass a valid GD imagecolor");			
		// }


		// Get the 4 corners of this arc segment
		$corners=$this->corners();

		// Get a bounding box
		$bounds=Point::bounding_box($corners);

		// Get the offset into the center of the image
		$offset_x=imagesx($this->i)/2;
		$offset_y=imagesy($this->i)/2;

		// Allocate versions of this color (transparencies).  Bounding box has 1 color since it is just a solid.
		$colors=array();
		if($bounding_box) {

				array_push($colors, imagecolorallocatealpha($this->i, $red, $green, $blue, 0));	

		} else {

			for($a=0; $a<128; $a++) {

				array_push($colors, imagecolorallocatealpha($this->i, $red, $green, $blue, $a));	
			}
		}
		//$redcolor=imagecolorallocatealpha($this->i, 255, 0, 0, 0);

		// Iterate over the bounding box, plus 2px padding
		for($y=floor($bounds[1]->y)-2; $y<ceil($bounds[0]->y)+2; $y++){
	
			for($x=floor($bounds[0]->x)-2; $x<ceil($bounds[1]->x)+2; $x++){

				// Bounding box sets all pixels
				if($bounding_box) {

					imagesetpixel($this->i, $offset_x+$x, $offset_y-$y, $colors[0]);

				// Otherwise, we do a hit test
				} else {

					$distance = $this->hit_test(new Point($x, $y));
					if($distance!== NULL) {

						// Pick a color based on hit distance (default to solid)
						$color=$colors[0];	

						if($distance >0){
//						if($distance ==22){

							$color=$colors[floor(127 * $distance)];
//							$color=$redcolor;
						}

						imagesetpixel($this->i, $offset_x+$x, $offset_y-$y, $color);
					}
				}
			}
		}

		// Deallocate colors
		foreach ($colors as $color) {

			imagecolordeallocate($this->i, $color);
		}


	}

	/**
	 * Draw the arc corners
	 *
	 * @param $red [0..255]
	 * @param $green [0..255]
	 * @param $blue [0..255]
	 */
	public function draw_corners($red=255, $green=255, $blue=255) {

		if(gettype($this->i) != "resource") {

			throw new InvalidArgumentException("This SpiralArc does not have a valid GD image");			
		}

		// Get the offset into the center of the image
		$offset_x=imagesx($this->i)/2;
		$offset_y=imagesy($this->i)/2;


		$corners=$this->corners();

		// Allocate a color
		$color=imagecolorallocate($this->i, $red, $green, $blue);
		foreach ($corners as $p) {
			imagesetpixel($this->i, $offset_x+$p->x, $offset_y-$p->y, $color);
		}

		imagecolordeallocate($this->i, $color);

	}
	/**
	 * Does a point lie within our arc?
	 *
	 * @param Point to test
	 * @return 0 if point lies inside.  Distance if < 1px outside.  NULL otherwise 
	 */
	public function hit_test($point){

		// Convert point to polar
		$polar=$point->get_polar();
		$rho=$polar[0];
		$theta=$polar[1];

		// Get our theta values mod 2PI
		$t1=fmod($this->t1, (2*pi()));
		$t2=fmod($this->t2 , (2*pi()));

		// But make sure that t2 is still > t1 :)
		if($t2 < $t1) {

			$t2+=2*pi();
		}

		// Is this theta between our theta values?
		if($theta > $t2 || $theta < $t1) {

			return NULL;
		}

		// Figure out how much farther out t1 is than when theta = =
		$base_radius=$this->width * ($this->t1/(2*pi()));

		// Base radius starts at r0
		$base_radius+=$this->r0;

		// Radius growth is how much the radius would grow between t1 and theta
		$radius_growth=(($theta-$this->t1)/(2*pi())) * $this->width;

		$distance_from_inner_border=$rho - ($base_radius+$radius_growth);
		$distance_from_outer_border=($base_radius+$radius_growth+$this->width)-$rho;

		// Inside the border
		if($distance_from_outer_border >0 && $distance_from_inner_border >0 ) {

			return 0;
		}

		// More than 1px outside the border
		if($distance_from_outer_border < -1 || $distance_from_inner_border < -1) {

			return NULL;
		}

		// [0..1) px outside the border: $distance is (-1..0]
//		if($distance_from_outer_border < 0 || $distance_from_inner_border < 0) {
//		return 22;
		return min(abs($distance_from_inner_border), abs($distance_from_outer_border));
//		}

		// Inside the box
//		throw new Exception("Flow should never get here");
	}

	/**
	 * Corners
	 *
	 * Return the four corners of this arc segment
	 * @return array() containing four Points
	 */
	private function corners() {

		// Every time we make 2PI revolutions, inner radius increases by $width
		$base_radius=$this->width * ($this->t1/(2*pi()));

		// Base radius starts at r0
		$base_radius+=$this->r0;

		// Radius growth is how much the radius grows between t1 and t2
		$radius_growth=(($this->t2-$this->t1)/(2*pi())) * $this->width;

		$result=array();
		array_push($result, Point::from_polar($base_radius,$this->t1));
		array_push($result, Point::from_polar($base_radius+$this->width,$this->t1));
		array_push($result, Point::from_polar($base_radius+$this->width+$radius_growth,$this->t2));
		array_push($result, Point::from_polar($base_radius+$radius_growth,$this->t2));

		return $result;
	}



}



?>