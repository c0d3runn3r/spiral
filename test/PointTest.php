<?php


class PointTest extends PHPUnit_Framework_TestCase {


	public function testValues (){

		$p=new Point(1,2);

		$this->assertEquals(1,$p->x);
		$this->assertEquals(2,$p->y);
	}

	public function testSetPolar() {

		// Test polar input, using the pythagorean theorem
		$p=new Point();
		$p->set_polar(sqrt(2),pi()/4);
		$this->assertLessThanOrEqual(.000001, abs(1 - $p->x));
		$this->assertLessThanOrEqual(.000001, abs(1 - $p->y));
	}

	public function testToPolar() {

		// Test polar input, using the pythagorean theorem
		$p=new Point();
		$p->set_polar(2,pi()/4);

		// Convert back to polar
		$polar=$p->get_polar();
		$this->assertLessThanOrEqual(.000001, abs( 2 - $polar[0]));
		$this->assertLessThanOrEqual(.000001, abs( pi()/4 - $polar[1]));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testBoundingBoxNeedsArray() {

		$result=Point::bounding_box(new Point(1,1));
	}


	public function testBoundingBox() {

		// Get a bounding box from some points
		$result=Point::bounding_box(array(new Point(1,1), new Point(-1,-1), new Point(2,0)));

		// Get topleft, bottom right
		$this->assertEquals(2,count($result));
		$topleft=$result[0];
		$bottomright=$result[1];

		// Check
		$this->assertEquals(2, $bottomright->x);
		$this->assertEquals(-1, $bottomright->y);
		$this->assertEquals(-1, $topleft->x);
		$this->assertEquals(1, $topleft->y);

	}

}


?>