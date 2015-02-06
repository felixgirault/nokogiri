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
				<span class="a">Text</span>
				<span class="b"> Text </span>
				<span>
					<span>Text</span>
					<span>Text</span>
				</span>
			</p>
XML;

		$expected = <<<XML
			<p>
				<span class="a">Text</span>
				<span class="b"> Text </span>
				<span>
					<span>Te</span></span></p>
XML;

		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 10)
		);
	}

	/**
	 *
	 */
	public function testCutWithoutTrimming() {
		$xml = '<p> <span> Text</span></p>';
		$expected = '<p> <span> Te</span></p>';

		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 4, false)
		);
	}

	/**
	 *
	 */
	public function testCutWithTextNode() {
		$xml = '<p>This <em>is</em> a text node</p>';
		$expected = '<p>This <em>is</em> a</p>';

		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 8)
		);
	}

	/**
	 *
	 */
	public function testCutWithAutoClosingTags() {
		$xml = '<p>Lorem<br /> ipsum dolor sit amet</p>';
		$expected = '<p>Lorem<br /> ipsum</p>';

		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 11)
		);

		$xml = '<p>Lorem<br/> ipsum dolor sit amet</p>';
		$expected = '<p>Lorem<br/> ipsum</p>';

		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 11)
		);
	}
}
