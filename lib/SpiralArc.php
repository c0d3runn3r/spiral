
<?php

/**
 * SpiralArc
 * 
 * Represent a segment of a spiral
 * @author c0d3runn3r
 */
class SpiralArc {
	

	private $i;
	private $t1;
	private $t2;
	private $width;
	private $r0;


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

		$this->i=$i;
		$this->t1=$t1;
		$this->t2=$t2;
		$this->width=$width;
		$this->r0=$r0;


		// Theta must be increasing and > 0
		if($t2<$t1 || $t2<0 || $t1 <0) {

			throw new InvalidArgumentException("Spiral must be clockwise monotonic increasing (0 <= t1 <= t2)");
		}

		// Both thetas must be in same quadrant
		if(!$this->shared_quadrant($t1, $t2)) {

			throw new InvalidArgumentException("t1 and t2 must be in the same (I,II,III,IV) quadrant");			
		}


	}

	public function __destruct() {



	}

	/**
	 * Draw the arc
	 *
	 * @param $color
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

		// Iterate over the bounding box
		for($y=floor($bounds[1]->y); $y<ceil($bounds[0]->y); $y++){
	
			for($x=floor($bounds[0]->x); $x<ceil($bounds[1]->x); $x++){

				// Bounding box sets all pixels
				if($bounding_box) {

					imagesetpixel($this->i, $offset_x+$x, $offset_y-$y, $colors[0]);

				// Otherwise, we do a hit test
				} else if ($distance = $this->hit_test(new Point($x, $y))) {

					// Pick a color based on hit distance (default to solid)
					$color=$colors[0];	

					if($distance <1){

						$color=$colors[floor(127 * (1-$distance))];
					}

					imagesetpixel($this->i, $offset_x+$x, $offset_y-$y, $color);
				}
			}
		}

		// Deallocate colors
		foreach ($colors as $color) {

			imagecolordeallocate($this->i, $color);
		}


	}

	/**
	 * Does a point lie within our arc?
	 *
	 * @param Point to test
	 * @return NULL if point lies outside the arc, or numerical distance from nearest (inner, outer) border
	 */
	public function hit_test($point){

		// Convert point to polar
		$polar=$point->get_polar();
		$rho=$polar[0];
		$theta=$polar[1];

		// Get our theta values mod 2PI
		$t1=fmod($this->t1, (2*pi()));
		$t2=fmod($this->t2 , (2*pi()));

		// Is this theta between our theta values?
		if($theta > $t2 || $theta < $t1) {

			return NULL;
		}

		// Every time we make 2PI revolutions, inner radius increases by $width
		$base_radius=$this->width * floor($this->t1/(2*pi()));

		// Base radius starts at r0
		$base_radius+=$this->r0;

		// Radius growth is how much the radius would grow between t1 and theta
		$radius_growth=(($theta-$this->t1)/(2*pi())) * $this->width;

		$distance_from_inner_border=$rho - ($base_radius+$radius_growth);
		$distance_from_outer_border=($base_radius+$radius_growth+$this->width)-$rho;

		if($distance_from_outer_border < 0 || $distance_from_inner_border < 0) {

			return NULL;
		}

		return min($distance_from_inner_border, $distance_from_outer_border);
	}

	/**
	 * Corners
	 *
	 * Return the four corners of this arc segment
	 * @return array() containing four Points
	 */
	private function corners() {

		// Every time we make 2PI revolutions, inner radius increases by $width
		$base_radius=$this->width * floor($this->t1/(2*pi()));

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


	/**
	 * Shared quadrant
	 *
	 * Are two angles in the same quadrant?
	 *
	 * @arg $a the first angle (radians)
	 * @arg $b the second angle (radians)
	 * @returns true if they are in the same (I,II,III,IV) quadrant
	 */
	private function shared_quadrant($a, $b){

		$q1=$this->quadrants($a);
		$q2=$this->quadrants($b);

		foreach ($q1 as $quadrant_of_a){

			if(in_array($quadrant_of_a, $q2)) {

				return true;
			}
		}

		return false;
	}

	/**
	 * Quadrants
	 *
	 * Which quadrant(s) does an angle inhabit?
	 *
	 * @arg $a the angle (radians)
	 * @returns array of quadrants that this angle might inhabit (PI/2 would return (1,2) since it is in I,II quadrants)
	 */
	private function quadrants($a){

		// Make positive
		while($a<0) {

			$a+=(2*pi());
		}

		// Mod 2PI
		$a=fmod($a,(2*pi()));

		if($a==0) {

			return array(1,2);
		}

		if($a < (pi()/2)) {

			return array(1);
		}

		if($a==(pi()/2)) {

			return array(1,4);
		}

		if($a < pi()) {

			return array(4);
		}

		if($a==pi()) {

			return array(3,4);
		}

		if($a < (3* pi()/2)) {

			return array(3);
		}

		if($a==(3*pi()/2)) {

			return array(2,3);
		}

		if($a < (2*pi())) {

			return array(2);
		}

	}


}



?>