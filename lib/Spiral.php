<?php

/**
 * Spiral
 *
 * Draw a spiral
 */
class Spiral extends SpiralBase {


	private $segments;	// Array of arc segments

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


		// Get the start of the first segment
		$start=$t1;

		// Get the largest angle ($end) that fits within the same quadrant
		$quadrants=1+floor($t1/(pi()/2));
		$end=$quadrants*(pi()/2);

		// Iterate through each quadrant, creating arcs
		$this->segments=array();
		while($end <= $t2) {

			$complete_revolutions=floor($start/(2*pi()));

			$mod_start=fmod($start, 2*pi());
			$mod_end=$mod_start+($end-$start);

			array_push($this->segments, new SpiralArc($i, $mod_start, $mod_end, $width, $r0 + ($width*$complete_revolutions) ));
			$quadrants++;
			$start=$end;
			$end=$quadrants*(pi()/2);
		}

		// Create a final arc, for the last segment
//		if($t2 > $end){
		$complete_revolutions=floor($start/(2*pi()));
		$mod_start=fmod($start, 2*pi());
		$mod_end=$mod_start+($t2-$start);
		array_push($this->segments, new SpiralArc($i, $mod_start, $mod_end, $width, $r0+ ($width*$complete_revolutions) ));			
//		}

	}

	/**
	 * Draw the arc by iterating all segments
	 *
	 * @param $red [0..255]
	 * @param $green [0..255]
	 * @param $blue [0..255]
	 */
	public function draw($red=0, $green=255, $blue=0) {


		foreach ($this->segments as $seg) {

			$seg->draw($red, $green, $blue);

		}

	}
}

?>