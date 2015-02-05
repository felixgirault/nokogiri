<?php

use PHPUnit_Framework_TestCase as TestCase;



/**
 *
 */
class NokogiriTest extends TestCase {

	//
	public $Nokogiri = null;

	/**
	 *
	 */
	public function setUp() {
		$this->Nokogiri = new Nokogiri\Nokogiri();
	}

	/**
	 *
	 */
	public function testCut() {
		$xml = <<<XML
			<p>
				<span class="a">First</span>
				<span class="b">First</span>
				<span>
					<span>First</span>
					<span>First</span>
				</span>
			</p>
XML;

		$expected = <<<XML
			<p>
				<span class="a">First</span>
				<span class="b">First</span>
				<span>
					<span>Fi</span></span></p>
XML;

		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 12)
		);
	}
}
