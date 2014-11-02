<?php


class SpiralArcTest extends PHPUnit_Framework_TestCase {


	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testDecreasingTheta (){

		$s=new SpiralArc(NULL, .2, .1, 5, 5);

	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testNegativeTheta (){

		$s=new SpiralArc(NULL, -1, 1, 5, 5);

	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testArcAcrossI_IIQuadrants (){

		$s=new SpiralArc(NULL, 0.25*pi(), 0.75*pi(), 5, 5);

	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testArcAcrossII_IIIQuadrants (){

		$s=new SpiralArc(NULL, pi(), 1.75*pi(), 5, 5);

	}

	public function testNormalUse (){

		$s=new SpiralArc(NULL, .1, .2, 5, 5);
		$this->assertNotNull($s);
	}


}



?>