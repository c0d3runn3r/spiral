<?php
/**
 * Point
 *
 * Defines a point in cartesian coordinates
 * @author c0d3runn3r
 */
class Point {

	public $x;
	public $y;

	public function __construct($x=0, $y=0) {

		$this->x=$x;
		$this->y=$y;
	}

	/**
	 * Set value using polar coordinates
	 *
	 * @param rho radius
	 * @param theta angle (radians)
	 */
	public function set_polar($rho, $theta){

		$this->x=$rho*cos($theta);
		$this->y=$rho*sin($theta);
	}

	/**
	 * Create a new Point value using polar coordinates
	 *
	 * @param rho radius
	 * @param theta angle (radians)
	 * @return Point
	 */
	public static function from_polar($rho, $theta){

		$p=new Point();
		$p->set_polar($rho, $theta);
		return $p;
	}

	/**
	 * Convert to polar coordinates
	 *
	 * @return array(rho, theta)
	 */
	public function get_polar(){

		$rho=sqrt($this->x*$this->x + $this->y*$this->y);
		$theta=atan2($this->y, $this->x);

		// atan2 returns negative values for input in the II, III quadrants
		if($theta < 0){

			$theta+=(2*pi());
		}

		return array($rho, $theta);
	}

	/**
	 * Bounding box
	 * 
	 * @param an array of Point objects
	 * @return array(Point, Point) defining the top left and bottom right bounds
	 */
	public static function bounding_box($points) {

		$max_x=NULL;
		$min_x=NULL;
		$max_y=NULL;
		$min_y=NULL;


		if(!is_array($points)) {

			throw new InvalidArgumentException("Parameter must be of array type");
		}

		if(count($points)<1) {

			throw new InvalidArgumentException("Array must contain at least one value");
		}

		foreach ($points as $p) {

			if(get_class($p) != "Point") {

				throw new InvalidArgumentException("All objects in array must be of type Point");
			}

			if(is_null($max_x) || $max_x < $p->x) {

				$max_x=$p->x;
			}

			if(is_null($min_x) || $min_x > $p->x) {

				$min_x=$p->x;
			}
			if(is_null($max_y) || $max_y < $p->y) {

				$max_y=$p->y;
			}

			if(is_null($min_y) || $min_y > $p->y) {

				$min_y=$p->y;
			}
		}

		return array(new Point($min_x,$max_y),new Point($max_x, $min_y));

	}

}



?>