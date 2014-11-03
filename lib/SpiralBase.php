<?php 

/**
 * SpiralBase
 * 
 * Base class for spiral classes
 * @author c0d3runn3r
 */
class SpiralBase {
	
	protected $i;
	protected $t1;
	protected $t2;
	protected $width;
	protected $r0;

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

		// Width must be > 0
		if($width <= 0) {
			throw new InvalidArgumentException("Width must be > 0");
		}

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
	protected function shared_quadrant($a, $b){
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
	protected function quadrants($a){

		// Floating point fudge
		static $nil=.0000001;

		// Make positive
		while($a<0) {
			$a+=(2*pi());
		}

		// Mod 2PI
		$a=fmod($a,(2*pi()));
		
		if(abs($a) < $nil) {
			return array(1,2);
		}
		if(abs($a-(pi()/2)) < $nil) {
			return array(1,4);
		}
		if($a < (pi()/2+$nil)) {
			return array(1);
		}
		if(abs($a-pi())<$nil) {
			return array(3,4);
		}
		if($a < pi()+$nil) {
			return array(4);
		}
		if(abs($a-(3*pi()/2))<$nil) {
			return array(2,3);
		}
		if($a < (3* pi()/2 + $nil)) {
			return array(3);
		}
		if($a < (2*pi()+$nil)) {
			return array(2);
		}
	}

}

?>